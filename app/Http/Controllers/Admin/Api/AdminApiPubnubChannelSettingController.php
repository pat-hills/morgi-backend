<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\PubnubChannelSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class AdminApiPubnubChannelSettingController extends Controller
{
    public function index(): JsonResponse
    {
        $channels_settings_options = PubnubChannelSetting::all();
        return response()->json($channels_settings_options);
    }

    public function update(Request $request, PubnubChannelSetting $pubnub_channel_setting): JsonResponse
    {
        if($pubnub_channel_setting->is_active){
            return response()->json(['message' => 'Channel Setting is already active'], 400);
        }

        PubnubChannelSetting::query()
            ->where('is_active', true)
            ->update([
                'is_active' => false
            ]);

        $pubnub_channel_setting->update([
            'is_active' => true
        ]);

        return response()->json($pubnub_channel_setting);
    }
}
