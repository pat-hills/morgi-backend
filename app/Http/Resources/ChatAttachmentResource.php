<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use App\Models\User;
use App\Utils\StorageUtils;

class ChatAttachmentResource extends Resource
{
    public function small(): ChatAttachmentResource
    {
        $this->addAttributes([
            'id',
            'sender_id',
            'receiver_id',
            'type',
            'path_location',
            'title'
        ]);

        $this->addUrlToResources();

        return $this;
    }

    public function extended(): ChatAttachmentResource
    {
        $this->small();

        $this->addSenderToResources();
        $this->addReceiverToResources();

        return $this;
    }

    private function addUrlToResources()
    {
        foreach ($this->resources as $resource) {
            $resource->url = StorageUtils::signUrl($resource->path_location);
        }
        $this->attributes[] = 'url';
    }

    private function addSenderToResources()
    {
        $senders_ids = $this->resources->pluck('sender_id');
        $senders = UserResource::compute(
            $this->request,
            User::query()->findMany($senders_ids),
            'small'
        )->get();

        foreach ($this->resources as $resource) {
            $resource->sender = $senders->where('id', $resource->sender_id)->first();
        }

        $this->attributes[] = 'sender';
    }

    private function addReceiverToResources()
    {
        $receivers_ids = $this->resources->pluck('receiver_id');
        $receivers = UserResource::compute(
            $this->request,
            User::query()->findMany($receivers_ids),
            'small'
        )->get();

        foreach ($this->resources as $resource) {
            $resource->receiver = $receivers->where('id', $resource->receiver_id)->first();
        }

        $this->attributes[] = 'receiver';
    }
}
