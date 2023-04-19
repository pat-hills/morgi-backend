<?php


namespace App\Ccbill;


use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CcbillSubscriptionManagement
{
    private $clientAccnum, $clientSubacc, $username, $password, $subscriptionId, $action;
    private $returnXML = 1;
    private $form_link = 'https://datalink.ccbill.com/utils/subscriptionManagement.cgi?';

    public function __construct($subscriptionId)
    {
        $ccbill_config = Config::get('ccbill.morgi');

        $this->clientAccnum = $ccbill_config['CLIENT_ACCNUM'];
        $this->clientSubacc = $ccbill_config['CLIENT_SUBACC'];
        $this->username = $ccbill_config['CCBILL_USERNAME'];
        $this->password = $ccbill_config['CCBILL_PASSWORD'];
        $this->subscriptionId = $subscriptionId;
    }

    public function voidOrRefund()
    {
        $this->action = 'voidOrRefundTransaction';

        $params = [
            'clientAccnum' => $this->clientAccnum,
            'usingSubacc' => $this->clientSubacc,
            'username' => $this->username,
            'password' => $this->password,
            'action' => $this->action,
            'subscriptionId' => $this->subscriptionId,
            'returnXML' => $this->returnXML
        ];

        $url = $this->form_link;

        $response = Http::get($url, $params)->getBody();

        $xml = simplexml_load_string($response);
        $json = json_encode($xml);

        return json_decode($json,true);
    }
}
