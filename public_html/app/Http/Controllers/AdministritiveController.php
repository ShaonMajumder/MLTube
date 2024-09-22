<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class AdministritiveController extends Controller
{
    /**
     * Clear all caches.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return view('admin.manage-site.data.index');
    }

    /**
     * Clear all caches.
     *
     * @return \Illuminate\Http\Response
     */
    public function clearAll()
    {
        try{
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('event:clear');
            Artisan::call('optimize:clear');
    
            return back()->with(['success' => 'All have been cleared successfully.']);
        } catch (\Exception $e) {
            return back()->with(['error' => 'An error occurred while deleting all.']);
        }
        
    }

    public function clearPersonalCookies() {
        $sessionCookieName = config('session.cookie'); // This is the session cookie name, default is 'laravel_session'
        // dd(Cookie::get() );
        try {
            foreach (Cookie::get() as $cookieName => $cookieValue) {
                // if ($cookieName !== $sessionCookieName) {
                    // Forget cookies with domain and path
                    Cookie::queue(Cookie::forget($cookieName, '/', null));
                // }
            }
            // return back()->with(['success' => 'Personal cookies cleared successfully except session cookie.']);
        } catch (\Exception $e) {
            return back()->with(['error' => 'An error occurred while deleting personal cookies.']);
        }
    }
    

    public function clearAllSessions()
    {
        $sessionPath = storage_path('framework/sessions');

        try {
            if (File::exists($sessionPath)) {
                $files = File::files($sessionPath);
                foreach ($files as $file) {
                    File::delete($file);
                }

                return back()->with(['success' => 'All session files deleted successfully.']);
            } else {
                return back()->with(['message' => 'Session directory does not exist.'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error clearing session files: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while deleting session files.'], 500);
        }
    }

    public function clearAllCaches(){
        
        try {
            Artisan::call('cache:clear');
            Redis::flushdb();
            // Redis::flushall();
            return back()->with(['success' => 'Application caches deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Error clearing session files: ' . $e->getMessage());
            return back()->with(['error' => 'An error occurred while deleting Application caches.']);
        }
    }

    public function clearPersonalSession()
    {
        $sessionId = Session::getId();
        $sessionPath = storage_path('framework/sessions');

        try {
            if (File::exists($sessionPath)) {
                $sessionFile = $sessionPath . '/' . $sessionId;
                if (File::exists($sessionFile)) {
                    File::delete($sessionFile);
                    return response()->json(['message' => 'Current user session file deleted successfully.']);
                } else {
                    return response()->json(['message' => 'Session file not found.'], 404);
                }
            } else {
                return response()->json(['message' => 'Session directory does not exist.'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting session file: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while deleting the session file.'], 500);
        }
    }
}
