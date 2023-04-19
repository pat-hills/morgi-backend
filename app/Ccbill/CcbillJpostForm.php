<?php


namespace App\Ccbill;


use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;

class CcbillJpostForm
{
    private const GIFT_TEXT = "Gift {{rookie_first_name}} with {{currency}}{{amount}}";
    private const UPDATE_CARD_TEXT = "Update card details";
    private const BUY_MICROMORGI_TEXT = "Buy Micro Morgis package of {{currency}}{{amount}}";

    private const GIFT_SUBTEXT = "{{currency}}{{amount}} will be charged on the same date each month from now unless cancelled or modified";
    private const UPDATE_CARD_SUBTEXT = "";
    private const BUY_MICROMORGI_SUBTEXT = "";


    private const GIFT_CTA = "Gift {{rookie_first_name}}";
    private const UPDATE_CARD_CTA = "Save updates";
    private const BUY_MICROMORGI_CTA = "Buy Micro Morgi Package";

    private const FOOTER = "Please note that the updated card will be used for all recurring gifts marked by you on the previous page, and for all your future gifts";

    private const TAGS = [
        '{{rookie_first_name}}' => 'rookie_first_name',
        '{{currency}}' => 'currency',
        '{{amount}}' => 'formPrice'
    ];

    private const CUSTOM_FORM_DATA = [
        'micromorgi' => [
            'text' => self::BUY_MICROMORGI_TEXT,
            'subtext' => self::BUY_MICROMORGI_SUBTEXT,
            'cta' => self::BUY_MICROMORGI_CTA,
            'footer' => self::FOOTER,
        ],
        'gift' => [
            'text' => self::GIFT_TEXT,
            'subtext' => self::GIFT_SUBTEXT,
            'cta' => self::GIFT_CTA,
            'footer' => self::FOOTER,
        ],
        'renew' => [
            'text' => self::GIFT_TEXT,
            'subtext' => self::GIFT_SUBTEXT,
            'cta' => self::GIFT_CTA,
            'footer' => self::FOOTER,
        ],
        'credit_card' => [
            'text' => self::UPDATE_CARD_TEXT,
            'subtext' => self::UPDATE_CARD_SUBTEXT,
            'cta' => self::UPDATE_CARD_CTA,
            'footer' => self::FOOTER,
        ],
    ];


    private $clientAccnum, $clientSubacc, $formName, $formPrice, $currencyCode, $formDigest, $salt, $metadata, $type, $currency, $rookie_first_name;
    private $form_link = "https://bill.ccbill.com/jpost/signup.cgi?";
    private $formPeriod = 0;

    public function __construct($type, $currency_type, $formPrice, $currencyCode, $metadata)
    {
        $ccbill_config = ($type==='credit_card')
            ? Config::get("ccbill.credit_card")
            : Config::get("ccbill.$currency_type");

        $this->clientAccnum = $ccbill_config['CLIENT_ACCNUM'];
        $this->clientSubacc = $ccbill_config['CLIENT_SUBACC'];
        $this->salt = $ccbill_config['CCBILL_SALT'];
        $this->formName = $ccbill_config['CCBILL_FORM_NAME'];
        $this->formPrice = $formPrice;
        $this->currencyCode = $currencyCode;
        $this->currency = CcbillCurrencyCodes::getCurrencySymbol($currencyCode);
        $this->type = $type;
        $this->rookie_first_name = $metadata['rookie_first_name'] ?? "";

        $digest_string = $formPrice . $this->formPeriod . $currencyCode . $this->salt;
        $digest = md5($digest_string);
        $this->formDigest = $digest;

        $this->metadata = Crypt::encryptString(json_encode($metadata));
    }

    public function getFormUrl(): string
    {

        $form_url = $this->form_link . 'clientAccnum=' . $this->clientAccnum . '&clientSubacc=' . $this->clientSubacc . '&formName=' . $this->formName
            . '&formPrice=' . $this->formPrice . '&formPeriod=' . $this->formPeriod . '&currencyCode=' . $this->currencyCode . '&formDigest=' . $this->formDigest
            . '&metadata=' . $this->metadata;

        /*
         * Add custom form data to customize it
         */
        $form_url .= $this->mapFormData();

        return $form_url;
    }

    private function mapFormData(): string
    {
        $query_params = "";
        $tags = self::TAGS;
        $form_data = self::CUSTOM_FORM_DATA[$this->type];


        $tags_to_find = array_keys($tags);
        $tags_to_replace = array_map(function ($item){
            return $this->$item;
        }, array_values($tags));

        foreach ($form_data as $key => $data){

            $content = str_replace($tags_to_find, $tags_to_replace, $data);
            $query_params .= "&$key=$content";
        }

        return $query_params;
    }
}
