<?php

namespace App\Http\Controllers;

use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => ['sometimes', 'exists:countries,id']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $cache_reference = "cities:{$request->response_type}";
        if (isset($request->country_id)) {
            $cache_reference .= ":country_id:{$request->country_id}";
        }

        $cities = Cache::tags('cities')->get($cache_reference);
        if (isset($cities)) {
            return response()->json($cities);
        }

        $cities = City::query()->when($request->country_id, function ($query, $country_id){
            $query->where('country_id', $country_id);
        })->get();

        $cities = CityResource::compute($request, $cities)->get();

        Cache::tags(['cities'])->put($cache_reference, $cities, 3600);

        return response()->json($cities);
    }
}
