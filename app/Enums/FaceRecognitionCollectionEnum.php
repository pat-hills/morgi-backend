<?php

namespace App\Enums;

class FaceRecognitionCollectionEnum
{
    const TYPE_ROOKIE_MALE = 'rookie_male';
    const TYPE_ROOKIE_FEMALE = 'rookie_female';
    const TYPE_ROOKIE_OTHER = 'rookie_other';
    const TYPE_LEADER_MALE = 'leader_male';
    const TYPE_LEADER_FEMALE = 'leader_female';
    const TYPE_LEADER_OTHER = 'leader_other';

    const TYPES = [
        self::TYPE_ROOKIE_MALE,
        self::TYPE_ROOKIE_FEMALE,
        self::TYPE_ROOKIE_OTHER,
        self::TYPE_LEADER_MALE,
        self::TYPE_LEADER_FEMALE,
        self::TYPE_LEADER_OTHER,
    ];
}
