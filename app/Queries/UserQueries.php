<?php

namespace App\Queries;

use App\Models\User;
use App\Models\UserLoginHistory;
use Carbon\Carbon;

class UserQueries
{
    private $user;

    public static function config(int $user_id): UserQueries
    {
        return new self($user_id);
    }

    public function __construct(int $user_id)
    {
        $this->user = User::find($user_id);
    }

    public function getLastLoginAt(): ?string
    {
        return $this->user->last_login_at;
    }

    public function getLoginCount(): int
    {
        return UserLoginHistory::query()
            ->where('user_id', $this->user->id)
            ->where('is_signup_values', false)
            ->count();
    }

    public function getCurrentSessionTimeInSeconds(): int
    {
        $last_login_at = $this->getLastLoginAt();
        $last_login_at = (isset($last_login_at)) ? strtotime($last_login_at) : 0;
        $now = now()->timestamp;

        return $now - $last_login_at;
    }
}
