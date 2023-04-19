<?php

namespace App\Models;

use App\Enums\PubnubGroupEnum;
use App\Http\Resources\GoalResource;
use App\Logger\Logger;
use App\Models\ChatTopic;
use App\Models\FavoriteActivity;
use App\Mixpanel\Events\EventDisconnectTelegramSuccess;
use DateTime;
use Carbon\Carbon;
use App\Telegram\TelegramUtils;
use App\Http\Resources\ProfileAlertCollection;
use App\Mixpanel\Events\EventDeleteAccountSuccess;
use App\Services\Chat\Chat;
use App\Services\Mailer\Mailer;
use App\Utils\EmailBlacklist\EmailBlacklistUtils;
use App\Utils\NotificationUtils;
use App\Utils\UserUtils;
use Illuminate\Http\Request;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'type', 'status', 'email', 'email_verified_at', 'password', 'remember_token', 'activation_token',
        'active', 'language', 'group_id', 'referral_code', 'referred_by', 'last_login_at', 'last_activity_at', 'username', 'unsubscribe_token',
        'description', 'gender_id', 'admin_check', 'admin_id', 'decline_reason', 'cookie_policy', 'currency', 'pubnub_uuid',
        'signup_country_id', 'sent_first_message', 'clicked_facebook_link', 'clicked_telegram_link', 'clicked_instagram_link', 'password_forced',
        'updated_username', 'telegram_bot_token', 'joined_telegram_bot_at', 'telegram_username', 'telegram_user_id', 'telegram_chat_id',
        'total_subscriptions_count', 'last_daily_login_at', 'referral_bonus_transaction_id', 'persona', 'advertisement_id',
        'has_face_processed', 'facebook_id', 'google_id', 'signup_source', 'total_successful_paused_connections', 'total_paused_connections', 'can_receive_telegram_message'
    ];

    protected $hidden = [
        'password', 'remember_token', 'unsubscribe_token',
        'activation_token', 'email_verified_at', 'last_activity_at', 'last_login_at', 'referral',
        'active', 'updated_at', 'admin_check', 'updated_fields', 'pubnub_uuid',
        'admin_id', 'decline_reason', 'cookie_policy', 'deleted_at'
    ];

    protected $casts = [
        'sent_first_message' => 'boolean',
        'active' => 'boolean',
        'cookie_policy' => 'boolean',
        'admin_check' => 'boolean',
        'password_forced' => 'boolean',
        'has_face_processed' => 'boolean'
    ];

    public function getGender()
    {
        return Gender::where('id', $this->gender_id)->first();
    }

    public function getOwnDescription()
    {
        $description_history = UserDescriptionHistory::query()
            ->where('user_id', $this->id)
            ->where('status', 'to_check')
            ->latest()
            ->first();

        if(isset($description_history)){
            return $description_history->description;
        }

        return $this->description;
    }

    public function getFullNameAttribute(): ?string
    {
        if($this->type === 'rookie'){
            $rookie = Rookie::find($this->id);
            return (isset($rookie)) ? $rookie->full_name : null;
        }

        return $this->username;
    }

    public function getIdVerifiedAttribute()
    {
        $id = UserIdentityDocument::where('user_id', $this->id)->first();

        if(isset($id)){

            $front = (isset($id->front_photo))
                ? ['status' => $id->front_photo->status, 'url' => $id->front_photo->url,
                    'path_location' => $id->front_photo->path_location, 'reason' => $id->front_photo->decline_reason]
                : null;
            $back = (isset($id->back_photo))
                ? ['status' => $id->back_photo->status, 'url' => $id->back_photo->url,
                    'path_location' => $id->back_photo->path_location, 'reason' => $id->back_photo->decline_reason]
                : null;
            $selfie = (isset($id->selfie_photo))
                ? ['status' => $id->selfie_photo->status, 'url' => $id->selfie_photo->url,
                    'path_location' => $id->selfie_photo->path_location, 'reason' => $id->selfie_photo->decline_reason]
                : null;

            $response = [
                'card_id_status' => 'approved',
                'card_id_reason' => null,
                'front' => $front,
                'back' => $back,
                'selfie' => $selfie
            ];

            return $response;
        }

        $id_validation = UserIdentityDocumentHistory::where('user_id', $this->id)->latest()->first();

        if(isset($id_validation)){

            $front = (isset($id_validation->front_photo))
                ? ['status' => $id_validation->front_photo->status, 'url' => $id_validation->front_photo->url,
                    'path_location' => $id_validation->front_photo->path_location, 'reason' => $id_validation->front_photo->decline_reason]
                : null;
            $back = (isset($id_validation->back_photo))
                ? ['status' => $id_validation->back_photo->status, 'url' => $id_validation->back_photo->url,
                    'path_location' => $id_validation->back_photo->path_location, 'reason' => $id_validation->back_photo->decline_reason]
                : null;
            $selfie = (isset($id_validation->selfie_photo))
                ? ['status' => $id_validation->selfie_photo->status, 'url' => $id_validation->selfie_photo->url,
                    'path_location' => $id_validation->selfie_photo->path_location, 'reason' => $id_validation->selfie_photo->decline_reason]
                : null;

            $response = [
                'card_id_status' => $id_validation->status,
                'card_id_reason' => $id_validation->reason,
                'front' => $front,
                'back' => $back,
                'selfie' => $selfie
            ];

            return $response;
        }

        return null;
    }

    public function getOwnAvatar()
    {
        $validation_photo = PhotoHistory::where('user_id', $this->id)
            ->where('main', true)
            ->where('status', 'to_check')
            ->latest()
            ->first();

        if($validation_photo){
            return $validation_photo->append('under_validation');
        }

        $photo = Photo::where('user_id', $this->id)->where('main', true)->first();

        return ($photo) ? $photo->append('under_validation') : null;
    }

    public function getPublicAvatar()
    {
        $photo = Photo::where('user_id', $this->id)->where('main', true)->first();
        return (isset($photo)) ? $photo->append('under_validation') : null;
    }

    public function setDescription(string $description = null): void
    {
        if($this->description===$description){
            return;
        }

        if(empty($description)){
            $this->update(['description' => null]);
            UserDescriptionHistory::where('user_id', $this->id)->where('status', 'to_check')->update(['status' => 'no_action']);
            UserDescriptionHistory::create(['user_id' => $this->id, 'description' => null, 'status' => 'no_action']);
            return;
        }

        UserDescriptionHistory::create([
            'user_id' => $this->id,
            'description' => $description
        ]);

        $this->update(['admin_check' => true]);
    }

    public function addPhoto(string $path_location, bool $main = false)
    {
        if(!isset($path_location)){
            throw new \Exception("Error in you path location");
        }

        $photos_count = Photo::where('user_id', Auth::id())->count() + PhotoHistory::where('user_id', Auth::id())->where('status', 'to_check')->count();
        if($photos_count>=10){
            throw new \Exception("Max photos count reached");
        }

        if($main){
            PhotoHistory::where('user_id', $this->id)->update(['main' => false]);
        }

        $this->update(['admin_check' => true]);

        return PhotoHistory::create(['user_id' => $this->id, 'path_location' => $path_location, 'main' => $main]);
    }

    public function removeAvatar()
    {
        $avatar = $this->getOwnAvatar();
        if(!isset($avatar)){
            return;
        }

        if($avatar->under_validation){
            PhotoHistory::query()->find($avatar->id)->delete();
            return;
        }

        Photo::query()->find($avatar->id)->delete();
    }


    public function getIsOnlineAttribute(): bool
    {
        return strtotime($this->last_activity_at) > Carbon::now()->subMinutes(env('IS_ONLINE_RANGE_IN_MINUTES',1440))->timestamp
            && isset($this->joined_telegram_bot_at);
    }

    public function newLogin(string $ip, string $user_agent, bool $is_signup): void
    {
        UserLoginHistory::create([
            'user_id' => $this->id,
            'ip_address' => $ip ?? '127.0.0.1',
            'user_agent' => $user_agent ?? 'Undefined',
            'is_signup_values' => $is_signup
        ]);

        if(!$is_signup){
            $this->update(['last_login_at' => now()]);
        }
    }

    public function emailsSentByTypeCount(string $type)
    {
        return UserEmailSent::where('user_id', $this->id)->where('type', $type)->count();
    }

    public function removeUserData()
    {
        $new_email = UserUtils::genUnicUnknowEmail();
        $new_username = UserUtils::genUnicUnknowUsername();

        try {
            EventDeleteAccountSuccess::config($this->id);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        try {
            EmailBlacklistUtils::set($this->email)->firstOrCreate();

            if(isset($this->joined_telegram_bot_at, $this->telegram_chat_id)){
                TelegramUtils::sendTelegramNotifications($this->telegram_chat_id, 'disconnect', null, $this->id);
            }

            $this->createUserStatusHistory('deleted', 'USER');
            $this->update([
                'status' => 'deleted',
                'email' => $new_email,
                'username' => $new_username,
                'active' => 0,
                'activation_token' => null,
                'unsubscribe_token' => null,
                'description' => 'unknow',
                'joined_telegram_bot_at' => null,
                'telegram_chat_id' => null,
                'telegram_user_id' => null,
                'telegram_username' => null,
                'gender_id' => Gender::query()->where('key_name', 'unknown')->first()->id ?? 1
            ]);

            $channels_ids = PubnubChannel::query()->where("{$this->type}_id", $this->id)->pluck('id')->toArray();
            PubnubGroupChannel::query()->whereIn('channel_id', $channels_ids)->delete();
            PubnubChannel::query()->whereIn('id', $channels_ids)->delete();
            PubnubGroup::query()->where('user_id', $this->id)->delete();

            Photo::query()->where('user_id', $this->id)->delete();
            Video::query()->where('user_id', $this->id)->delete();
            UserPath::query()->where('user_id', $this->id)->delete();

            switch ($this->type){
                case 'rookie':
                    Rookie::query()->find($this->id)->update([
                        'first_name' => 'DELETED', 'last_name' => 'USER',
                        'birth_date' => '1935-01-01', 'street' => 'unknow',
                        'apartment_number' => 'unknow', 'city_id' => 'unknow', 'zip_code' => 'unknow',
                        'phone_number' => 'unknow', 'region_id' => 'unknow', 'region_name' => 'unknow'
                    ]);

                    $subscriptions = Subscription::query()->where('rookie_id', $this->id);
                    $leader_to_notify = $subscriptions->pluck('leader_id')->toArray();

                    foreach ($leader_to_notify as $leader_id){
                        NotificationUtils::sendNotification($leader_id, "rookie_deleted_account", now());
                    }

                    $subscriptions->update([
                        'status' => 'canceled', 'canceled_at' => now(), 'deleted_at' => now(), 'sent_reply_reminder_email_at' => null, 'valid_until_at' => now()
                    ]);
                    RookieSeen::query()->where('rookie_id', $this->id)->delete();
                    RookieSaved::query()->where('rookie_id', $this->id)->delete();
                    RookieOfTheDay::query()->where('rookie_id', $this->id)->delete();

                    UserIdentityDocument::query()->where('user_id', $this->id)->delete();
                    break;
                case 'leader':

                    $subscriptions = Subscription::query()->where('leader_id', $this->id);
                    $rookie_to_notify = $subscriptions->pluck('rookie_id')->toArray();

                    foreach ($rookie_to_notify as $rookie_id){
                        NotificationUtils::sendNotification($rookie_id, "leader_deleted_account", now());
                    }

                    $subscriptions->update([
                        'status' => 'canceled', 'canceled_at' => now(), 'deleted_at' => now(), 'sent_reply_reminder_email_at' => null, 'valid_until_at' => now()
                    ]);
                    RookieSaved::query()->where('leader_id', $this->id)->delete();

                    UserIdentityDocument::query()->where('user_id', $this->id)->delete();
                    break;
                default:
                    break;
            }

        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    public function getUpdatedFieldsAttribute()
    {
        $fields = [
            'photo' => PhotoHistory::where('user_id', $this->id)->where('status', 'to_check')->exists(),
            'video' => VideoHistory::where('user_id', $this->id)->where('status', 'to_check')->exists(),
            'id' => false,
            'bio' => false
        ];

        if($bio = UserDescriptionHistory::where('user_id', $this->id)->latest('id')->first()){
            $fields['bio'] = $bio->status=='to_check';
        }

        if($id_ver = UserIdentityDocumentHistory::where('user_id', $this->id)->latest('id')->first()){
            $fields['id'] = $id_ver->status=='pending';
        }

        return $fields;
    }

    public function latestOrCreateGroupByCategory(string $category): PubnubGroup
    {
        $pubnub_group = PubnubGroup::where('user_id', $this->id)
            ->where('category', $category)
            ->latest()
            ->first();

        if(!isset($pubnub_group)){
            return Chat::config($this->id)->createUserChannelGroup($this, PubnubGroupEnum::DIRECT_CATEGORY);
        }

        /*
         * Check if group is full, if true create another group to avoid pubnub limitations
         */
        $pubnub_group_channels_count = PubnubGroupChannel::query()->where('group_id', $pubnub_group->id)->count();
        if($pubnub_group_channels_count >= 2000){
            return Chat::config($this->id)->createUserChannelGroup($this, PubnubGroupEnum::DIRECT_CATEGORY);
        }

        return $pubnub_group;
    }

    public function unsubscribeFromTelegram()
    {
        $this->update([
            'telegram_username' => null,
            'telegram_user_id' => null,
            'telegram_chat_id' => null,
            'joined_telegram_bot_at' => null
        ]);

        try {
            EventDisconnectTelegramSuccess::config($this->id, true);
        }catch (\Exception $exception) {
        }
    }

    public function getProfileAlertsAttribute()
    {
        $this->updateProfileAlertsData();

        $profile_alerts = ProfileAlert::query()->where('user_id', $this->id);
        $response = new ProfileAlertCollection($profile_alerts->get());

        if($this->type==='rookie'){
            $profile_active_code_id = ProfileAlertCode::query()->where('code', 'PA_ROOKIE_002')->first()->id;
            if(isset($profile_active_code_id)){
                $profile_alerts->where('code_id', $profile_active_code_id)->delete();
            }
        }

        $profile_alerts->update(['seen_at' => now()]);

        return $response;
    }

    private function updateProfileAlertsData()
    {
        if($this->type==='leader'){

            $failed_trans_count = Subscription::query()->where('leader_id', $this->id)->where('status', 'failed')->count();
            $credit_card_error_count_id = ProfileAlertCode::query()->where('code', 'PA_LEADER_002')->first()->id;
            $credit_card_sticky_error_id = ProfileAlertCode::query()->where('code', 'PA_LEADER_003')->first()->id;

            if($failed_trans_count === 0){

                ProfileAlert::query()->where('user_id', $this->id)
                    ->whereIn('code_id', [$credit_card_error_count_id, $credit_card_sticky_error_id])
                    ->delete();
                return;
            }

            $leader_payment_method_exists = LeaderCcbillData::where('leader_id', $this->id)
                ->where('active', true)
                ->exists();

            $leader = Leader::query()->find($this->id);

            if(!ProfileAlert::query()->where('user_id', $this->id)->where('code_id', $credit_card_error_count_id)->exists()
                && !$leader_payment_method_exists) {
                ProfileAlert::query()->create(['user_id' => $this->id, 'code_id' => $credit_card_error_count_id]);
                ProfileAlert::query()->where('user_id', $this->id)->where('code_id', $credit_card_sticky_error_id)->delete();
            }

            if(!ProfileAlert::query()->where('user_id', $this->id)->where('code_id', $credit_card_sticky_error_id)->exists()
                && $leader_payment_method_exists && $leader->hasNewCreditCard()) {
                ProfileAlert::query()->create(['user_id' => $this->id, 'code_id' => $credit_card_sticky_error_id]);
                ProfileAlert::query()->where('user_id', $this->id)->where('code_id', $credit_card_error_count_id)->delete();
            }
            return;
        }

        if($this->type === 'rookie' && in_array($this->status, ['pending', 'new'])) {

            $pending_approval_code_id = ProfileAlertCode::query()->where('code', 'PA_ROOKIE_001')->first()->id;

            if(!ProfileAlert::query()->where('user_id', $this->id)->where('code_id', $pending_approval_code_id)->exists()){
                ProfileAlert::query()->create(['user_id' => $this->id, 'code_id' => $pending_approval_code_id]);
            }
        }
    }

    public function getAgeAttribute()
    {
        if($this->type !== 'rookie'){
            return null;
        }

        $rookie = Rookie::query()->find($this->id);
        $date = new DateTime($rookie->birth_date);
        return (new DateTime())->diff($date)->y;
    }

    public function createPasswordReset($ip_address, $type = 'PASSWORD_RESET')
    {
        try {

            $password_reset = PasswordReset::updateOrCreate(
                ['email' => $this->email],
                ['email' => $this->email, 'token' => md5(uniqid('', true))]
            );

            PasswordResetHistory::create([
                'user_id' => $this->id,
                'email' => $this->email,
                'ip_address' => $ip_address
            ]);

        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        $recovery_link = env('FRONTEND_URL') . env('PASSWORD_RESET_FRONTEND_PATH') . $password_reset->token;
        try {
            Mailer::create($this)->setMisc(['recovery_link' => $recovery_link])->setTemplate($type)->sendAndCreateUserEmailSentRow();
        }catch (\Exception $exception){
            Logger::logException($exception);
        }
    }

    public function createUserStatusHistory($new_status, $changed_by, $reason = null)
    {
        if ($this->type === 'leader') {
            $leader = Leader::query()->find($this->id);
            $old_status = $leader->internal_status ?? $this->status;
        } else {
            $old_status = $this->status;
        }

        if($old_status!==$new_status){
            UserStatusHistory::create([
                'user_id' => $this->id,
                'old_status' => $old_status,
                'new_status' => strtolower(str_replace('_', ' ', $new_status)),
                'changed_by' => $changed_by,
                'reason' => $reason
            ]);
        }
    }

    public function goals()
    {
        return $this->hasMany(Goal::class,'rookie_id');
    }

    public function converters()
    {
        return $this->hasMany(RookiesConverterRequest::class,'rookie_id');
    }

    public function broadcasts()
    {
        return $this->hasMany(Broadcast::class,'sender_id');
    }

    public function goalBroadcasts()
    {
        return $this->hasMany(Broadcast::class,'sender_id')->whereHas('goals');
    }

    public function chatTopicsSaved()
    {
        return $this->belongsToMany(ChatTopic::class, 'chat_topics_users', 'users_id', 'chat_topics_id');
    }

    public function favoriteActivitiesSaved()
    {
        return $this->belongsToMany(FavoriteActivity::class, 'favorite_activities_users', 'users_id', 'favorite_activities_id');
    }
}
