<?php

use App\Enums\CommonEnum;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Enums\RouteEnum;
use App\Helpers\Elasticsearch;
use App\Http\Controllers\AdministritiveController;
use App\Http\Controllers\CacheController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ChannelSubscriptionController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\ThumbnailController;
use App\Http\Controllers\UploadVideoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\VoteController;
use App\Models\Subscription;
use Illuminate\Routing\PendingResourceRegistration;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('{slug?}', [HomeController::class, 'index'])->where('slug', '(home|/)')->name(RouteEnum::HOME);
Route::get('search', [HomeController::class, 'search'])->name('search');

Auth::routes();


/*
check goes to auth group
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');
*/

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/profile', function () {
    // Only verified users may access this route...
})->middleware('verified');


Route::middleware(['auth'])->group( function(){
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');
});

Route::get('/user/{user}', [ChannelSubscriptionController::class, 'user' ])->name(RouteEnum::USERS_SHOW);

Route::resource('channels',ChannelController::class);    
Route::resource('channels', ChannelController::class)->names([
    'show' => RouteEnum::CHANNELS_SHOW,
    'update' => RouteEnum::CHANNEL_UPDATE
]);

Route::get('users/myaccount', [UserController::class, 'myaccount'])->name(RouteEnum::MYACCOUNT_SHOW);


Route::get('videos/{video}', [VideoController::class, 'show'])->name(RouteEnum::VIDEOS_SHOW);
Route::put('videos/{video}', [VideoController::class, 'updateViews']);
Route::get('videos/{video}/comments', [CommentController::class, 'index']);
Route::get('comments/{comment}/replies', [CommentController::class, 'show']);


Route::middleware(['verified'])->group( function(){
    Route::middleware(['auth'])->group( function(){
        Route::put('videos/{video}/update', [VideoController::class, 'update'])->name(RouteEnum::VIDEOS_UPDATE)->middleware(['permission:' . PermissionEnum::VIDEOS_UPDATE]);
        // Route::post('videos/{video}/object_tags', [UploadVideoController::class, 'get_ml_tags'])->name(RouteEnum::VIDEOS_GET_OBJECT_TAGS)->middleware(['permission:' . PermissionEnum::VIDEOS_GET_OBJECT_TAGS]);

        Route::prefix('channels')->group(function(){
            Route::get('/{channel}/videos', [UploadVideoController::class, 'index'])->name(RouteEnum::CHANNEL_VIDEOS_UPLOAD)->middleware(['permission:' . PermissionEnum::CHANNEL_VIDEOS_UPLOAD]);
            Route::get('/{channel}/videos/upload', [UploadVideoController::class, 'index'])
                ->name(RouteEnum::CHANNEL_VIDEOS_UPLOAD)
                ->middleware(['permission:' . PermissionEnum::CHANNEL_VIDEOS_UPLOAD]);
            Route::post('/{channel}/videos/upload', [UploadVideoController::class, 'store'])
                ->name(RouteEnum::CHANNEL_VIDEOS_UPLOAD)
                ->middleware(['permission:' . PermissionEnum::CHANNEL_VIDEOS_UPLOAD]);
            Route::get('/{channel}/subscribers', [ChannelSubscriptionController::class, 'listChannelSubscribers'])
                ->name(RouteEnum::CHANNEL_SUBSCRIBERS)
                ->middleware(['permission:' . PermissionEnum::CHANNEL_SUBSCRIBERS]);
            
            
            Route::resource('{channel}/subscriptions', ChannelSubscriptionController::class)
                ->only(['store', 'destroy'])
                ->names([
                    'store' => RouteEnum::CHANNEL_SUBSCRIPTIONS_STORE,
                    'destroy' => RouteEnum::CHANNEL_SUBSCRIPTIONS_DESTROY
                ])
                ->mapResourcePermissions([
                    'store' => 'permission:'.PermissionEnum::CHANNEL_SUBSCRIPTIONS_STORE,
                    'destroy' => 'permission:'.PermissionEnum::CHANNEL_SUBSCRIPTIONS_DESTROY
                ]);
        });

        
        /// 
        
        Route::post('comments/{video}', [CommentController::class, 'store'])->name(RouteEnum::COMMENTS_STORE)->middleware(['permission:' . PermissionEnum::COMMENTS_STORE]);
        Route::post('votes/{type}', [VoteController::class, 'vote' ])->name(RouteEnum::VOTES)->middleware(['permission:' . PermissionEnum::VOTES]);
        
        Route::get('/user/{user}/subscriptions', [ChannelSubscriptionController::class, 'listSubscriptions' ])->name(RouteEnum::USER_CHANNEL_SUBSCRIPTIONS)->middleware(['permission:' . PermissionEnum::USER_CHANNEL_SUBSCRIPTIONS]);
    });
    
});

