<?php

namespace App\Enums;

class TransactionTypeEnum
{
    const TYPES_TAGS = [
        'rookie' => [
            'chat' => ['{{leader_full_name}}'],
            'gift' => ['{{leader_full_name}}'],
            'withdrawal' => ['{{payment_method}}', '{{payment_info}}', '{{payment_approved_at}}', '{{taxed_dollars}}', '{{payment_period_start_date}}', '{{payment_period_end_date}}'],
            'withdrawal_pending' => ['{{payment_method}}', '{{payment_info}}', '{{taxed_dollars}}', '{{payment_period_start_date}}', '{{payment_period_end_date}}'],
            'withdrawal_rejected' => ['{{payment_method}}', '{{payment_info}}', '{{payment_rejected_at}}', '{{taxed_dollars}}', '{{payment_period_start_date}}', '{{payment_period_end_date}}'],
            'refund' => ['{{leader_full_name}}', '{{referal_internal_id}}'],
            'bonus' => [],
            'bought_micromorgi' => [],
            'rookie_block_leader' => ['{{leader_full_name}}', '{{referal_internal_id}}'],
            'refund_bonus' => [],
            'chargeback' => ['{{referal_internal_id}}'],
            'fine' => [],
            'gift_with_coupon' => ['{{leader_full_name}}'],
            'not_refund_gift_with_coupon' => ['{{leader_full_name}}'],
            'goal_withdraw' => ['{{goal_id}}', '{{goal_name}}'],
            'goal' => []
        ],
        'leader' => [
            'chat' => ['{{rookie_username}}', '{{rookie_full_name}}'],
            'gift' => ['{{rookie_username}}', '{{morgi}}', '{{rookie_full_name}}'],
            'refund' => ['{{rookie_username}}', '{{rookie_full_name}}', '{{referal_internal_id}}'],
            'bonus' => [],
            'bought_micromorgi' => ['{{micromorgi}}'],
            'rookie_block_leader' => ['{{rookie_username}}', '{{rookie_full_name}}'],
            'refund_bonus' => [],
            'chargeback' => ['{{rookie_username}}', '{{rookie_full_name}}', '{{referal_internal_id}}'],
            'fine' => [],
            'gift_with_coupon' => ['{{rookie_username}}', '{{rookie_full_name}}', '{{coupon_id}}'],
            'not_refund_gift_with_coupon' => ['{{rookie_username}}', '{{rookie_full_name}}', '{{coupon_id}}'],
            'goal_withdraw' => [],
            'goal' => ['{{rookie_username}}', '{{rookie_full_name}}']
        ]
    ];

    const TYPES_TAGS_ATTRIBUTE = [
        'rookie' => [
            'chat' => ['leader_full_name'],
            'gift' => ['leader_full_name'],
            'withdrawal' => ['payment_method', 'payment_info', 'payment_approved_at', 'taxed_dollars', 'payment_period_start_date', 'payment_period_end_date'],
            'withdrawal_pending' => ['payment_method', 'payment_info', 'taxed_dollars', 'payment_period_start_date', 'payment_period_end_date'],
            'withdrawal_rejected' => ['payment_method', 'payment_info', 'payment_rejected_at', 'taxed_dollars', 'payment_period_start_date', 'payment_period_end_date'],
            'refund' => ['leader_full_name', 'referal_internal_id'],
            'bonus' => [],
            'bought_micromorgi' => [],
            'rookie_block_leader' => ['leader_full_name', 'referal_internal_id'],
            'refund_bonus' => [],
            'chargeback' => ['referal_internal_id'],
            'fine' => [],
            'gift_with_coupon' => ['leader_full_name'],
            'not_refund_gift_with_coupon' => ['leader_full_name'],
            'goal_withdraw' => ['goal_id', 'goal_name'],
            'goal' => []
        ],
        'leader' => [
            'chat' => ['rookie_username', 'rookie_full_name'],
            'gift' => ['rookie_username', 'morgi', 'rookie_full_name'],
            'refund' => ['rookie_username', 'rookie_full_name', 'referal_internal_id'],
            'bonus' => [],
            'bought_micromorgi' => ['micromorgi', 'dollars'],
            'rookie_block_leader' => ['rookie_username', 'rookie_full_name'],
            'refund_bonus' => [],
            'chargeback' => ['rookie_username', 'rookie_full_name', 'referal_internal_id'],
            'fine' => [],
            'gift_with_coupon' => ['rookie_username', 'rookie_full_name', 'coupon_id'],
            'not_refund_gift_with_coupon' => ['rookie_username', 'rookie_full_name', 'coupon_id'],
            'goal_withdraw' => [],
            'goal' => ['rookie_username', 'rookie_full_name']
        ]
    ];
}
