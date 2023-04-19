<?php


namespace App\Services\Uploader;


use App\Models\GoalMedia;
use App\Services\Uploader\MediaFormats\ImageFormat;
use App\Services\Uploader\MediaFormats\VideoFormat;
use Exception;
use App\Services\Interfaces\Uploader as UploaderInterface;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class Uploader implements UploaderInterface
{

    private const HANDLERS = ['S3Uploader'];

    /**
     * @inheritDoc
     */
    public static function upload($file, string $media_type): array
    {
        // Because of course laravel doesn't handle one file the same as multiple files...
        if($file instanceof UploadedFile){
            self::validate($file, $media_type);
        }

        $handler = new S3Uploader();
        return $handler::upload($file, $media_type);
    }

    /**
     * Validate file to upload
     *
     * @param $file File to upload
     * @param string $media_type Media's type
     *
     * @throws BadRequestException | ServiceUnavailableHttpException | Exception
     */
    public static function validate($file, string $media_type) : void
    {
        if(!in_array($media_type, GoalMedia::TYPES)){
            throw new BadRequestException("Invalid media type supplied");
        }

        switch ($media_type){
            case GoalMedia::TYPE_VIDEO:
                $format = new VideoFormat();
                break;
            case GoalMedia::TYPE_IMAGE:
                $format = new ImageFormat();
                break;
            default:
                throw new BadRequestException("Invalid media type supplied");
        }

        $format->validate($file);
    }
}