Route::post('/update-theme', [ThemeController::class, 'update'])->name(RouteEnum::THEME_UPDATE);


Route::prefix(CommonEnum::ADMIN)->middleware(['role:' . RoleEnum::ADMIN])->group(function () {
    
    Route::prefix(CommonEnum::MANAGE_SITE)->group(function () {

        Route::prefix('data')->group(function () {

            // Admin site management dashboard
            Route::get('/', [AdministritiveController::class, 'index'])->name(RouteEnum::ADMIN_MANAGE_SITE);
    
            // Clear all site-related caches, sessions, cookies
            Route::get('/clear-all', [AdministritiveController::class, 'clearAll'])->name(RouteEnum::ADMIN_MANAGE_SITE_CLEAR_ALL);
            Route::get('/clear-all-sessions', [AdministritiveController::class, 'clearAllSessions'])->name(RouteEnum::ADMIN_MANAGE_SITE_CLEAR_ALL_SESSIONS);
            Route::get('/clear-all-cookies', [AdministritiveController::class, 'clearAllCookies'])->name(RouteEnum::ADMIN_MANAGE_SITE_CLEAR_ALL_COOKIES);
            Route::get('/clear-all-caches', [AdministritiveController::class, 'clearAllCaches'])->name(RouteEnum::ADMIN_MANAGE_SITE_CLEAR_ALL_CACHES);
    
            // Clear personal session, cookies, and caches for current user
            Route::get('/clear-personal-session', [AdministritiveController::class, 'clearPersonalSession'])->name(RouteEnum::ADMIN_MANAGE_SITE_CLEAR_PERSONAL_SESSION);
            Route::get('/clear-personal-cookies', [AdministritiveController::class, 'clearPersonalCookies'])->name(RouteEnum::ADMIN_MANAGE_SITE_CLEAR_PERSONAL_COOKIES);
            Route::get('/clear-personal-cache', [AdministritiveController::class, 'clearPersonalCaches'])->name(RouteEnum::ADMIN_MANAGE_SITE_CLEAR_PERSONAL_CACHE);
        });

        Route::prefix('push-notification')->group(function () {
            Route::get('/', [PushNotificationController::class, 'index'])->name(RouteEnum::ADMIN_MANAGE_SITE_PUSH_NOTIFICATION);
            Route::get('/create', [PushNotificationController::class, 'create'])->name('push-notifications.create');
            Route::post('/store', [PushNotificationController::class, 'store'])->name('push-notifications.store');
            Route::post('/send/{pushNotification}', [PushNotificationController::class, 'send'])->name('push-notifications.send');
            Route::post('/toggle', [PushNotificationController::class, 'togglePushNotifications'])->name('push-notifications.toggle');

        });
    });
});

// check the security
Route::get('thumbnails/{filename}', [ThumbnailController::class, 'show'])->name('thumbnails.show');


Route::post('save-push-notification-sub',[PushNotificationController::class,'saveSubscriptionToTopic']);

// In your routes/web.php
Route::get('/log-test', function () {
    // Log::channel('elasticsearch')->info('Test log from Laravel!');
    // Log::channel('elasticsearch')->info('Sending Push Notification', [
    //     'index' =>  config('elasticsearch.indices.push_notifications'),
    //     'notification' => [],
    //     'response' => [],
    // ]);
    
    $indexPattern = config('elasticsearch.indices.push_notifications') . '-*';
    $patternId = config('elasticsearch.indices.push_notifications') . '-pattern';
    (new Elasticsearch())->createKibanaIndexPattern($indexPattern, $patternId); // ok

    
    // $today = now()->format('Y-m-d');
    // $indexName = 'push-notification-logs-2024-10-12';

    // dd((new Elasticsearch())->isClusterHealthy());
    // (new Elasticsearch())->createLogsIndex($indexName);

    return 'Log sent to Elasticsearch!';
});

Route::get('/test-elasticsearch', function () {
    $client = \Elastic\Elasticsearch\ClientBuilder::create()
        ->setHosts([env('ELASTICSEARCH_HOST', 'elasticsearch:9200')])
        ->build();

    return $client->info();
});
