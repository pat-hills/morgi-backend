<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaderCcbillData extends Model
{
    use HasFactory;

    protected $table = 'leaders_ccbill_data';

    protected $fillable = [
        'subscriptionId',
        'clientAccnum',
        'clientSubacc',
        'subscriptionCurrencyCode',
        'cardType',
        'last4',
        'expDate',
        'paymentAccount',
        'ipAddress',
        'reservationId',
        'leader_id',
        'active',
        'billingCountry',
        'accountingCurrencyCode',
        'address1',
        'billedCurrencyCode',
        'billedInitialPrice',
        'billedRecurringPrice',
        'bin',
        'city',
        'dynamicPricingValidationDigest',
        'email',
        'firstName',
        'lastName',
        'formName',
        'initialPeriod',
        'paymentType',
        'postalCode',
        'priceDescription',
        'referringUrl',
        'state',
        'subscriptionTypeId',
        'subscriptionInitialPrice',
        'transactionId'
    ];

    protected $hidden = [
        'last4',
        'expDate',
        'cardType',
        'ipAddress',
        'reservationId',
        'paymentAccount'
    ];

    public function getCardInfo()
    {
        return [
            'type' => $this->cardType,
            'exp_date' => $this->expDate,
            'last_digits' => $this->last4
        ];
    }
}
