<?php

namespace App\Services\Uploader\MediaFormats;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class MediaFormat
{
    protected $maxSize;
    protected $validExtensions;

    protected function validate($file){

        if(!$file->isValid()){
            throw new BadRequestException("Invalid media");
        }

        if($file->isExecutable()){
            throw new BadRequestException("Invalid media, file executable");
        }

        try {
            $file_extension = strtolower($file->extension());
        } catch (\Exception $exception){
            throw new BadRequestException("Invalid media, unable to read extension");
        }

        if (!in_array($file_extension, $this->validExtensions)){
            throw new BadRequestException("Invalid media, invalid extension");
        }

        $video_size_in_mb = round($file->getSize()/1000000, 2);
        if($video_size_in_mb > $this->maxSize){
            throw new BadRequestException("Invalid video size, max size allowed: {$this->maxSize} MB");
        }

    }
}