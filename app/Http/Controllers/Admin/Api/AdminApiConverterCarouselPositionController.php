<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\ConverterCarouselPosition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


class AdminApiConverterCarouselPositionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $carousel_position_available = ConverterCarouselPosition::all();
        return response()->json($carousel_position_available);
    }

    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'positions' => ['required', 'array'],
            'positions.*.position' => ['required', 'numeric', 'min:0', 'max:49']
        ], [
            'positions.*.position.max' => 'Position(s) value(s) not correct. The carousel start at 0 and end at 49.',
        ]);

        if ($validator->fails()){
            return response()->json(['message' => $validator->errors()->first()], Response::HTTP_BAD_REQUEST);
        }

        $positions = collect($request->positions);

        if($positions->count() !== count(array_unique($positions->pluck('position')->toArray()))){
            return response()->json(['message' => 'There cannot be equal positions'], Response::HTTP_BAD_REQUEST);
        }

        $converters_carousel_positions_ids = $positions->pluck('id');
        $converters_carousel_positions = ConverterCarouselPosition::query()
            ->whereIn('id', $converters_carousel_positions_ids)
            ->get();

        foreach ($converters_carousel_positions as $carousel_position){
            $position = $positions->where('id', $carousel_position->id)->first();
            $carousel_position->update([
                'position' => $position['position']
            ]);
        }

        return response()->json($converters_carousel_positions);
    }
}
