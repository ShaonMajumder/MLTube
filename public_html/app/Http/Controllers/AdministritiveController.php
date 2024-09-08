<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AdministritiveController extends Controller
{
    /**
     * Clear all caches.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return view('admin.index');
    }

    /**
     * Clear all caches.
     *
     * @return \Illuminate\Http\Response
     */
    public function clearAll()
    {
        // Call Artisan commands
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('event:clear');
        Artisan::call('optimize:clear');

        return response()->json([
            'message' => 'All caches have been cleared successfully!',
        ]);
    }

    public function clearPersonalCookies() {
        foreach (Cookie::get() as $cookieName => $cookieValue) {
            Cookie::queue(Cookie::forget($cookieName));
        }
        return response()->json(['message' => 'Personal cookies cleared successfully.']);
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
