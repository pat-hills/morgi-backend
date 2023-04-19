<?php


namespace App\Ccbill;


use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CcbillRebill
{
    private $clientAccnum, $clientSubacc, $username, $password, $newClientAccnum, $newClientSubacc, $currencyCode, $initialPrice, $subscriptionId;
    private $sharedAuthentication = 1;
    private $initialPeriod = 1;
    private $recurringPrice = 0;
    private $recurringPeriod = 0;
    private $rebills = 0;
    private $returnXML = 1;
    private $action = 'chargeByPreviousTransactionId';
    private $form_link = 'https://bill.ccbill.com/jpost/billingApi.cgi?';

    public function __construct($newClientAccnum, $newClientSubacc, $initialPrice, $subscriptionId, $currencyCode, $is_subscription)
    {
        $config = ($is_subscription) ? 'morgi' : 'micromorgi';
        $ccbill_config = Config::get("ccbill.$config");

        $this->clientAccnum = $ccbill_config['CLIENT_ACCNUM'];
        //$this->clientSubacc = $ccbill_config['CLIENT_SUBACC'];
        $this->username = $ccbill_config['CCBILL_USERNAME'];
        $this->password = $ccbill_config['CCBILL_PASSWORD'];
        $this->newClientAccnum = $newClientAccnum;
        $this->newClientSubacc = $ccbill_config['CLIENT_SUBACC'];
        $this->initialPrice = $initialPrice;
        $this->subscriptionId = $subscriptionId;
        $this->currencyCode = $currencyCode;
    }

    public function fetchCcbillRebillApi()
    {
        $params = [
            'clientAccnum' => $this->clientAccnum,
            'username' => $this->username,
            'password' => $this->password,
            'action' => $this->action,
            'newClientAccnum' => $this->newClientAccnum,
            'newClientSubacc' => $this->newClientSubacc,
            'sharedAuthentication' => $this->sharedAuthentication,
            'initialPrice' => $this->initialPrice,
            'initialPeriod' => $this->initialPeriod,
            'recurringPrice' => $this->recurringPrice,
            'recurringPeriod' => $this->recurringPeriod,
            'rebills' => $this->rebills,
            'subscriptionId' => $this->subscriptionId,
            'currencyCode' => $this->currencyCode,
            'returnXML' => $this->returnXML,
        ];

        if($params['subscriptionId']==='null' || $params['newClientAccnum']==='null' || $params['newClientSubacc']==='null' || $params['initialPrice']==='null'){
            throw new \Exception("CCBill exception: Bad rebill api configuration");
        }

        $url = $this->form_link;

        try {
            $response = Http::get($url, $params)->getBody();
        }catch (HttpClientException $exception){
            throw new \Exception("CCBill exception: $exception");
        }

        $xml = simplexml_load_string($response);
        $json = json_encode($xml);

        return json_decode($json, true);
    }

    public function getPayload()
    {
        $params = [
            'clientAccnum' => $this->clientAccnum,
            'clientSubacc' => $this->clientSubacc,
            'username' => $this->username,
            'password' => $this->password,
            'action' => $this->action,
            'newClientAccnum' => $this->newClientAccnum,
            'newClientSubacc' => $this->newClientSubacc,
            'sharedAuthentication' => $this->sharedAuthentication,
            'initialPrice' => $this->initialPrice,
            'initialPeriod' => $this->initialPeriod,
            'recurringPrice' => $this->recurringPrice,
            'recurringPeriod' => $this->recurringPeriod,
            'rebills' => $this->rebills,
            'subscriptionId' => $this->subscriptionId,
            'currencyCode' => $this->currencyCode,
            'returnXML' => $this->returnXML,
        ];

        if($params['subscriptionId']==='null' || $params['newClientAccnum']==='null' || $params['newClientSubacc']==='null' || $params['initialPrice']==='null'){
            throw new \Exception("CCBill exception: Bad rebill api configuration");
        }

        $url = $this->form_link;

        return $url . http_build_query($params);
    }
}
