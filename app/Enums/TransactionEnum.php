<?php

namespace App\Enums;

class TransactionEnum
{
    const PENDING = 'pending';
    const APPROVED = 'approved';
    const DECLINED = 'declined';

    const INTERNAL_STATUS = [
        self::PENDING,
        self::APPROVED,
        self::DECLINED
    ];

    const VOID = 'void';
    const CHARGEBACK = 'chargeback';

    const REFUND_TYPE = [
        self::VOID,
        self::CHARGEBACK,
        self::REFUND
    ];

    const CHAT = 'chat';
    const GIFT = 'gift';
    const WITHDRAWAL = 'withdrawal';
    const REFUND = 'refund';
    const BONUS = 'bonus';
    const WITHDRAWAL_REJECTED = 'withdrawal_rejected';
    const BOUGHT_MICROMORGI = 'bought_micromorgi';

    const TYPES = [
        self::CHAT,
        self::GIFT,
        self::WITHDRAWAL,
        self::REFUND,
        self::BONUS,
        self::WITHDRAWAL,
        self::BOUGHT_MICROMORGI,
        'goal',
        'goal_withdraw'
    ];

    const ROOKIE_SIGN_MAP = [
        'chat' => '+',
        'gift' => '+',
        'bonus' => '+',
        'withdrawal' => '-',
        'withdrawal_pending' => '-',
        'withdrawal_rejected' => '-',
        'refund' => '-',
        'fine' => '-',
        'goal' => '+',
        'goal_withdraw' => '+'
    ];

    const LEADER_SIGN_MAP = [
        'chat' => '-',
        'gift' => '-',
        'bonus' => '+',
        'bought_micromorgi' => '-',
        'fine' => '-',
        'goal' => '-'
    ];

    const LEADER_REFUND_SIGN_MAP = [
        'chat' => '+',
        'gift' => '+',
        'bonus' => '-',
        'bought_micromorgi' => '+',
        'goal' => '+'
    ];
}
