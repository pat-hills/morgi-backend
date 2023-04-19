<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customerly extends Model
{
    private function customerlyCurl($method, $url, $postdata = null)
    {
        $customerly_access_token = env('CUSTOMERLY_ACCESS_TOKEN');
        if(!$customerly_access_token){
            throw new \Exception('Miss Customerly access token');
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if($method == 'POST'){
            curl_setopt($ch, CURLOPT_POST,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: text/json;charset=UTF-8',
            'Access-Control-Allow-Origin: *',
            'Access-Control-Allow-Headers: content-type, authentication',
            "Authentication: AccessToken: ".env('CUSTOMERLY_ACCESS_TOKEN')
        ));

        $output = curl_exec($ch);
        $output = json_decode($output, true);

        curl_close($ch);

        return $output;
    }

    private function createUserOnCustomerly($user_id, $email, $name)
    {
        $url = "https://api.customerly.io/v1/users";

        $json_array = [
            'users' => [
                [
                    'email' => $email,  //Required. We use emails to identify a user
                    'user_id' => $user_id."_".uniqid('', true),  // Not required
                    'name' => $name,  // Not required
                ]
            ]
        ];

        $postdata = json_encode($json_array);

        try {
            $output = $this->customerlyCurl('POST', $url, $postdata);
        }catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

        return $output;
    }

    public function getUserID($email)
    {
        $email = urlencode($email);
        $url = "https://api.customerly.io/v1/users?email=$email";

        try {
            $output = $this->customerlyCurl('GET', $url);
        }catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

        if(!$this->isOutputGood($output)){
            return false;
        }

        return $output['data']['user_id'];
    }

    public function createUser($user_id, $email, $name)
    {
        $url = "https://api.customerly.io/v1/users";

        $json_array = [
            'users' => [
                [
                    'email' => $email,  //Required. We use emails to identify a user
                    'user_id' => $user_id."".rand(5000,70000),  // Not required
                    'name' => $name,  // Not required
                ]
            ]
        ];

        $postdata = json_encode($json_array);

        try {
            $output = $this->customerlyCurl('POST', $url, $postdata);
        }catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

        return $output;
    }

    public function sendMessage($id, $to_email, $message)
    {
        $url = "https://api.customerly.io/v1/messages";

        $json_array = array(
            "from" => array(
                "type" => "admin",
                "id" => 32595
            ),
            "to" => array(
                "type" => "user",
                "email" => $to_email,
                "id" => $id
            ),
            "content" => $message
        );

        $postdata = json_encode($json_array);

        try {
            $output = $this->customerlyCurl('POST', $url, $postdata);
        }catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

        return $output;
    }


    public function getUserData($email)
    {
        $email = urlencode($email);
        $url = "https://api.customerly.io/v1/users?email=$email";

        try {
            $output = $this->customerlyCurl('GET', $url);
        }catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

        if(!$this->isOutputGood($output)){
            return false;
        }

        return $output;
    }

    private  function isOutputGood($output)
    {
        return !array_key_exists('error', $output);
    }
}
