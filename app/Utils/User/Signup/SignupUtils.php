<?php

namespace App\Utils\User\Signup;

use App\Logger\Logger;
use App\Mixpanel\Events\EventSignupSuccess;
use App\Mixpanel\Utils\UserProfileUtils;
use App\Models\Country;
use App\Models\User;
use App\Models\UserABGroup;
use App\Models\UserReferralEmailsSent;
use App\Services\Chat\Chat;
use App\Services\Mailer\Mailer;
use App\Utils\NotificationUtils;
use App\Utils\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SignupUtils
{
    private $request;
    private $handler;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->handler = ($this->request->type==='rookie')
            ? new RookieSignupUtils($this->request)
            : new LeaderSignupUtils($this->request);
    }

    public function validate(): void
    {
        try {
            $this->handler->validate();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    public function signup(): User
    {
        /*
         * Create new row in users table and rookies or leaders table
         */
        try {
            $user = $this->createUser();
            $this->handler->create($user);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        try {
            UserProfileUtils::storeOrUpdate($user->id);
            EventSignupSuccess::config($user->id);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        /*
         * Create the user on PubNub
         */
        Chat::config($user->id)->userSignup($user);

        /*
         * Do refer action like start chat between rookie and leader
         */
        $this->doReferActions($user);

        /*
         * Store signup data
         */
        $user->newLogin(Utils::getRealIp($this->request), $this->request->header('User-Agent'), true);

        /*
         * Send signup activate email if signup source is morgi
         */
        if($user->signup_source === 'morgi'){
            try {
                self::sendSignupActivateEmail($user);
            }catch (\Exception $exception){
            }
        }

        try {
            NotificationUtils::sendNotification($user->id, "{$user->type}_login", now());
            if($user->type === 'rookie'){
                NotificationUtils::sendNotification($user->id, 'telegram_bot', now());
            }
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return $user;
    }

    private function createUser(): User
    {
        $country = $this->getCountryByIp();

        $user_attributes = [
            'cookie_policy' => true,
            'type' => $this->request->type,
            'email' => $this->request->email,
            'gender_id' => $this->request->gender_id,
            'currency' => $country->currency,
            'signup_country_id' => $country->id,
            'persona' => $this->request->persona,
            'advertisement_id' => $this->request->advertisement_id,
            'signup_source' => $this->getSignupSource(),
            'password' => bcrypt($this->request->password),
            'referral_code' => rand(1, 10000) . Str::uuid()->toString(),
            'pubnub_uuid' => Str::orderedUuid()->toString(),
            'username' => $this->generateUsernameFromEmail(),
            'group_id' => $this->getGroupId(),
            'referred_by' => $this->getReferredBy(),
            'activation_token' => $this->generateRandomToken(),
            'unsubscribe_token' => $this->generateRandomToken(),
            'telegram_bot_token' => $this->generateRandomToken()
        ];

        try {
            $user = User::create($user_attributes);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        try {
            $chat_topics_ids =$this->request->input('chat_topics_ids');
            $favorite_activities_ids =$this->request->input('favorite_activities_ids');
            
            if(isset($chat_topics_ids) && !empty($chat_topics_ids))
            {
                $user->chatTopicsSaved()->attach($chat_topics_ids);
            }

            if(isset($favorite_activities_ids) && !empty($favorite_activities_ids))
            {
                $user->favoriteActivitiesSaved()->attach($favorite_activities_ids);
            } 
            
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return $user;
    }

    private function getSignupSource(): string
    {
        if(isset($this->request->signup_source) && in_array($this->request->signup_source, ['facebook', 'google'])){
            return $this->request->signup_source;
        }

        return 'morgi';
    }

    private function getReferredBy(): ?int
    {
        if(!isset($this->request->referral_code)){
            return null;
        }

        $referrer_user = User::where('username', $this->request->referral_code)->where('active', true)->first();
        if(!isset($referrer_user)){
            return null;
        }

        return $referrer_user->id;
    }

    private function doReferActions(User $user): void
    {
        if(!isset($user->referred_by)){
            return;
        }

        $referrer_user = User::where('id', $user->referred_by)->where('active', true)->first();
        if(!isset($referrer_user)){
            return;
        }

        $user_referral_email = UserReferralEmailsSent::where('user_id', $referrer_user->id)
            ->where('email', $user->email)
            ->first();
        if(isset($user_referral_email)){
            $user_referral_email->update([
                'user_joined' => true,
                'referred_user_id' => $user->id
            ]);
        }

        /*
         * If rookie referred leader start chat between rookie and leader
         */
        if($referrer_user->type==='rookie' && $user->type==='leader'){
            try {
                $user_referral_email_id = (isset($user_referral_email)) ? $user_referral_email->id : null;
                Chat::config($user->id)->startDirectChat($user, $referrer_user, null, $user_referral_email_id, true);
            } catch (\Exception $e) {
            }
        }
    }

    private function getGroupId(): int
    {
        return $this->request->public_group ?? UserABGroup::query()->inRandomOrder()->first()->id;
    }

    private function generateRandomToken(): string
    {
        return md5(
            rand(1, 10000) . uniqid('', true) . rand(1, 10000)
        );
    }

    private function getCountryByIp(): Country
    {
        $country_name_by_ip = Utils::ipInfo(
            Utils::getRealIp($this->request)
        );

        return Country::where('name', $country_name_by_ip)->first() ?? Country::where('name', 'United States')->first();
    }

    private function generateUsernameFromEmail(): string
    {
        /*
         * Take the first part of user's email
         */
        $raw_username = explode('@', $this->request->email)[0];

        /*
         * Remove white spaces and invalid chars
         */
        $raw_username = strtolower(str_replace(
            ' ', '', preg_replace('/[^A-Za-z0-9_.]/', '', $raw_username)
        ));

        /*
         * Check if username already exists and make it unique
         */
        $username = $raw_username;
        $counter = 0;
        while (User::where('username', $username)->exists()){
            $counter++;
            $username = $raw_username . $counter;
        }

        return $username;
    }

    public static function sendSignupActivateEmail(User $user): void
    {
        $activation_link = env('FRONTEND_URL') . env('SIGNUP_ACTIVATE_FRONTEND_PATH') . $user->activation_token;
        try {
            Mailer::create($user)->setMisc(['activation_link' => $activation_link])->setTemplate('ACCOUNT_ACTIVATION')->sendAndCreateUserEmailSentRow();
        }catch (\Exception $exception){
            Logger::logException($exception);
        }
    }

}
