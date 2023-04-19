<?php


namespace App\FaceRecognition;

use Aws\Rekognition\Exception\RekognitionException;
use Aws\Rekognition\RekognitionClient;
use Aws\Result;

class AwsFaceRekognitionClient
{

    /*
     * This attribute contains AWS's RekognitionClient
     */
    private $client;

    public function __construct()
    {
        $credentials = [
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
        ];

        $this->client = new RekognitionClient($credentials);
    }

    protected function createAwsCollection(string $collection_id): Result
    {
        try {
            $result = $this->client->createCollection([
                'CollectionId' => $collection_id
            ]);
        }catch (RekognitionException $exception){
            throw new \Exception($exception->getMessage());
        }

        return $result;
    }

    protected function storeAwsPhotoFaces(string $collection_id, string $external_image_id, string $s3_file_path): Result
    {
        try {
            $result = $this->client->indexFaces([
                'CollectionId' => $collection_id,
                'DetectionAttributes' => ['DEFAULT'],
                'ExternalImageId' => $external_image_id,
                'MaxFaces' => 1,
                'QualityFilter' => 'AUTO',
                'Image' => [
                    'S3Object' => [
                        'Bucket' => env('AWS_BUCKET'),
                        'Name' => $s3_file_path,
                    ],
                ],
            ]);
        }catch (RekognitionException $exception){
            throw new \Exception($exception->getAwsErrorCode());
        }

        return $result;
    }

    protected function searchAwsPhotoFaces(string $collection_id, string $s3_file_path): Result
    {
        try {
            $result = $this->client->searchFacesByImage([
                'CollectionId' => $collection_id,
                'FaceMatchThreshold' => 50,
                'MaxFaces' => 100, //Max face to return
                'QualityFilter' => 'AUTO',
                'Image' => [
                    'S3Object' => [
                        'Bucket' => env('AWS_BUCKET'),
                        'Name' => $s3_file_path,
                    ],
                ],
            ]);
        }catch (RekognitionException $exception){
            throw new \Exception($exception->getAwsErrorCode());
        }

        return $result;
    }

}
