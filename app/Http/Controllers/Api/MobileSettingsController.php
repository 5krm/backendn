<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserSetting;

class MobileSettingsController extends Controller
{
    /**
     * Get user settings
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $settings = UserSetting::firstOrCreate(
            ['user_id' => $user->id],
            ['theme' => 'light', 'language' => 'en', 'notifications_enabled' => true]
        );

        return response()->json(['settings' => $settings]);
    }

    /**
     * Update user settings
     */
    public function update(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'theme' => 'nullable|string',
            'language' => 'nullable|string',
            'notifications_enabled' => 'nullable|boolean',
        ]);

        $settings = UserSetting::firstOrCreate(
            ['user_id' => $user->id],
            ['theme' => 'light', 'language' => 'en', 'notifications_enabled' => true]
        );

        if ($request->has('theme')) $settings->theme = $request->theme;
        if ($request->has('language')) $settings->language = $request->language;
        if ($request->has('notifications_enabled')) $settings->notifications_enabled = $request->notifications_enabled;
        
        $settings->save();

        return response()->json(['settings' => $settings, 'message' => 'Settings updated successfully']);
    }
}
