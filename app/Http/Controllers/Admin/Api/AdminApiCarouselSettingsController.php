<?php

namespace App\Http\Controllers\Admin\Api;

use App\Enums\CarouselTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\CarouselSettingResource;
use App\Models\CarouselSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminApiCarouselSettingsController extends Controller {

    public function index(Request $request): JsonResponse
    {
        $carousel_settings = CarouselSetting::all();

        $response = CarouselSettingResource::compute($request, $carousel_settings)->get();

        return response()->json($response);
    }

    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'carousel_type' => ['required', 'string', Rule::in(CarouselTypeEnum::TYPES)]
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }

        CarouselSetting::query()
            ->where('type', "!=", $request->carousel_type)
            ->update(['is_active' => false]);

        CarouselSetting::query()
            ->where('type', $request->carousel_type)
            ->update(['is_active' => true]);

        return response()->json([]);
    }
}
