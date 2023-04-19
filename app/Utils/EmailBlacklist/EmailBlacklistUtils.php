<?php

namespace App\Utils\EmailBlacklist;

use App\Models\UserEmailBlacklist;

class EmailBlacklistUtils
{
    private $email;
    private $hashed_email;

    public function __construct(string $email)
    {
        $this->email = $email;
        $this->hashed_email = md5($this->email);
    }

    public function isBlacklisted(): bool
    {
        return UserEmailBlacklist::query()->where('email', $this->hashed_email)->exists();
    }

    public function firstOrCreate(): UserEmailBlacklist
    {
        $user_email_blacklist = UserEmailBlacklist::where('email', $this->hashed_email)->first();
        return $user_email_blacklist ?? UserEmailBlacklist::create(['email' => $this->hashed_email]);
    }

    public static function set(string $email): EmailBlacklistUtils
    {
        return new EmailBlacklistUtils($email);
    }
}
