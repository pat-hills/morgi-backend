<?php

namespace App\Utils\Upload;

use App\Utils\StorageUtils;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Illuminate\Http\UploadedFile;

class UploadUtils
{
    public const BYTES_IN_MB = 1000000;
    public const TYPE_PHOTO  = 'photo';
    public const TYPE_VIDEO  = 'video';

    public static function upload(UploadedFile $file, string $type, bool $is_chat_attachment = false)
    {
        if (!$file->isValid()) {
            return response()->json(['message' => trans($type . '.invalid_' . $type)], 400);
        }

        $image_size_in_mb = round($file->getSize() / self::BYTES_IN_MB, 2);

        switch ($type) {
            case self::TYPE_PHOTO:
                $max_value = env('MAX_IMAGE_SIZE_IN_MB', 10);
                break;
            case self::TYPE_VIDEO:
                $max_value = env('MAX_VIDEO_SIZE_IN_MB', 80);
                break;
            default:
                throw new BadRequestException("File type not valid");
        }

        if ($image_size_in_mb >= $max_value) {
            throw new BadRequestException(("Invalid {$type} size, max size allowed: {$max_value}"));
        }

        // TODO: rimuovere sta pezza e mappare bene chat attachment
        // Pezza aggiunta per far andare nella giusta folder i chat attachments
        if($is_chat_attachment){
            $type = 'chat_attachment';
        }

        $response = StorageUtils::storeObject($file, $type);
        if ($response['status'] === 'error') {
            throw new BadRequestException($response['message']);
        }

        return $response;
    }
}
