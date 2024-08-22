<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class CacheController extends Controller
{
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
}
