<?php

namespace App\FaceRecognition;

use App\Models\FaceRecognitionCollection;
use Illuminate\Support\Str;

class AwsFaceRekognitionCollectionUtils extends AwsFaceRekognitionClient
{
    /**
     * @throws \Exception
     */
    public function createCollection(string $type): FaceRecognitionCollection
    {
        $name = Str::uuid()->toString();

        try {
            $aws_collection = $this->createAwsCollection($name);
        }catch (\Exception $exception){
            throw new \Exception("Unable to create collection: {$exception->getMessage()}");
        }

        try {
            $collection = FaceRecognitionCollection::create([
                'type' => $type,
                'name' => $name,
                'aws_arn' => $aws_collection->get('CollectionArn')
            ]);
        }catch (\Exception $exception){
            throw new \Exception("Unable to create FaceRecognitionCollection: {$exception->getMessage()}");
        }

        $collection->refresh();

        return $collection;
    }

    /**
     * @throws \Exception
     */
    public function getOrCreateFirstAvailableCollection(string $type): FaceRecognitionCollection
    {
        $collection = FaceRecognitionCollection::where('type', $type)
            ->where('is_active', true)
            ->where('is_full', false)
            ->first();

        /*
         * If exists active collection, return it
         */
        if(isset($collection)){
            return $collection;
        }

        /*
         * If not exists active collection, create and return it
         */
        try {
            $collection = $this->createCollection($type);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        return $collection;
    }
}
