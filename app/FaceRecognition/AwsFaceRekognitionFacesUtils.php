<?php

namespace App\FaceRecognition;

use App\Models\Gender;
use App\Models\Photo;
use App\Models\PhotoFaceRecognition;
use App\Models\User;
use Illuminate\Support\Str;

class AwsFaceRekognitionFacesUtils extends AwsFaceRekognitionClient
{
    public function storePhotoFaces(Photo $photo): ?PhotoFaceRecognition
    {
        /*
         * If photo is already processed, skip
         */
        if($photo->is_face_recognition_processed){
            return null;
        }

        /*
         * If user was already processed, skip
         */
        $user = User::where('id', $photo->user_id)->where('has_face_processed', false)->first();
        if(!isset($user)){
            return null;
        }

        $user_gender = Gender::find($user->gender_id);
        if(!isset($user_gender)){
            throw new \Exception("Unable to retrieve user's gender");
        }

        /*
         * Retrieve AWS's collection to upload faces
         */
        $collection_type = "{$user->type}_{$user_gender->key_name}";
        try {
            $collection = (new AwsFaceRekognitionCollectionUtils())->getOrCreateFirstAvailableCollection($collection_type);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        /*
         * Upload on AWS's collection the photo to detect faces
         */
        $external_image_id = Str::uuid()->toString();
        try {
            $aws_face_recognition = $this->storeAwsPhotoFaces($collection->name, $external_image_id, $photo->path_location);
        }catch (\Exception $exception){

            if($exception->getMessage()==='InvalidS3ObjectException'){
                $photo->update(['is_face_recognition_processed' => true]);
                return null;
            }

            throw new \Exception($exception->getMessage());
        }

        $faces = $aws_face_recognition->get('FaceRecords');

        /*
         * If AWS does not detect faces, skip
         */
        if(count($faces)<=0 || !array_key_exists('Face', $faces[0])){
            $photo->update(['is_face_recognition_processed' => true]);
            return null;
        }

        $face = $faces[0];

        $photo_face_recognition = PhotoFaceRecognition::create([
            'photo_id' => $photo->id,
            'confidence' => $face['Face']['Confidence'],
            'external_image_id' => $face['Face']['ExternalImageId'],
            'payload' => json_encode($aws_face_recognition->toArray()),
            'collection_id' => $collection->id
        ]);

        $photo->update(['is_face_recognition_processed' => true]);
        $user->update(['has_face_processed' => true]);

        $photo_face_recognition->refresh();

        return $photo_face_recognition;
    }
}
