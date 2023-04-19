<?php

namespace App\Services\Mailer;

use App\Models\Email;
use App\Models\Rookie;
use App\Models\User;
use App\Models\UserEmailSent;
use SendGrid;
use SendGrid\Mail\From;
use SendGrid\Mail\Mail;
use SendGrid\Mail\To;

class Mailer
{
    private $receiver = null;

    private $from;
    private $to = null;

    private $email = null;
    private $template_type = null;
    private $misc = null;

    private $sendgrid;
    private $response = null;


    public function __construct()
    {
        $this->sendgrid = new SendGrid(env('MAIL_PASSWORD'));
        $this->from = new From(
            env("MAIL_FROM_ADDRESS"),
            'Morgi'
        );
    }

    public function setReceiver(MailerReceiver $receiver): Mailer
    {
        if(!$receiver->canSendEmail()){
            throw new \Exception("Cannot send email to {$receiver->email}");
        }

        $this->receiver = $receiver;

        return $this;
    }

    public function setMisc(array $misc = []): Mailer
    {
        /*
         * Default misc values
         */
        $misc['facebook_link'] = env('FACEBOOK_LINK');
        $misc['telegram_link'] = env('TELEGRAM_LINK');
        $misc['instagram_link'] = env('INSTAGRAM_LINK');
        $misc['homepage_url'] = env('FRONTEND_URL');

        $misc['unsubscribe_link'] = (isset($this->receiver->unsubscribe_token)) ? env('FRONTEND_URL') . env('UNSUBSCRIBE_FRONTEND_PATH') . $this->receiver->unsubscribe_token : null;

        if(!array_key_exists('name', $misc) || !isset($misc['name'])){
            $misc['name'] = ($this->receiver->type==='leader')
                ? $this->receiver->full_name
                : $this->receiver->first_name;
        }

        $this->misc = $misc;
        return $this;
    }

    public function setTemplate(string $template_type): Mailer
    {
        $template = Email::query()->select('sendgrid_id')->where('type', $template_type)->first();
        if(!isset($template)){
            throw new \Exception("Unable to retrieve email template");
        }

        $this->template_type = $template_type;
        $this->to = [
            new To(
                $this->receiver->email,
                $this->receiver->full_name,
                $this->misc
            )
        ];

        $this->email = (new Mail(
            $this->from,
            $this->to
        ));

        $this->email->setTemplateId($template->sendgrid_id);

        return $this;
    }

    public function send(int $send_at = null): Mailer
    {
        if(!isset($this->email)){
            throw new \Exception("Unable to send email, template was not set");
        }

        if(isset($send_at)){
            try {
                $this->email->setSendAt($send_at);
            }catch (\Exception $exception){
            }
        }

        try {
            $this->response = $this->sendgrid->send($this->email);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

        return $this;
    }

    public function sendAndCreateUserEmailSentRow(int $send_at = null): Mailer
    {
        try {
            $this->send($send_at);
            UserEmailSent::create([
                'user_id' => $this->receiver->id,
                'type' => $this->template_type,
                'sent' => $sent = $this->response->statusCode()===202,
                'sendgrid_message_id' => ($sent) ? $this->response->headers(true)['X-Message-Id'] : null,
                'errors' => (!$sent) ? $this->response->body() : null
            ]);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        return $this;
    }

    public function getResponse(): ?SendGrid\Response
    {
        return $this->response;
    }

    public static function create(User $user = null, string $email = null): Mailer
    {
        try {
            $receiver = new MailerReceiver($user, $email);
            $mailer = (new Mailer())->setReceiver($receiver);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        return $mailer;
    }
}
