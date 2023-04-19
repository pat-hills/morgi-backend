<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rookie extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'description',
        'birth_date',
        'country_id',
        'region_id',
        'region_name',
        'morgi_balance',
        'micro_morgi_balance',
        'withdrawal_balance',
        'street',
        'apartment_number',
        'city_id',
        'zip_code',
        'phone_number',
        'age_confirmation',
        'user_id',
        'beauty_score',
        'intelligence_score',
        'likely_receive_score',
        'untaxed_withdrawal_balance',
        'untaxed_micro_morgi_balance',
        'untaxed_morgi_balance',
        'first_micromorgi_gift_leaders',
        'is_favourite',
        'path_changes_count',
        'is_converter',
        'leaders_first_subscription',
        'converter_carousel_position_id'
    ];

    protected $casts = [
        'is_converter' => 'boolean',
        'is_favourite' => 'boolean'
    ];

    public function goals()
    {
        return $this->hasMany(Goal::class, 'rookie_id')
          ->where('status', '!=', Goal::STATUS_CANCELLED)
          ->orderByDesc('created_at');
    }

    public function convertersRequest()
    {
        return $this->hasMany(RookiesConverterRequest::class,'rookie_id');
    }

    public function hasPhoto(int $photo_id): bool
    {
        $photo = Photo::query()->find($photo_id);
        if(!$photo || $photo->rookie_id !== $this->id){
            return false;
        }

        return true;
    }

    public function getHasPaymentMethodAttribute()
    {
        return $this->hasPaymentMethod();
    }

    public function getOwnVideo()
    {
        $validation_video = VideoHistory::where('user_id', $this->id)
            ->where('status', 'to_check')
            ->latest()
            ->first();

        if($validation_video){
            return $validation_video->append('under_validation');
        }

        $video = Video::where('user_id', $this->id)->first();

        return ($video) ? $video->append('under_validation') : null;
    }

    public function addVideo(string $path_location)
    {
        if(empty($path_location)){
            return null;
        }

        User::find($this->id)->update(['admin_check' => true]);

        return VideoHistory::create(['user_id' => $this->id, 'path_location' => $path_location]);
    }

    public function removeVideo()
    {
        $video = $this->getOwnVideo();
        if(!isset($video)){
            return;
        }

        if($video->under_validation===true){
            VideoHistory::query()->find($video->id)->delete();
            return;
        }

        Video::query()->find($video->id)->delete();
    }

    public function getRegion()
    {
        if($this->region_id){
            return Region::query()->find($this->region_id);
        }

        if($this->region_name){
            return ['name' => $this->region_name];
        }

        return null;
    }

    public function getCity()
    {
        return (isset($this->city_id))
            ? City::query()->find($this->city_id)
            : null;
    }

    public function getActiveAttribute()
    {
        return User::query()->find($this->id)->active;
    }

    public function hasPaymentMethod(): bool
    {
        return PaymentPlatformRookie::where('rookie_id', $this->id)->count() > 0;
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function pushMicromorgi($micromorgi, $taxed_micromorgi): void
    {
        $this->update([
            'micro_morgi_balance' => $this->micro_morgi_balance + $taxed_micromorgi,
            'untaxed_micro_morgi_balance' => $this->untaxed_micro_morgi_balance + $micromorgi,
        ]);
    }

    public function popMicromorgi($micromorgi, $taxed_micromorgi): void
    {
        $this->update([
            'micro_morgi_balance' => $this->micro_morgi_balance - $taxed_micromorgi,
            'untaxed_micro_morgi_balance' => $this->untaxed_micro_morgi_balance - $micromorgi,
        ]);
    }

    public function pushMorgi($morgi, $taxed_morgi): void
    {
        $this->update([
            'morgi_balance' => $this->morgi_balance + $taxed_morgi,
            'untaxed_morgi_balance' => $this->untaxed_morgi_balance + $morgi,
        ]);
    }

    public function popMorgi($morgi, $taxed_morgi): void
    {
        $this->update([
            'morgi_balance' => $this->morgi_balance - $taxed_morgi,
            'untaxed_morgi_balance' => $this->untaxed_morgi_balance - $morgi,
        ]);
    }

    public function pushDollars($dollars, $taxed_dollars): void
    {
        $this->update([
            'withdrawal_balance' => $this->withdrawal_balance + $taxed_dollars,
            'untaxed_withdrawal_balance' => $this->untaxed_withdrawal_balance + $dollars,
        ]);
    }

    public function popDollars($dollars, $taxed_dollars): void
    {
        $this->update([
            'withdrawal_balance' => $this->withdrawal_balance - $taxed_dollars,
            'untaxed_withdrawal_balance' => $this->untaxed_withdrawal_balance - $dollars,
        ]);
    }

    public function hasBlockedLeader(int $leader_id): bool
    {
        return UserBlock::where('from_user_id', $this->id)
            ->where('to_user_id', $leader_id)
            ->whereNull('deleted_at')
            ->exists();
    }

    public function getMainPaymentMethod()
    {
        return PaymentPlatformRookie::where('rookie_id', $this->id)->where('main', 1)->first();
    }

    // TODO Ticking time bomb, remember to readapt this whenever we fix the chat
    public function chattingLeaders()
    {
        return $this->belongsToMany(Leader::class, 'pubnub_channels', 'rookie_id', 'leader_id');
    }

    public function subscribedLeaders()
    {
        return $this->belongsToMany(Leader::class, 'subscriptions', 'rookie_id', 'leader_id');
    }
}
