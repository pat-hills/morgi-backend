<?php


namespace App\Utils;


use Aws\CloudFront\CloudFrontClient;
use Aws\ElasticTranscoder\ElasticTranscoderClient;
use Aws\Exception\AwsException;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Imagick;
use ImagickException;
use Intervention\Image\Exception\ImageException;
use Intervention\Image\Facades\Image;

class StorageUtils
{

    const PHOTOS_VALID_EXTENSION = ['jpeg', 'jpg', 'png', 'heif', 'heic'];
    const VIDEOS_VALID_EXTENSION = ['mp4', 'mov', 'qt'];

    public static function storeObject($file, $type){

        if(!(isset($file) || isset($type))){
            return ['status' => 'error', 'message' => 'Invalid input'];
        }

        $random_string = Str::random(12);
        $random_int = rand(1000, 9999);

        $today = Carbon::now()->format('Y-m-d');

        try {
            $extension = strtolower($file->extension());
        }catch (\Exception $e){
            return ['status' => 'error', 'message' => 'The file that you uploaded is invalid. Extension error.', 'type' => 'extension_error'];
        }

        switch ($type){
            case "photo":

                if(!in_array($extension, self::PHOTOS_VALID_EXTENSION)){
                    return ['status' => 'error', 'message' => "The file that you uploaded is invalid. Invalid extension. $extension"];
                }

                $tmp_new_path = sys_get_temp_dir() . '/' . Str::uuid() . ".jpg";
                $folder = "temp";

                if(in_array($extension, ['heif', 'heic'])){
                    try {
                        $name = "photo_{$random_int}_$random_string.jpg";

                        $image_blob = file_get_contents($file->getPathName());

                        $new_img = new Imagick();
                        $new_img->readImageBlob($image_blob);
                        $new_img->scaleImage(1280, 640,true);
                        $new_img->setImageFormat('jpeg');
                        $new_img->setImageCompression(imagick::COMPRESSION_JPEG);
                        $new_img->setCompressionQuality(100);
                        $new_img->writeImage($tmp_new_path);
                        $new_img->clear();

                        $file = new UploadedFile($tmp_new_path, Str::orderedUuid());

                    } catch (ImagickException $exception) {
                        return ['status' => 'error', 'message' => "Could not validate $extension images, " . $exception->getMessage()];
                    }
                }else{
                    try {
                        $name = "photo_{$random_int}_$random_string.$extension";

                        $file_path = sys_get_temp_dir() . '/' . $name;
                        $img = Image::make($file);
                        $img->resize(null, 640, function ($const) {
                            $const->aspectRatio();
                        })->encode($extension, 100)->save($file_path);

                        $file = new UploadedFile($file_path, Str::orderedUuid());
                    }catch (ImageException $exception){
                        return ['status' => 'error', 'message' => "Could not validate $extension images, " . $exception->getMessage()];

                    }
                }

                break;
            case "video":

                if(!in_array($extension, self::VIDEOS_VALID_EXTENSION)){
                    return ['status' => 'error', 'message' => "The file that you uploaded is invalid. Invalid extension. $extension", 'type' => 'extension_error'];
                }

                $folder = "temp";
                $name = "video_{$random_int}_$random_string.$extension";
                break;
            case "chat_attachment":

                if(!(in_array($extension, self::PHOTOS_VALID_EXTENSION) || in_array($extension, self::VIDEOS_VALID_EXTENSION))){
                    return ['status' => 'error', 'message' => "The file that you uploaded is invalid. Invalid extension. $extension"];
                }

                $folder = "chat_attachments";
                $name = "chat_attachment_{$random_int}_$random_string.$extension";
                break;
            default:
                return ['status' => 'error', 'message' => 'Invalid file type', 'type' => 'extension_error'];
        }

        $final_folder = $folder . '/' . $today;

        try {
            $file->storeAs($final_folder, $name);
        }catch (\Exception $e){
            return ['status' => 'error', 'message' => $e->getMessage(), 'type' => 'upload_error'];
        }

        return ['status' => true, 'path_location' => $final_folder . '/' . $name];
    }

