<?php

namespace App\Http\Controllers;

use App\Enums\ChatAttachmentEnum;
use App\Http\Resources\ChatAttachmentResource;
use App\Models\ChatAttachment;
use App\Models\PubnubChannel;
use App\Utils\Upload\UploadUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ChatAttachmentController extends Controller
{
    public function store(Request $request, PubnubChannel $pubnubChannel, $type): JsonResponse
    {

        if(!($request->hasFile('file') && $request->file('file')->isValid())) {
            return response()->json(['message' => ($type == ChatAttachmentEnum::PHOTO) ? trans('photo.invalid_image') : trans('video.invalid_video')], 400);
        }

        $file = $request->file('file');
        $mimes = ['image' => 'mimes:jpeg,jpg,png', 'video' => 'mimes:mp4,mov,qt'];

        $validator = Validator::make($request->all(), [
            'file' => ['required', $mimes[$type]],
            'receiver_id' => ['required', 'integer', 'exists:users,id']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $types = ['image' => UploadUtils::TYPE_PHOTO, 'video' => UploadUtils::TYPE_VIDEO];
        $type = $types[$type];

        try {
            $response = UploadUtils::upload($file, $type, true);
        } catch (BadRequestException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        $chat_attachment = ChatAttachment::create([
            'path_location' => $response['path_location'],
            'type' => $type,
            'sender_id' => Auth::id(),
            'receiver_id' => (int)$request->receiver_id
        ]);

        $response = ChatAttachmentResource::compute(
            $request,
            $chat_attachment
        )->first();

        return response()->json($response, 201);
    }

    public function show(Request $request, PubnubChannel $pubnubChannel, ChatAttachment $chatAttachment): JsonResponse
    {
        $requesting_user = $request->user();
        if(!$chatAttachment->canViewAttachment($requesting_user->id)){
            return response()->json(['message' => 'You cant view this attachment'], 403);
        }

        $response = ChatAttachmentResource::compute(
            $request,
            $chatAttachment
        )->first();

        return response()->json($response);
    }
}
