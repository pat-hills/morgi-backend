<?php

namespace App\Services\Mailer;

use App\Models\Rookie;
use App\Models\User;
use App\Utils\EmailBlacklist\EmailBlacklistUtils;

class MailerReceiver
{
    public $id = null;
    public $type = null;
    public $first_name = null;
    public $last_name = null;
    public $unsubscribe_token = null;

    public $full_name;
    public $email;

    public function __construct(User $user = null, string $email = null)
    {
        if(!isset($user) && !isset($email)){
            throw new \Exception("Unable to create MailerReceiver");
        }

        if(isset($user)){

            $this->id = $user->id;
            $this->type = $user->type;
            $this->full_name = $user->full_name;
            $this->email = $user->email;
            $this->unsubscribe_token = $user->unsubscribe_token;

            $rookie_user = ($this->type==='rookie') ? Rookie::find($this->id) : null;
            if(isset($rookie_user)){
                $this->first_name = $rookie_user->first_name;
                $this->last_name = $rookie_user->last_name;
            }
        }

        if(isset($email)){
            $this->full_name = $email;
            $this->email = $email;
        }
    }

    public function canSendEmail(): bool
    {
        return !EmailBlacklistUtils::set($this->email)->isBlacklisted();
    }
}
