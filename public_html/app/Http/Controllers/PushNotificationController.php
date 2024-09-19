<?php

namespace App\Http\Controllers;

use App\Enums\RouteEnum;
use App\Helpers\FirebasePushNotification;
use App\Models\PushNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PushNotificationController extends Controller
{
    /**
     * Clear all caches.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $pushNotifications = PushNotification::paginate(10);
        return view('admin.manage-site.push-notification.index' , compact('pushNotifications'));
    }

    public function create(){
        return view('admin.manage-site.push-notification.create');
    }

    public function saveSubscriptionToTopic(Request $request){
        $topic=config('firebase.key')['topic'];
        try{
            $deviceToken = $request->device_token;
            $deviceTokens[]=$deviceToken;
            $firebaseAdmin = new FirebasePushNotification();
            $results = $firebaseAdmin->subscribeToTopic($topic,$deviceTokens);
            return response()->json([
                'status' => true,
                'message' => 'subscribed to topic successfully.',
                'results' => $results
            ]);
        } catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Store a newly created push notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            
            // Validate the input data
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'nullable|string',
                'url' => 'required|url',
                // 'status' => 'required|boolean',
                // 'activate_at' => 'required|date',
                // 'inactivate_at' => 'nullable|date',
                // 'schedule_time' => 'nullable|date_format:H:i',
                // 'thumbnail_desktop' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048', // Max 2MB
                // 'thumbnail_mobile' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',  // Max 2MB
            ]);

            

            // // Handle file uploads
            // if ($request->hasFile('thumbnail_desktop')) {
            //     $desktopThumbnailPath = $request->file('thumbnail_desktop')->store('thumbnails/desktop', 'public');
            // }

            // if ($request->hasFile('thumbnail_mobile')) {
            //     $mobileThumbnailPath = $request->file('thumbnail_mobile')->store('thumbnails/mobile', 'public');
            // }

            // dd($request->all(),[
            //     'title' => $validated['title'],
            //     'message' => $validated['message'],
            //     // 'status' => $validated['status'],
            //     'url' => $validated['url'],
            //     // 'activate_at' => $validated['activate_at'],
            //     // 'inactivate_at' => $validated['inactivate_at'],
            //     // 'schedule_time' => $validated['schedule_time'],
            //     // 'thumbnail_desktop' => $desktopThumbnailPath ?? null,
            //     // 'thumbnail_mobile' => $mobileThumbnailPath ?? null,
            // ]);
            // Create the new push notification
            $pushNotification = PushNotification::create([
                'title' => $validated['title'],
                'message' => $validated['message'],
                // 'status' => $validated['status'],
                'url' => $validated['url'],
                // 'activate_at' => $validated['activate_at'],
                // 'inactivate_at' => $validated['inactivate_at'],
                // 'schedule_time' => $validated['schedule_time'],
                // 'thumbnail_desktop' => $desktopThumbnailPath ?? null,
                // 'thumbnail_mobile' => $mobileThumbnailPath ?? null,
            ]);

            // Redirect to the push notifications list with a success message
            return redirect()->route(RouteEnum::ADMIN_MANAGE_SITE_PUSH_NOTIFICATION)
                            ->with('success', 'Push notification created successfully.');
        } catch(Exception $e){
            dd($e->getMessage());
        }
    }
}
