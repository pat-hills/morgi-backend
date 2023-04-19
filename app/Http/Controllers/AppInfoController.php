<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppInfoController extends Controller
{
    public function version(Request $request)
    {
        return response()->json([
            'version' => env('VERSION'),
            'headers' => $request->headers->all(),
        ]);
    }
}