    public static function assignObject($path_location, $type, $user){

        if(!(isset($path_location) || isset($user) || isset($type))){
            return ['status' => 'error', 'message' => 'Invalid input'];
        }

        $path_exploded_slash = explode("/", $path_location);
        $folder = $path_exploded_slash[0];

        if($folder!=='temp' || !Storage::disk('s3')->exists($path_location)){
            return ['status' => 'error', 'message' => "File doesn't exists"];
        }

        $random_string = Str::random(12);

        $path_exploded_dot = explode(".", $path_location);
        $extension = strtolower($path_exploded_dot[count($path_exploded_dot)-1]);

        $today = Carbon::now()->format('Y-m-d');

        switch ($type){
            case "photo":

                if(!in_array($extension, self::PHOTOS_VALID_EXTENSION)){
                    return ['status' => 'error', 'message' => "The file that you uploaded is invalid. Invalid extension. $extension"];
                }

                $folder = "photos";
                $name = "{$user->id}_photo_$random_string.$extension";
                break;
            case "video":

                if(!in_array($extension, self::VIDEOS_VALID_EXTENSION)){
                    return ['status' => 'error', 'message' => "The file that you uploaded is invalid. Invalid extension. $extension"];
                }

                $folder = "videos";
                $name = "{$user->id}_video_$random_string.$extension";
                break;
            case "identity_document":

                if(!in_array($extension, self::PHOTOS_VALID_EXTENSION)){
                    return ['status' => 'error', 'message' => "The file that you uploaded is invalid. Invalid extension. $extension"];
                }

                $folder = "identity_documents";
                $name = "{$user->id}_identity_document_$random_string.$extension";
                break;
            default:
                return ['status' => 'error', 'message' => 'Invalid file type'];
        }

        $final_folder = $folder . '/' . $today;

        try {
            Storage::disk('s3')->move($path_location, $final_folder . '/' . $name);
        }catch (\Exception $e){
            return ['status' => 'error', 'message' => $e->getMessage()];
        }

        if($type==='video'){
            try {
                self::createTranscoderJob($final_folder, $name);
            }catch (\Exception $exception){
            }
        }

        return ['status' => true, 'path_location' => $final_folder . '/' . $name];
    }

    public static function objectExists($path_location){

        return Storage::disk('s3')->exists($path_location);
    }

    public static function signUrl($path_location){

        $cloudFront = new CloudFrontClient([
            'region'  => env('AWS_DEFAULT_REGION'),
            'version' => '2014-11-06',
        ]);

        $resourceKey = env('AWS_URL', 'https://content.staging.morgi.org/') . $path_location;
        $expires = time() + env('AWS_TEMP_URL_TIME_IN_SECONDS', 3600);

        $signedUrlCannedPolicy = $cloudFront->getSignedUrl([
            'url'         => $resourceKey,
            'expires'     => $expires,
            'private_key' => env('CLOUDFRONT_PRIVATE_KEY_PATH'),
            'key_pair_id' => env('CLOUDFRONT_KEY_PAIR_ID'),
        ]);

        return $signedUrlCannedPolicy;
    }

    public static function createTranscoderJob(string $folder, string $filename): void
    {

        $transcoder_client = new ElasticTranscoderClient([
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
            'version' => '2012-09-25',
            'region' => env('AWS_DEFAULT_REGION'),
            'default_caching_config' => '/tmp',
        ]);

        /*
         * Hardcoded, but for the preset is OK!
         */
        $preset_id = '1351620000001-100070';

        $pipeline_id = env('AWS_TRANSCODER_PIPELINE_ID');
        $S3_file = "$folder/$filename";

        $output_key_prefix = $folder;
        $outputs = [
            [
                'Key' => "/processed_$filename",
                'PresetId' => $preset_id
            ]
        ];

        try {
            $transcoder_client->createJob([
                'PipelineId' => $pipeline_id,
                'Outputs' => $outputs,
                'OutputKeyPrefix' => $output_key_prefix,
                'Input' => [
                    'Key' => $S3_file
                ]
            ]);
        } catch (AwsException $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
