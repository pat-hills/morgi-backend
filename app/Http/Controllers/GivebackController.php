<?php

namespace App\Http\Controllers;

use App\Http\Resources\GivebackResource;
use App\Models\Giveback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GivebackController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $givebacks = Cache::get("givebacks:{$request->response_type}");
        if(isset($givebacks)) {
            return response()->json($givebacks);
        }

        $givebacks = GivebackResource::compute(
            $request,
            Giveback::query()->get()
        )->get();

        Cache::put("givebacks:{$request->response_type}", $givebacks, 86400);

        return response()->json($givebacks);
    }
}
