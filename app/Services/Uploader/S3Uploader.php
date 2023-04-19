<?php


namespace App\Services\Uploader;


use App\Models\GoalMedia;
use App\Models\Media;
use App\Services\Interfaces\Uploader as UploaderInterface;
use App\Utils\ImageThumbsUtils;
use App\Utils\StorageUtils;
use App\Utils\VideoUtils;
use Exception;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Imagick;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;


class S3Uploader implements UploaderInterface
{

    const S3_VERSION_FOLDER_NAME = 'v2/';
    const S3_UNASSIGNED_FOLDER_NAME = self::S3_VERSION_FOLDER_NAME . 'unassigned_media/';
    const S3_PUBLIC_UPLOADS_FOLDER_NAME = self::S3_VERSION_FOLDER_NAME . "public/";
    const S3_PRIVATE_UPLOADS_FOLDER_NAME = self::S3_VERSION_FOLDER_NAME . "private/";

    const S3_AVATARS_FOLDER_NAME = self::S3_PUBLIC_UPLOADS_FOLDER_NAME . 'avatars/';
    const S3_HEADERS_FOLDER_NAME = self::S3_PUBLIC_UPLOADS_FOLDER_NAME . 'headers/';
    const S3_IMAGES_FOLDER_NAME = self::S3_PRIVATE_UPLOADS_FOLDER_NAME . 'images/';
    const S3_VIDEOS_FOLDER_NAME = self::S3_PRIVATE_UPLOADS_FOLDER_NAME . 'videos/';
    const ORIGINAL_FILE_NAME = 'original';


    /**
     * @inheritDoc
     */
    public static function upload(UploadedFile $file, string $media_type): array
    {

        if(!array_key_exists($media_type, GoalMedia::FILE_TYPES)){
            throw new Exception("Unable to compute file's type", 500);
        }

        $folder_name = self::generateFolderName($media_type);
        $filename = self::ORIGINAL_FILE_NAME . '.' . $file->extension();

        /*
         * Let's upload media on S3 bucket
         */

        try {
            self::storeFile($file, $filename, $folder_name);
        }catch (UnsupportedMediaTypeHttpException $e){
            throw new Exception($e->getMessage(), 415);
        } catch (Exception $e){
            throw new Exception($e->getMessage(), 500);
        }

        /*
         * Return response with this format ['media_path' => 'string']
         */

        return [
            'media_path' => $folder_name . '/' . $filename
        ];
    }

    private static function storeFile(UploadedFile $file, string $filename, string $folder): void
    {

        try {
            $file->storeAs($folder, $filename);
        }catch (Exception $e){
            throw new UnsupportedMediaTypeHttpException("Unable to upload media: {$e->getMessage()}");
        }

    }

    private static function generateFolderName(string $media_type): string
    {

        $folder_date_format = now()->format('Y-m') . "/" . now()->format('d') . "/";
        $final_folder_name = now()->timestamp . '-' . Str::uuid();

        if($media_type===GoalMedia::TYPE_AVATAR){
            return self::S3_AVATARS_FOLDER_NAME . $folder_date_format . $final_folder_name;
        }

        if($media_type===GoalMedia::TYPE_HEADER){
            return self::S3_HEADERS_FOLDER_NAME . $folder_date_format . $final_folder_name;
        }

        if($media_type===GoalMedia::TYPE_IMAGE){
            return self::S3_IMAGES_FOLDER_NAME . $folder_date_format . $final_folder_name;
        }

        if($media_type===GoalMedia::TYPE_VIDEO){
            return self::S3_VIDEOS_FOLDER_NAME . $folder_date_format . $final_folder_name;
        }

        /*
         * Fallback folder
         */
        return self::S3_UNASSIGNED_FOLDER_NAME . $folder_date_format . "/$media_type/" . $final_folder_name;
    }
}
