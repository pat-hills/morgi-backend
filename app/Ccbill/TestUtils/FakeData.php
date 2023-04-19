<?php

namespace App\Ccbill\TestUtils;

use App\Logger\Logger;
use App\Models\LeaderCcbillData;
use App\Webhooks\Ccbill\CCbillWebhook;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class FakeData
{
    public static function isFakeCCBillActive(): bool
    {
        return env('APP_ENV') !== 'prod' && env('FAKE_CCBILL_ACTIVE', false) === true;
    }

    public static function fakeRefund(string $leader_payment_id): void
    {

        $data = [
            'accountingAmount' => '50.00',
            'accountingCurrency' => 'USD',
            'accountingCurrencyCode' => '840',
            'amount' => '50.00',
            'cardType' => 'VISA',
            'clientAccnum' => '948001',
            'clientSubacc' => '0090',
            'currency' => 'USD',
            'currencyCode' => '840',
            'expDate' => '1227',
            'last4' => '1111',
            'paymentAccount' => '693a3b8d0d888c3d04800000004bacd',
            'paymentType' => 'CREDIT',
            'reason' => 'Refunded through Data Link: subscriptionManagement.cgi',
            'subscriptionId' => 'CCBILL-INTERNAL-TEST',
            'timestamp' => '2022-02-22 11:29:04',
            'transactionId' => 'CCBILL-INTERNAL-TEST',
            'eventType' => 'Refund',
            'eventGroupType' => 'Subscription',
            'leader_payment_id' => $leader_payment_id
        ];

        $fake_request = new Request();
        $fake_request = $fake_request->merge($data);

        $webhook_response = (new CCbillWebhook())->store($fake_request);
        if($webhook_response->getStatusCode() !== 200){
            Logger::logMessage("Fake webhook returned: {$webhook_response->getContent()}");
            throw new \Exception("Fake webhook returned: {$webhook_response->getContent()}");
        }
    }

    public static function fakeNewSalesSuccess(array $metadata): void
    {
        $encrypted_metadata = Crypt::encryptString(
            json_encode($metadata)
        );

        $data = [
            "accountingCurrency" => "USD",
            "accountingCurrencyCode" => "840",
            "accountingInitialPrice" => $metadata['dollar_amount'],
            "accountingRecurringPrice" => "0",
            "billedCurrency" => "USD",
            "billedCurrencyCode" => "840",
            "billedInitialPrice" => $metadata['dollar_amount'],
            "billedRecurringPrice" => "0",
            "bin" => "411111",
            "cardType" => "VISA",
            "clientAccnum" => "948001",
            "clientSubacc" => "0093",
            "country" => "IT",
            "dynamicPricingValidationDigest" => "00881eb43c65fe5f50aef5b34fee0e3c",
            "email" => "ccbill.internal.test@kromin.it",
            "expDate" => "0624",
            "firstName" => "CCBILL-INTERNAL-TEST",
            "formName" => "948001-0093cc-1",
            "initialPeriod" => "0",
            "ipAddress" => "127.0.0.1",
            "last4" => "1111",
            "lastName" => "CCBILL-INTERNAL-TEST",
            "paymentAccount" => "CCBILL-INTERNAL-TEST",
            "paymentType" => "CREDIT",
            "postalCode" => "ad400",
            "priceDescription" => "{$metadata['dollar_amount']} monthly (recurring payment)",
            "rebills" => "0",
            "recurringPeriod" => "0",
            "referringUrl" => env('FRONTEND_URL'),
            "state" => "XX",
            "subscriptionCurrency" => "USD",
            "subscriptionCurrencyCode" => "840",
            "subscriptionId" => "CCBILL-INTERNAL-TEST",
            "subscriptionInitialPrice" => $metadata['dollar_amount'],
            "subscriptionTypeId" => "0",
            "timestamp" => now()->toDateTimeString(),
            "transactionId" => "CCBILL-INTERNAL-TEST",
            "X-metadata" => $encrypted_metadata,
            "eventType" => "NewSaleSuccess",
            "eventGroupType" => "Subscription",
            'mirtillo' => "mirtilloDebug"
        ];

        $fake_request = new Request();
        $fake_request = $fake_request->merge($data);

        $webhook_response = (new CCbillWebhook())->store($fake_request);
        if($webhook_response->getStatusCode() !== 200){
            Logger::logMessage("Fake webhook returned: {$webhook_response->getContent()}");
            throw new \Exception("Fake webhook returned: {$webhook_response->getContent()}");
        }

        /*$url = env('APP_URL') . '/v2/ccbill/webhook';
        try {
            $response = Http::post($url, $data)->getBody();
        }catch (HttpClientException $exception){
            Logger::logMessage('Unable to fake ccbill webhook');
        }*/
    }

    public static function createFakeLeaderCcbillData(int $leader_id): LeaderCcbillData
    {
        return LeaderCcbillData::create([
            'leader_id' => $leader_id,
            'active' => true,
            'subscriptionId' => 'CCBILL-INTERNAL-TEST',
            'transactionId' => 'CCBILL-INTERNAL-TEST',
            'last4' => '1234',
            'expDate' => '0623',
            'cardType' => 'VISA',
            'subscriptionCurrencyCode' => '840',
        ]);
    }

    public static function createOrActivateFakeLeaderCcbillData(int $leader_id): LeaderCcbillData
    {
        $leader_ccbill_data = LeaderCcbillData::where('leader_id', $leader_id)
            ->where('subscriptionId', 'CCBILL-INTERNAL-TEST')
            ->where('active', true)
            ->first();

        return $leader_ccbill_data ?? LeaderCcbillData::create([
                'leader_id' => $leader_id,
                'active' => true,
                'subscriptionId' => 'CCBILL-INTERNAL-TEST',
                'transactionId' => 'CCBILL-INTERNAL-TEST',
                'last4' => '1234',
                'expDate' => '0623',
                'cardType' => 'VISA',
                'subscriptionCurrencyCode' => '840',
            ]);
    }
}
