<?php

namespace App\Http\Controllers;

use App\Http\Resources\PhotoHistoryResource;
use App\Http\Resources\PhotoResource;
use App\Logger\Logger;
use App\Models\Photo;
use App\Models\PhotoHistory;
use App\Utils\StorageUtils;
use App\Utils\Upload\UploadUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class PhotoController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $photos = Photo::query()->where('user_id', $user->id)->get();

        $photos_validation = PhotoHistory::query()
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['approved', 'declined'])
            ->get();

        $photos = PhotoResource::compute($request, $photos)->get();
        $photos_validation = PhotoHistoryResource::compute($request, $photos_validation)->get();

        $response = $photos->merge($photos_validation)->take(10);

        return response()->json($response);
    }

    public function store(Request $request)
    {
        if(!($request->hasFile('photo') && $request->file('photo')->isValid())) {
            return response()->json(['message' => trans('photo.invalid_image')], 400);
        }

        $validator = Validator::make($request->all(), [
            'photo' => ['required', 'mimes:jpeg,jpg,png,heif,heic'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $response = UploadUtils::upload($request->photo, UploadUtils::TYPE_PHOTO);
        } catch (BadRequestException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['path_location' => $response['path_location'], 'url' => StorageUtils::signUrl($response['path_location'])], 201);
    }


    public function assignPhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'path_location' => ['required', 'unique:photos,path_location']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = $request->user();

        $response = StorageUtils::assignObject($request->path_location, 'photo', $user);
        if($response['status']==='error'){
            return response()->json(['message' => $response['message']], 400);
        }

        try {
            $photo = $user->addPhoto($response['path_location']);
        }catch (\Exception $exception){
            Logger::logException($exception);
            return response()->json(['message' => $exception->getMessage()], 400);
        }

        $photo = PhotoHistoryResource::compute($request, $photo)->first();

        return response()->json($photo);
    }

    public function deletePhoto(Request $request, Photo $photo)
    {
        $user = $request->user();
        if($user->id !== $photo->user_id){
            return response()->json(['message' => "You can delete only yours photos"], 403);
        }

        try {
            $photo->delete();
        }catch (\Exception $exception){
            Logger::logException($exception);
            return response()->json(['message' => "Error during photo's delete"], 500);
        }

        return response()->json([]);
    }

    public function deleteValidationPhoto(Request $request, PhotoHistory $photoHistory)
    {
        $user = $request->user();
        if($user->id !== $photoHistory->user_id){
            return response()->json(['message' => "You can delete only yours photos"], 403);
        }

        try {
            $photoHistory->delete();
        }catch (\Exception $exception){
            Logger::logException($exception);
            return response()->json(['message' => "Error during photo's delete"], 500);
        }

        return response()->json([]);
    }
}
