<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\GoalMedia;
use App\Services\Uploader\Uploader;
use App\Utils\StorageUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UploadController extends Controller
{
    public function upload(Request $request){

        $validator = Validator::make($request->all(), [
            'file' => ['required'],
            'filetype' => ['required', Rule::in(GoalMedia::TYPES)]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try{
            $response = Uploader::upload($request->file, $request->filetype);
        } catch (BadRequestHttpException $e) {
            return response()->json(['message' => $e->getMessage(), 400]);
        }

        return response()->json($response);
    }

    public function multiUpload(Request $request){

        $validator = Validator::make($request->all(), [
            'files' => ['required'],
            'filetype' => ['required', Rule::in(GoalMedia::TYPES)]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $files = $request->all()['files'];
        $data = [];
        foreach ($files as $file){
            try{
                $response = Uploader::upload($file, $request->filetype);
                $data[] = $response['media_path'];
            } catch (BadRequestHttpException $e) {
                return response()->json(['message' => $e->getMessage(), 400]);
            }
        }
        return response()->json(['media_path' => $data]);
    }
}