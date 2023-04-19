<?php

namespace App\Http\Controllers\Admin\Api;

use App\Enums\SystemSettingEnum;
use App\Http\Controllers\Controller;
use App\Models\ActionTracking;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class AdminApiSystemSettingController extends Controller
{
    public function getSystemOrders(): JsonResponse
    {
        $system_settings = [
            SystemSettingEnum::CONVERTERS_CAROUSEL_ORDER_RANDOMLY,
            SystemSettingEnum::CONVERTERS_CAROUSEL_ORDER_CUSTOM
        ];
        return response()->json($system_settings);
    }

    public function getCurrentSystemOrder(): JsonResponse
    {
        $system_setting = SystemSetting::query()->first();
        return response()->json($system_setting);
    }

    public function updateCurrentSystemOrder(Request $request, SystemSetting $system_setting): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'converters_carousel_order' => ['required', 'string', Rule::in(SystemSettingEnum::CONVERTERS_CAROUSEL_ORDERS_AVAILABLE)],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $system_setting->update([
            'converters_carousel_order' => $request->converters_carousel_order
        ]);

        return response()->json($system_setting);
    }
}
