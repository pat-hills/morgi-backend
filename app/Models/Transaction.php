<?php

namespace App\Models;

use App\Enums\TransactionEnum;
use App\Enums\TransactionTypeEnum;
use App\Logger\Logger;
use App\Services\Mailer\Mailer;
use App\Utils\ActivityLogsUtils;
use App\Utils\NotificationUtils;
use App\Utils\ReasonUtils;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'internal_id' , 'referal_internal_id', 'rookie_id', 'leader_id',
        'type', 'transaction_type_id', 'morgi', 'taxed_morgi',
        'taxed_micromorgi', 'taxed_dollars', 'micromorgi',
        'dollars', 'notes', 'payment_rookie_id',
        'subscription_id', 'refund_type', 'admin_id', 'refunded_at',
        'leader_payment_id', 'refunded_by', 'admin_description',
        'internal_status', 'internal_status_reason', 'internal_status_by',
        'user_block_id', 'coupon_id', 'goal_id', 'is_goal_transaction_refund'
    ];

    protected $appends = [
        'description'
    ];

    public function getReferalTransactionCreatedAtAttribute(){

        if(!isset($this->referal_internal_id)){
            return null;
        }

        $referal_transaction = Transaction::query()->where('internal_id', $this->referal_internal_id)->first();
        if(!isset($referal_transaction)){
            return null;
        }

        return $referal_transaction->created_at;
    }

    public function getSignByUserType($user_type){

        if($user_type === 'leader'){
            return $this->computeLeaderSign();
        }

        if($user_type === 'rookie'){
            return $this->computeRookieSign();
        }

        return null;
    }

    private function computeLeaderSign(){

        $type = $this->type;

        $leader_map = TransactionEnum::LEADER_SIGN_MAP;

        if(isset($leader_map[$type])){
            return $leader_map[$type];
        }

        $leader_refund_map = TransactionEnum::LEADER_REFUND_SIGN_MAP;

        if($type==='refund'){

            $refund_type = Transaction::where('internal_id', $this->referal_internal_id)->first();

            if(!isset($refund_type)){
                return null;
            }

            if(isset($leader_refund_map[$refund_type->type])){
                return $leader_refund_map[$refund_type->type];
            }
        }

        return null;
    }

    private function computeRookieSign(){

        $type = $this->type;
        $rookie_map = TransactionEnum::ROOKIE_SIGN_MAP;

        return (isset($rookie_map[$type])) ? $rookie_map[$type] : null;
    }

    public function getRookieUsernameAttribute(){
        return User::query()->find($this->rookie_id)->username ?? null;
    }

    public function getRookieFullNameAttribute(){
        return User::query()->find($this->rookie_id)->full_name ?? null;
    }

    public function getLeaderFullNameAttribute(){
        return User::query()->find($this->leader_id)->full_name ?? null;
    }

    public function getPaymentInfoAttribute(){

        $payment_rookie = PaymentRookie::query()->find($this->payment_rookie_id)->reference;
        $reference = json_decode($payment_rookie, false);

        return $reference->wallet_number ?? $reference->email ?? $reference->wallet_address ?? "";
    }

    public function getGoalNameAttribute(): ?string
    {
        $goal = Goal::find($this->goal_id);
        return (isset($goal)) ? $goal->name : null;
    }

    public function getPaymentMethodAttribute(){

        $payment_rookie = PaymentRookie::query()->find($this->payment_rookie_id);
        if(!isset($payment_rookie)){
            return null;
        }

        $payment = Payment::query()->find($payment_rookie->payment_id);
        if(!isset($payment)){
            return null;
        }

        $payment_platform = PaymentPlatformRookie::query()->find($payment->payment_platform_id);
        if(!isset($payment_platform)){
            return null;
        }

        $payment_platform_name = PaymentPlatform::query()->find($payment_platform->payment_platform_id);
        if(!isset($payment_platform_name)){
            return null;
        }

        return $payment_platform_name->name;
    }

    public function getPaymentPeriodStartDateAttribute(){

        $payment_rookie = PaymentRookie::query()->find($this->payment_rookie_id);
        if(!isset($payment_rookie)){
            return null;
        }

        $payment = Payment::query()->find($payment_rookie->payment_id);
        if(!isset($payment)){
            return null;
        }

        $payment_period = PaymentPeriod::query()->find($payment->payment_period_id);
        if(!isset($payment_period)){
            return null;
        }

        return $payment_period->start_date;
    }

    public function getPaymentPeriodEndDateAttribute(){

        $payment_rookie = PaymentRookie::query()->find($this->payment_rookie_id);
        if(!isset($payment_rookie)){
            return null;
        }

        $payment = Payment::query()->find($payment_rookie->payment_id);
        if(!isset($payment)){
            return null;
        }

        $payment_period = PaymentPeriod::query()->find($payment->payment_period_id);
        if(!isset($payment_period)){
            return null;
        }

        return $payment_period->end_date;
    }

    public function getPaymentRejectedAtAttribute(){

        $payment_rookie = PaymentRookie::query()->find($this->payment_rookie_id);
        if(!isset($payment_rookie)){
            return null;
        }

        return Carbon::create($payment_rookie->rejected_at)->toDateString();
    }

    public function getPaymentApprovedAtAttribute(){

        $payment_rookie = PaymentRookie::query()->find($this->payment_rookie_id);
        if(!isset($payment_rookie)){
            return null;
        }

        return Carbon::create($payment_rookie->approved_at)->toDateString();
    }

    public function getDescriptionAttribute(){

        $user = Auth::user();
        if(!isset($user)){
            return null;
        }

        $transaction_type = TransactionType::query()->find($this->transaction_type_id);

        return $this->mapDescription($transaction_type, $user->type);
    }

    private function mapDescription($transaction_type, $user_type){

        if(!$transaction_type || in_array($user_type, ['admin', 'operator'])){
            return null;
        }

        $tags_to_search = TransactionTypeEnum::TYPES_TAGS[$user_type][$transaction_type->type];
        $tags_to_replace = TransactionTypeEnum::TYPES_TAGS_ATTRIBUTE[$user_type][$transaction_type->type];

        foreach ($tags_to_replace as $key=>$tag){
            $tags_to_replace[$key] = $this->$tag;
        }

        $description_field = "description_$user_type";
        $transaction_type_description = $transaction_type->$description_field;

        return str_replace($tags_to_search, $tags_to_replace, $transaction_type_description);
    }

    public function approveRookiePayment($admin_id = null, $notes = null){

        $transaction_type = TransactionType::query()->where('type', 'withdrawal')->first();
        $this->update(['type' => 'withdrawal', 'transaction_type_id' => $transaction_type->id, 'admin_id' => $admin_id, 'notes' => ReasonUtils::ALL_REASON[$notes] ?? $notes]);

        $payment_rookie = PaymentRookie::query()->find($this->payment_rookie_id);
        $payment_rookie->update(['status' => 'successful', 'note' => ReasonUtils::ALL_REASON[$notes] ?? $notes, 'admin_id' => $admin_id, 'approved_at' => now(), 'rejected_at' => NULL]);

        ActivityLog::query()->create([
            'refund_type' => 'withdrawal',
            'initiated_by' => 'morgi',
            'internal_id' => ActivityLogsUtils::generateInternalId($this->rookie_id),
            'transaction_internal_id' => $this->internal_id,
            'rookie_id' => $this->rookie_id,
            'morgi' => $this->taxed_morgi,
            'micromorgi' => $this->taxed_micromorgi,
            'dollars' => $this->taxed_dollars
        ]);

    }

    public function rejectRookiePayment($admin_id = null, $notes = null){

        $transaction_type = TransactionType::query()->where('type', 'withdrawal_rejected')->first();
        $this->update([
            'type' => 'withdrawal_rejected',
            'transaction_type_id' => $transaction_type->id,
            'refunded_by' => $admin_id,
            'notes' => ReasonUtils::ALL_REASON[$notes] ?? $notes,
            'refunded_at' => now()
        ]);

        $payment_rookie = PaymentRookie::query()->find($this->payment_rookie_id);
        $payment_rookie->update([
            'status' => 'declined',
            'note' => ReasonUtils::ALL_REASON[$notes] ?? $notes,
            'admin_id' => $admin_id,
            'rejected_at' => now()
        ]);

        ActivityLog::query()->create([
            'refund_type' => 'withdrawal_rejected',
            'initiated_by' => 'morgi',
            'internal_id' => ActivityLogsUtils::generateInternalId($this->rookie_id),
            'transaction_internal_id' => $this->internal_id,
            'rookie_id' => $this->rookie_id,
            'morgi' => $this->taxed_morgi,
            'micromorgi' => $this->taxed_micromorgi,
            'dollars' => $this->taxed_dollars
        ]);

        $user = User::find($this->rookie_id);
        try {
            Mailer::create($user)
                ->setMisc(['reason' => ReasonUtils::ALL_REASON[$notes] ?? $notes])
                ->setTemplate('ROOKIE_PAYMENT_REJECTED')
                ->sendAndCreateUserEmailSentRow();

            NotificationUtils::sendNotification($this->rookie_id, 'rookie_rejected_payment_general', now());
        }catch (\Exception $exception){
            Logger::logException($exception);
        }
    }
}
