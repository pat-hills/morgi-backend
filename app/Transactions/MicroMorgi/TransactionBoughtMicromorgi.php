<?php

namespace App\Transactions\MicroMorgi;

use App\LeaderPackages\AddMicromorgi;
use App\Logger\Logger;
use App\Models\ActivityLog;
use App\Models\LeaderPayment;
use App\Models\MicromorgiPackage;
use App\Models\Transaction;
use App\Transactions\TransactionBuilder;
use App\Utils\ActivityLogsUtils;
use App\Utils\NotificationUtils;
use App\Utils\Utils;

class TransactionBoughtMicromorgi extends TransactionBuilder
{
    public $type = 'bought_micromorgi';

    public static function create(int $leader_id,
                                  int $dollars,
                                  int $payment_method_id,
                                  string $ccbill_subscriptionId,
                                  string $ip_address = null): Transaction
    {
        $micromorgi = MicromorgiPackage::getMicromorgiAmount($dollars);

        try {
            $builder = new TransactionBoughtMicromorgi();
            $builder->setLeaderId($leader_id)
                ->setMicromorgi($micromorgi)
                ->setDollars($dollars)
                ->createLeaderPayment($payment_method_id, $ccbill_subscriptionId, $ip_address)
                ->store()
                ->adaptBalances()
                ->storeActivityLog();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        try {
            $builder->sendNotifications();
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        if(!isset($builder->transaction)){
            throw new \Exception("Transaction was not created");
        }

        return $builder->transaction;
    }

    private function createLeaderPayment(int $payment_method_id, string $ccbill_subscriptionId, string $ip_address = null): TransactionBoughtMicromorgi
    {
        $payment_country = (isset($ip_address)) ? Utils::ipInfo($ip_address) : null;

        $leader_payment = LeaderPayment::create([
            'leader_id' => $this->leader_id,
            'currency_type' => 'micro_morgi',
            'type' => 'mm_purchase',
            'status' => 'paid',
            'amount' => $this->micromorgi,
            'dollar_amount' => $this->dollars,
            'ip_address' => $ip_address,
            'payment_country' => $payment_country,
            'leader_payment_method_id' => $payment_method_id,
            'ccbill_subscriptionId' => $ccbill_subscriptionId,
        ]);

        $this->leader_payment_id = $leader_payment->id;

        return $this;
    }

    private function adaptBalances(): TransactionBoughtMicromorgi
    {
        AddMicromorgi::config($this->leader_id, $this->micromorgi)
            ->setLeaderPaymentId($this->leader_payment_id)
            ->add();
        return $this;
    }

    private function storeActivityLog(): TransactionBoughtMicromorgi
    {
        $this->activity_log = ActivityLog::create([
            'internal_id' => ActivityLogsUtils::generateInternalId($this->leader_id),
            'leader_id' => $this->leader_id,
            'micromorgi' => $this->micromorgi,
            'dollars' => $this->dollars,
            'transaction_internal_id' => $this->transaction->internal_id
        ]);

        return $this;
    }

    private function sendNotifications(): TransactionBoughtMicromorgi
    {
        $attributes = [
            'amount_micromorgi' => $this->micromorgi,
            'amount' => $this->dollars,
            'currency' => $this->leader_user->currency
        ];

        NotificationUtils::sendNotification(
            $this->leader_id,
            "leader_buy_micromorgi_package",
            now(),
            $attributes
        );

        return $this;
    }
}
