<?php

namespace App\Services\Uploader\MediaFormats;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ImageFormat extends MediaFormat
{

    public function __construct(){
        $this->maxSize = env('MAX_IMAGE_SIZE_IN_MB', 10);
        $this->validExtensions = ['jpeg', 'jpg', 'png', 'heif', 'heic'];
    }

    public function validate($file)
    {
        parent::validate($file); // The parent one shouldn't be directly callable
    }

}
