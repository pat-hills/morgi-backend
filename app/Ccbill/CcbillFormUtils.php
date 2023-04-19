<?php


namespace App\Ccbill;


use App\Ccbill\TestUtils\FakeData;
use App\Models\TransactionHandshake;
use Illuminate\Support\Str;

class CcbillFormUtils
{
    private const CURRENCY_TYPE_MAP = [
        'micromorgi' => 'micromorgi',
        'gift' => 'morgi',
        'renew' => 'morgi',
        'credit_card' => 'morgi'
    ];

    public static function createGiftForm($leader_id, $rookie_id, $price, $currency_code, $rookie_first_name)
    {
        $metadata = [
            'leader_id' => $leader_id,
            'rookie_id' => $rookie_id,
            'amount' => $price,
            'dollar_amount' => $price,
            'rookie_first_name' => $rookie_first_name
        ];

        try {
            $url = self::createForm('gift', $leader_id, $currency_code, $metadata);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        return $url;
    }

    public static function createRenewForm($leader_id, $price, $currency_code, $renew_subscriptions_ids, $update_subscriptions_ids = null)
    {
        $metadata = [
            'leader_id' => $leader_id,
            'rookie_id' => null,
            'amount' => $price,
            'dollar_amount' => $price,
            'renew_subscriptions_id' => $renew_subscriptions_ids,
            'update_subscriptions_id' => $update_subscriptions_ids
        ];

        try {
            $url = self::createForm('renew', $leader_id, $currency_code, $metadata);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        return $url;
    }

    public static function createCreditCardForm($leader_id, $currency_code, $update_subscriptions_ids = null)
    {
        $metadata = [
            'leader_id' => $leader_id,
            'rookie_id' => null,
            'amount' => 0,
            'dollar_amount' => 0,
            'update_subscriptions_id' => $update_subscriptions_ids
        ];

        try {
            $url = self::createForm('credit_card', $leader_id, $currency_code, $metadata);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        return $url;
    }

    public static function createMicromorgiForm($leader_id, $micromorgi, $price, $currency_code)
    {
        $metadata = [
            'leader_id' => $leader_id,
            'rookie_id' => null,
            'amount' => $micromorgi,
            'dollar_amount' => $price
        ];

        try {
            $url = self::createForm('micromorgi', $leader_id, $currency_code, $metadata);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        return $url;
    }

    private static function createForm($type, $leader_id, $currency_code, $metadata)
    {
        if(!array_key_exists($type, self::CURRENCY_TYPE_MAP)){
            throw new \Exception("[CCBill Form] Invalid type supplied");
        }

        $currency_type = self::CURRENCY_TYPE_MAP[$type];

        try {

            $transaction_handshake = TransactionHandshake::query()->create([
                'user_id' => $leader_id,
                'rookie_id' => $metadata['rookie_id'],
                'amount' => $metadata['amount'],
                'dollar_amount' => $metadata['dollar_amount'],
                'type' => $type
            ]);

            $metadata['transaction_handshake_id'] = $transaction_handshake->id;
            $metadata['type'] = $type;
            $metadata['uuid'] = now()->timestamp . Str::uuid();

            $jpost_url = (new CcbillJpostForm($type, $currency_type, $metadata['dollar_amount'], $currency_code, $metadata))->getFormUrl();

            if(FakeData::isFakeCCBillActive()){
                $jpost_url = env('FRONTEND_URL') . '/payment-status';
                FakeData::fakeNewSalesSuccess($metadata);
            }

            $transaction_handshake->update([
                'jpost_url' => $jpost_url
            ]);

        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        return $jpost_url;
    }
}
