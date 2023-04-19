<?php


namespace App\Sendgrid;


use App\Models\EmailValidated;
use App\Utils\EmailBlacklist\EmailBlacklistUtils;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Facades\Http;

class SendgridAPI
{
    const EMAIL_VALIDATION_PATH = 'validations/email';
    const SPAM_REPORTS_PATH = 'suppression/spam_reports';
    const BLOCKS_PATH = 'suppression/blocks';
    const INVALID_EMAILS_PATH = 'suppression/invalid_emails';
    const BOUNCES_PATH = 'suppression/bounces';

    private $API_KEY;

    private $API_BASE_URL = "https://api.sendgrid.com/v3/";

    public function __construct($type = null)
    {
        if($type==='email_validation'){
            $this->API_KEY = env('SENDGRID_API_KEY');
            return;
        }

        $this->API_KEY = env('MAIL_PASSWORD');
    }

    public function emailValidation($email, $source)
    {
        $url = $this->API_BASE_URL . self::EMAIL_VALIDATION_PATH;
        $params = [
            'email' => $email,
            'source' => $source
        ];

        try {
            $response_body = Http::contentType('application/json')
                ->withToken($this->API_KEY)
                ->post($url, $params);
        }catch (HttpClientException $e){
            return ['status' => 'error', 'error' => 'curl error'];
        }

        if($response_body->status()!==200){
            return ['status' => 'error', 'error' => 'curl error'];
        }

        $response = json_decode($response_body->getBody())->result;

        $email_validated_attributes = [
            'email' => $response->email, 'verdict' => strtolower($response->verdict), 'score' => round($response->score, 2),
            'local' => $response->local, 'host' => $response->host, 'suggestion' => $response->suggestion ?? null,
            'has_valid_address_syntax' => $response->checks->domain->has_valid_address_syntax,
            'has_mx_or_a_record' => $response->checks->domain->has_mx_or_a_record, 'is_suspected_disposable_address' => $response->checks->domain->is_suspected_disposable_address,
            'is_suspected_role_address' => $response->checks->local_part->is_suspected_role_address, 'has_known_bounces' => $response->checks->additional->has_known_bounces,
            'has_suspected_bounces' => $response->checks->additional->has_suspected_bounces, 'source' => $response->source ?? null, 'ip_address' => $response->ip_address
        ];

        $email_validated = EmailValidated::create($email_validated_attributes);

        if($email_validated->verdict!=='valid'){
            EmailBlacklistUtils::set($email_validated->email)->firstOrCreate();
            return ['status' => 'invalid'];
        }

        return ['status' => true, 'email_validated' => $email_validated];
    }

    public function spamReports($start_time, $end_time)
    {
        $url = $this->API_BASE_URL . self::SPAM_REPORTS_PATH;
        $params = [
            'start_time' => $start_time,
            'end_time' => $end_time
        ];

        try {
            $response_body = Http::contentType('application/json')
                ->withToken($this->API_KEY)
                ->get($url, $params);
        }catch (HttpClientException $e){
            return ['status' => 'error', 'error' => 'curl error', 'e' => $e];
        }

        if($response_body->status()!==200){
            return ['status' => 'error', 'error' => 'curl error'];
        }

        return ['status' => true, 'data' => json_decode($response_body->getBody())];
    }

    public function blocks($start_time, $end_time)
    {
        $url = $this->API_BASE_URL . self::BLOCKS_PATH;
        $params = [
            'start_time' => $start_time,
            'end_time' => $end_time
        ];

        try {
            $response_body = Http::contentType('application/json')
                ->withToken($this->API_KEY)
                ->get($url, $params);
        }catch (HttpClientException $e){
            return ['status' => 'error', 'error' => 'curl error', 'e' => $e];
        }

        if($response_body->status()!==200){
            return ['status' => 'error', 'error' => 'curl error'];
        }

        return ['status' => true, 'data' => json_decode($response_body->getBody())];
    }

    public function invalidEmails($start_time, $end_time)
    {
        $url = $this->API_BASE_URL . self::INVALID_EMAILS_PATH;
        $params = [
            'start_time' => $start_time,
            'end_time' => $end_time
        ];

        try {
            $response_body = Http::contentType('application/json')
                ->withToken($this->API_KEY)
                ->get($url, $params);
        }catch (HttpClientException $e){
            return ['status' => 'error', 'error' => 'curl error', 'e' => $e];
        }

        if($response_body->status()!==200){
            return ['status' => 'error', 'error' => 'curl error'];
        }

        return ['status' => true, 'data' => json_decode($response_body->getBody())];
    }

    public function bounces($start_time, $end_time)
    {
        $url = $this->API_BASE_URL . self::BOUNCES_PATH;
        $params = [
            'start_time' => $start_time,
            'end_time' => $end_time
        ];

        try {
            $response_body = Http::contentType('application/json')
                ->withToken($this->API_KEY)
                ->get($url, $params);
        }catch (HttpClientException $e){
            return ['status' => 'error', 'error' => 'curl error', 'e' => $e];
        }

        if($response_body->status()!==200){
            return ['status' => 'error', 'error' => 'curl error'];
        }

        return ['status' => true, 'data' => json_decode($response_body->getBody())];
    }
}
