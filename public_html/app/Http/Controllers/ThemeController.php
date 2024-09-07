<?php

namespace App\Http\Controllers;

use App\Enums\CacheEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use App\Models\Settings;

class ThemeController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:light,dark',
        ]);

        $theme = $request->input('theme');

        if (Auth::check()) {
            $userId = Auth::id();
            $cacheThemeKey = CacheEnum::THEME;
            $cacheThemeKey = "{$userId}_{$cacheThemeKey}";

            Redis::set($cacheThemeKey, $theme);
            Settings::updateOrInsert(
                [
                    'user_id' => $userId,
                    'key' => 'theme'
                ],
                ['value' => $theme]
            );
        }

        cookie()->queue(cookie()->forever('guest_theme', $theme));
        session(['theme' => $theme]);

        return response()->json(['success' => true]);
    }

}