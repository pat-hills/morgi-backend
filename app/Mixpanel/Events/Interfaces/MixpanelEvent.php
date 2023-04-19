<?php

namespace App\Mixpanel\Events\Interfaces;

interface MixpanelEvent
{
    public function __construct(int $user_id, array $frontend_properties);

    public function store(): void;
}
