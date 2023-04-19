<?php

namespace App\Http\Controllers;

use App\Http\Resources\RegionResource;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class RegionController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => ['sometimes', 'exists:countries,id']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $cache_reference = "regions_country:{$request->response_type}";

        if (isset($request->country_id)) {
            $cache_reference .= ":country_id:{$request->country_id}";
        }

        $regions = Cache::get($cache_reference);
        if (isset($regions)) {
            return response()->json($regions);
        }

        $regions = Region::query()->when($request->country_id, function ($query, $country_id){
            $query->where('country_id', $country_id);
        })->get();

        $regions = RegionResource::compute($request, $regions)->get();

        Cache::put($cache_reference, $regions, 86400);

        return response()->json($regions);
    }
}
