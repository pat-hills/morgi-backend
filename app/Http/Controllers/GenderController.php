<?php

namespace App\Http\Controllers;

use App\Http\Resources\GenderResource;
use App\Models\Gender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GenderController extends Controller
{
    public function index(Request $request)
    {
        $cache_reference = (isset($request->is_leader) && $request->is_leader)
            ? "genders:leader"
            : "genders:rookie";

        $cache_reference .= ":{$request->response_type}";

        $genders = Cache::get($cache_reference);
        if (isset($genders)) {
            return response()->json($genders);
        }

        $genders = Gender::query()->where('key_name', '!=', 'unknown')->orderBy('key_name');
        if(isset($request->is_leader) && $request->is_leader){
            $genders = GenderResource::compute($request, $genders->get())->get();
            Cache::put($cache_reference, $genders, 86400);
            return response()->json($genders);
        }

        $genders = GenderResource::compute(
            $request,
            $genders->where('key_name', '!=', 'all')->get()
        )->get();

        Cache::put($cache_reference, $genders, 86400);

        return response()->json($genders);
    }
}
