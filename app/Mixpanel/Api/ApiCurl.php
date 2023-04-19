<?php

namespace App\Mixpanel\Api;

use Illuminate\Support\Facades\Http;

class ApiCurl
{
    private $base_url = 'https://api.mixpanel.com/';
    protected $action;
    protected $headers = [];
    protected $body = [];
    protected $method = 'post';

    public static function config(): ApiCurl
    {
        return new self();
    }

    public function setMethod(string $method): ApiCurl
    {
        $this->method = $method;
        return $this;
    }

    public function setAction(string $action): ApiCurl
    {
        $this->action = $action;
        return $this;
    }

    public function setBody(array $body): ApiCurl
    {
        $this->body = $body;
        return $this;
    }

    public function pushHeader(string $name, string $value): ApiCurl
    {
        $this->headers[$name] = $value;
        return $this;
    }

    private function getUrl(): string
    {
        return $this->base_url . $this->action;
    }

    public function exec(): void
    {
        try {
            if($this->method === 'get'){
                $response = Http::withHeaders($this->headers)
                    ->get($this->getUrl(), $this->body)
                    ->body();
            }

            if($this->method === 'post'){
                $response = Http::withHeaders($this->headers)
                    ->post($this->getUrl(), $this->body)
                    ->body();
            }
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        $response = json_decode($response);
        if($response === 0){
            throw new \Exception("Mixpanel returned an error");
        }
    }
}
