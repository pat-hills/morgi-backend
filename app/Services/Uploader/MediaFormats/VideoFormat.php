<?php

namespace App\Services\Uploader\MediaFormats;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VideoFormat extends MediaFormat
{

    public function __construct(){
        $this->maxSize = env('MAX_VIDEO_SIZE_IN_MB', 80);
        $this->validExtensions = ['mp4', 'mov', 'qt'];
    }

    public function validate($file)
    {
        parent::validate($file);

        $getID3 = new \getID3();
        $file = $getID3->analyze($file);

        if(isset($file['playtime_seconds']) && round($file['playtime_seconds'])>env('MAX_VIDEO_DURATION_IN_SECONDS')){
            throw new BadRequestHttpException(trans('video.invalid_video_duration'));
        }
    }

}