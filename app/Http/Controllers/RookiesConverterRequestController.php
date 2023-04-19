<?php

namespace App\Http\Controllers;

use App\Models\RookiesConverterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RookiesConverterRequestController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => ['required', 'string', 'min:2', 'max:150']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = $request->user();

        $existing_converter_request = RookiesConverterRequest::where('rookie_id', $user->id)->first();
        if (isset($existing_converter_request)){
            return response()->json(['message' => 'Only one converter request is allowed'], 400);
        }

        $rookies_converter_request = RookiesConverterRequest::create([
            'message' =>  $request->message,
            'rookie_id' => $user->id,
        ]);

        return response()->json($rookies_converter_request, 201);
    }

    public function update(Request $request, RookiesConverterRequest $rookiesConverterRequest)
    {
        $validator = Validator::make($request->all(), [
            'message' => ['required', 'string', 'min:2', 'max:150']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = $request->user();

        if ($user->id !== $rookiesConverterRequest->rookie_id) {
            return response()->json(['message' => 'You not allowed to perform this Action'], 403);
        }

        $rookiesConverterRequest->update([
            'message' =>  $request->message,
        ]);

        return response()->json($rookiesConverterRequest);
    }
}
