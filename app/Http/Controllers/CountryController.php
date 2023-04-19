<?php

namespace App\Http\Controllers;

use App\Http\Resources\CountryResource;
use App\Models\Country;
use App\Utils\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $countries = Cache::tags('country')->get("countries:{$request->response_type}");
        if (isset($countries)) {
            return response()->json($countries);
        }

        $countries = Country::query()
            ->select('id', 'has_childs', 'name')
            ->whereNotIn('alpha_3_code', ['USA', 'AND', 'ISR'])
            ->get();

        $country_usa = Country::query()
            ->where('alpha_3_code', 'USA')
            ->first();

        //Setting USA to the first position in response
        $countries->splice(0, 0, [$country_usa]);
        $countries = CountryResource::compute($request, $countries)->get();

        Cache::tags('country')->put("countries:{$request->response_type}", $countries, 86400);

        return response()->json($countries);
    }

    public function localize(Request $request)
    {
        $country_name = Utils::ipInfo(
            Utils::getRealIp($request)
        );

        $country = CountryResource::compute($request, Country::query()->where('name', $country_name)->first())->first();

        return response()->json($country);
    }
}
