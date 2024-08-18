<?php

use App\Enums\PermissionEnum;
use App\Enums\RouteEnum;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UploadVideoController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\VoteController;
use Illuminate\Routing\PendingResourceRegistration;

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

Route::get('{slug?}', [HomeController::class, 'index'])->where('slug', '(home|/)')->name('home');
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

Route::resource('channels',ChannelController::class);    
Route::resource('channels', ChannelController::class)->names([
    'show' => RouteEnum::CHANNELS_SHOW,
    'update' => RouteEnum::CHANNELS_UPDATE
]);
    
Route::get('videos/{video}', [VideoController::class, 'show'])->name('videos.show');
Route::put('videos/{video}', [VideoController::class, 'updateViews']);
Route::get('videos/{video}/comments', [CommentController::class, 'index']);
Route::get('comments/{comment}/replies', [CommentController::class, 'show']);


Route::middleware(['verified'])->group( function(){
    Route::middleware(['auth'])->group( function(){
        Route::put('videos/{video}/update', [VideoController::class, 'update'])->name(RouteEnum::VIDEOS_UPDATE)->middleware(['permission:' . PermissionEnum::VIDEOS_UPDATE]);
        // Route::post('videos/{video}/object_tags', [UploadVideoController::class, 'get_ml_tags'])->name(RouteEnum::VIDEOS_GET_OBJECT_TAGS)->middleware(['permission:' . PermissionEnum::VIDEOS_GET_OBJECT_TAGS]);

        Route::prefix('channels')->group(function(){
            Route::get('/{channel}/videos', [UploadVideoController::class, 'index'])->name(RouteEnum::CHANNEL_VIDEOS_UPLOAD)->middleware(['permission:' . PermissionEnum::CHANNEL_VIDEOS_UPLOAD]);
            Route::post('/{channel}/videos/upload', [UploadVideoController::class, 'store'])->middleware(['permission:' . PermissionEnum::CHANNEL_VIDEOS_UPLOAD]);

            
            Route::resource('{channel}/subscriptions', SubscriptionController::class)
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
        
        Route::post('comments/{video}', [CommentController::class, 'store'])->name(RouteEnum::COMMENTS_STORE)->middleware(['permission:' . PermissionEnum::COMMENTS_STORE]);
        Route::post('votes/{type}', [VoteController::class, 'vote' ])->name(RouteEnum::VOTES)->middleware(['permission:' . PermissionEnum::VOTES]);
    });
    
});
