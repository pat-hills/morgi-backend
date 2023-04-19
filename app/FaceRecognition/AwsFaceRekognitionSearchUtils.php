<?php

namespace App\FaceRecognition;

use App\Models\FaceRecognitionCollection;
use App\Models\Gender;
use App\Models\Photo;
use App\Models\RookieFaceRecognitionMatch;
use App\Models\User;
use App\Models\LeaderFaceRecognitionMatch;
use Illuminate\Support\Collection;

class AwsFaceRekognitionSearchUtils extends AwsFaceRekognitionClient
{
    public function matchLeaderRookies(Photo $photo): void
    {
        if(!$this->leaderHasToMatch($photo->user_id)){
            return;
        }

        $user = User::find($photo->user_id);
        if(!isset($user) || $user->type!=='leader'){
            throw new \Exception("Unable to retrieve photo's user");
        }

        $user_gender = Gender::find($user->gender_id);
        if(!isset($user_gender)){
            throw new \Exception("Unable to retrieve user's gender");
        }

        $collections = $this->getCollections($user->type, $user_gender->key_name);

        foreach ($collections as $collection){
            try {
                $collection_photos = $this->searchFacesPhotos($collection, $photo);
                if(isset($collection_photos)){
                    $this->storeLeaderMatches($user, $photo, $collection_photos);
                }
            }catch (\Exception $exception){
            }
        }
    }

    public function matchRookieRookies(Photo $photo): void
    {
        if(!$this->rookieHasToMatch($photo->user_id)){
            return;
        }

        $user = User::find($photo->user_id);
        if(!isset($user) || $user->type!=='rookie'){
            throw new \Exception("Unable to retrieve photo's user");
        }

        $collections = $this->getCollections($user->type);

        foreach ($collections as $collection){
            try {
                $collection_photos = $this->searchFacesPhotos($collection, $photo);
                if(isset($collection_photos)){
                    $this->storeRookieMatches($user, $photo, $collection_photos);
                }
            }catch (\Exception $exception){
            }
        }
    }

    private function leaderHasToMatch(int $leader_id): bool
    {
        return LeaderFaceRecognitionMatch::query()
            ->where('leader_id', $leader_id)
            ->count() <= 20;
    }

    private function rookieHasToMatch(int $rookie_id): bool
    {
        return RookieFaceRecognitionMatch::query()
                ->where('rookie_id', $rookie_id)
                ->count() <= 20;
    }

    private function storeLeaderMatches(User $user, Photo $user_photo, Collection $photos): void
    {
        $matches = [];
        foreach ($photos as $photo){

            if($photo->id===$user_photo || $user->id===$photo->user_id){
                continue;
            }

            $matches[] = [
                'leader_id' => $user->id,
                'rookie_id' => $photo->user_id,
                'leader_photo_id' => $user_photo->id,
                'rookie_photo_id' => $photo->id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        if(count($matches)>0){
            LeaderFaceRecognitionMatch::query()->insert($matches);
        }
    }

    private function storeRookieMatches(User $user, Photo $user_photo, Collection $photos): void
    {
        $matches = [];
        foreach ($photos as $photo){

            if($photo->id===$user_photo || $user->id===$photo->user_id){
                continue;
            }

            $matches[] = [
                'rookie_id' => $user->id,
                'to_rookie_id' => $photo->user_id,
                'rookie_photo_id' => $user_photo->id,
                'to_rookie_photo_id' => $photo->id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        if(count($matches)>0){
            RookieFaceRecognitionMatch::query()->insert($matches);
        }
    }

    private function searchFacesPhotos(FaceRecognitionCollection $collection, Photo $photo): ?Collection
    {
        /*
         * Retrieve on AWS's collection similar faces
         */
        try {
            $aws_face_recognition = $this->searchAwsPhotoFaces($collection->name, $photo->path_location);
        }catch (\Exception $exception){

            if($exception->getMessage()==='InvalidS3ObjectException'){
                return null;
            }

            throw new \Exception($exception->getMessage());
        }

        $aws_faces = $aws_face_recognition->get('FaceMatches');
        $external_images_ids = $this->getAwsFacesExternalImageIds($aws_faces);
        $photos = Photo::query()->select('photos.*')
            ->join('photos_face_recognition', 'photos_face_recognition.photo_id', '=', 'photos.id')
            ->whereIn('photos_face_recognition.external_image_id', $external_images_ids)
            ->where('photos.id', '!=', $photo->id)
            ->groupBy('photos.id')
            ->get();

        return ($photos->isNotEmpty()) ? $photos : null;
    }

    private function getAwsFacesExternalImageIds(array $aws_faces): array
    {
        $aws_faces_external_ids = [];
        foreach ($aws_faces as $aws_face){

            if(!isset($aws_face['Face']['ExternalImageId'])){
                continue;
            }

            $aws_faces_external_ids[] = $aws_face['Face']['ExternalImageId'];
        }

        return $aws_faces_external_ids;
    }

    private function getCollections(string $user_type, string $interested_gender_key_name = null): ?array
    {
        $user_type_to_find = 'rookie';

        if($interested_gender_key_name==='all' || $user_type==='rookie'){
            $collections = [];
            $collections_types = [
                "{$user_type_to_find}_male",
                "{$user_type_to_find}_female",
                "{$user_type_to_find}_other"
            ];

            foreach ($collections_types as $collection_type){
                try {
                    $collections[] = (new AwsFaceRekognitionCollectionUtils())->getOrCreateFirstAvailableCollection($collection_type);
                }catch (\Exception $exception){
                    throw new \Exception($exception->getMessage());
                }
            }

            return $collections;
        }

        $collection_type = "{$user_type_to_find}_$interested_gender_key_name";
        try {
            return [
                (new AwsFaceRekognitionCollectionUtils())->getOrCreateFirstAvailableCollection($collection_type)
            ];
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}
