<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Rookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;


class AdminApiRookieController extends Controller
{

    public function update(Request $request, Rookie $rookie): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'is_converter' => ['sometimes']
        ]);

        if ($validator->fails()){
            return response()->json(['message' => $validator->fails()], Response::HTTP_BAD_REQUEST);
        }

        $field_to_update = [];

        if($request->has('is_converter') && !empty($request->is_converter)){
            if ($request->boolean('is_converter') !== $rookie->is_converter){
                $field_to_update['is_converter'] = $request->boolean('is_converter');
            }
        }

        if(isset($field_to_update) && !empty($field_to_update)){
            $rookie->update($field_to_update);
        }

        return response()->json($rookie);
    }

    public function getConverters(Request $request): JsonResponse
    {
        $converters = Rookie::query()->select('rookies.*', 'converters_carousel_positions.position as carousel_position')
            ->where('is_converter', true)
            ->leftJoin('converters_carousel_positions', 'rookies.converter_carousel_position_id', '=', 'converters_carousel_positions.id')
            ->get();

        return response()->json($converters);
    }

    public function updateConvertersCarouselPosition(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'converters_positions' => ['required', 'array']
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->fails()], Response::HTTP_BAD_REQUEST);
        }

        $converters_positions = collect($request->converters_positions);

        $users_ids = $converters_positions->pluck('user_id');

        $rookies = Rookie::query()->whereIn('id', $users_ids)->get();

        foreach ($rookies as $rookie) {
            $position = $converters_positions->where('user_id', $rookie->id)->first();
            $rookie->update(['converter_carousel_position_id' => $position['position_id']]);
        }

        return response()->json($rookies);
    }
}
