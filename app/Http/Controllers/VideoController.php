<?php

namespace App\Http\Controllers;

use App\Http\Resources\VideoHistoriesResource;
use App\Http\Resources\VideoResource;
use App\Models\Rookie;
use App\Models\Video;
use App\Models\VideoHistory;
use App\Utils\StorageUtils;
use getID3;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    public function store(Request $request)
    {
        if(!$request->has('video')){
            return response()->json(['message' => "You must upload the video", 'type' => 'invalid_video'], 400);
        }

        if(!$request->file('video')->isValid()){
            return response()->json(['message' => trans('video.invalid_video'), 'error' => $request->file('video')->getErrorMessage(), 'type' => 'invalid_video'], 400);
        }

        $getID3 = new getID3();
        $file = $getID3->analyze($request->file('video'));

        if(isset($file['playtime_seconds']) && round($file['playtime_seconds'])>env('MAX_VIDEO_DURATION_IN_SECONDS')){
            return response()->json(['message' => trans('video.invalid_video_duration'), 'type' => 'invalid_duration'], 400);
        }

        $validator = Validator::make($request->all(), [
            'video' => ['required', 'mimes:mp4,mov,qt'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //Division by 1000000 to get size in MB
        $video_size_in_mb = round($request->file('video')->getSize()/1000000, 2);

        $max_value = env('MAX_VIDEO_SIZE_IN_MB', 80);
        if($video_size_in_mb > $max_value){
            return response()->json(['message' => "Invalid video size, max size allowed: " . $max_value, 'type' => 'invalid_size'], 400);
        }

        $response = StorageUtils::storeObject($request->video, 'video');

        if($response['status']==='error'){
            return response()->json(['message' => $response['message'], 'type' => $response['type']], 400);
        }

        return response()->json(['path_location' => $response['path_location'], 'url' => StorageUtils::signUrl($response['path_location'])], 201);
    }

    public function assign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'path_location' => ['required', 'unique:videos,path_location'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $rookie = Rookie::find(Auth::id());

        $rookie->removeVideo();

        $response = StorageUtils::assignObject($request->path_location, 'video', $rookie);

        if($response['status']==='error'){
            return response()->json(['message' => $response['message']], 400);
        }

        $video = $rookie->addVideo($response['path_location']);

        $video = VideoHistory::find($video->id);
        $video = VideoHistoriesResource::compute($request, $video)->first();

        return response()->json($video, 201);
    }
}
