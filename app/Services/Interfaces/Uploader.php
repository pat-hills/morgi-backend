<?php


namespace App\Services\Interfaces;


use Exception;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

interface Uploader
{

    /**
     * Upload to s3 bucket
     *
     * @param UploadedFile $file File to upload
     * @param string $media_type Media's type
     *
     * @return array
     * @throws BadRequestException | ServiceUnavailableHttpException | Exception
     */
    public static function upload(UploadedFile $file, string $media_type) : array;

}