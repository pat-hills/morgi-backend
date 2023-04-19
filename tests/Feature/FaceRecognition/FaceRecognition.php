<?php

use App\FaceRecognition\AwsFaceRekognitionCollectionUtils;
use App\FaceRecognition\AwsFaceRekognitionFacesUtils;
use App\Models\Gender;
use App\Models\User;

test("Test store photo's faces", function () {

    $utils = new AwsFaceRekognitionFacesUtils();
    $unprocessed_photo = \App\Models\Photo::where('is_face_recognition_processed', false)->inRandomOrder()->first();
    if(!isset($unprocessed_photo)){
        return;
    }

    $user = User::query()->where('id', $unprocessed_photo->user_id)->where('has_face_processed', false)->first();
    if(!isset($user)){
        return;
    }

    $user_gender = Gender::find($user->gender_id);
    if(!isset($user_gender)){
        return;
    }

    /*
     * Retrive AWS's collection to upload faces
     */
    $collection_type = "{$user->type}_{$user_gender->key_name}";
    try {
        $collection = (new AwsFaceRekognitionCollectionUtils())->getOrCreateFirstAvailableCollection($collection_type);
    }catch (\Exception $exception){
        throw new \Exception($exception->getMessage());
    }

    try {
        $photo_face_recognition = $utils->storePhotoFaces($unprocessed_photo);
        $unprocessed_photo->refresh();
    }catch (Exception $exception){
        throw new Exception($exception->getMessage());
    }

    expect($unprocessed_photo)->is_face_recognition_processed->toBe(true);

    if(!isset($photo_face_recognition)){
        return;
    }

    expect($photo_face_recognition)
        ->photo_id->toBe($unprocessed_photo->id)
        ->collection_id->toBe($collection->id);

});
