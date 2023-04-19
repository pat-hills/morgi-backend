<?php

namespace App\Models;

use App\Models\User;
use App\Models\Leader;
use App\Models\GoalType;
use App\Models\GoalMedia;
use App\Models\GoalProof;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use PubNub\Endpoints\Presence\Leave;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Goal extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_REVIEW = 'review';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_SUCCESSFUL = 'successful';
    public const STATUS_AWAITING_PROOF = 'awaiting_proof';
    public const STATUS_PROOF_PENDING_APPROVAL = 'proof_pending_approval';
    public const STATUS_PROOF_DECLINED = 'proof_status_declined';

    public const CANCEL_REASON_GOAL_NOT_REACHED = 'goal_not_reached';
    public const CANCEL_REASON_USER_CANCELLED = 'cancelled_by_user';
    public const CANCEL_REASON_OTHER = 'other';
    public const CANCEL_REASONS = [
        'goal_not_reached',
        'cancelled_by_user',
        'other'
    ];

    public const MINIMUM_SUCCESS_PERCENTAGE = 75;

    public const STATUS = [
        self::STATUS_PENDING,
        self::STATUS_ACTIVE,
        self::STATUS_SUSPENDED,
        self::STATUS_REVIEW,
        self::STATUS_CANCELLED,
        self::STATUS_SUCCESSFUL,
        self::STATUS_AWAITING_PROOF,
        self::STATUS_PROOF_PENDING_APPROVAL,
        self::STATUS_PROOF_DECLINED
    ];

    public const ADMIN_AVAILABLE_STATUS = [
        self::STATUS_SUSPENDED,
        self::STATUS_ACTIVE,
        self::STATUS_SUCCESSFUL,
        self::STATUS_CANCELLED,
        self::STATUS_AWAITING_PROOF,
        self::STATUS_PROOF_DECLINED
    ];

    public const AVAILABLE_STATUS_BY_STATUS = [
        self::STATUS_PENDING => [
            self::STATUS_ACTIVE,
            self::STATUS_CANCELLED,
            self::STATUS_SUSPENDED
        ],
        self::STATUS_REVIEW => [
            self::STATUS_ACTIVE,
            self::STATUS_SUSPENDED,
            self::STATUS_CANCELLED
        ],
        self::STATUS_ACTIVE => [
            self::STATUS_SUSPENDED,
            self::STATUS_CANCELLED
        ],
        self::STATUS_AWAITING_PROOF => [
            self::STATUS_SUCCESSFUL,
            self::STATUS_CANCELLED
        ],
        self::STATUS_PROOF_PENDING_APPROVAL => [
            self::STATUS_SUCCESSFUL,
            self::STATUS_PROOF_DECLINED,
            self::STATUS_AWAITING_PROOF
        ],
        self::STATUS_SUSPENDED => [
            self::STATUS_ACTIVE,
            self::STATUS_CANCELLED
        ]
    ];

    protected $guarded = [
        'cancelled_at', 'cancelled_reason'
    ];

    protected $casts = [
        'end_date' => 'datetime',
        'start_date' => 'datetime',
        'proof_type' => 'array',
        'has_image_proof' => 'boolean',
        'has_video_proof' => 'boolean'
    ];

    protected $fillable = [
        'name', 'slug', 'details', 'target_amount', 'currency_type',
        'rookie_id', 'start_date', 'end_date', 'thank_you_message', 'proof_note',
        'cancelled_at', 'cancelled_reason', 'cancelled_by_user_id', 'type_id', 'status',
        'has_image_proof', 'has_video_proof', 'cancelled_note'
    ];

    //TODO Refactor in BroadcastResource (Deadline is in 4 hours so don't look at me like that)
    protected $appends = [
        'donation_sum'
    ];

    public function rookie()
    {
        return $this->belongsTo(User::class,'rookie_id');
    }

    public function type()
    {
        return $this->belongsTo(GoalType::class,'type_id');
    }

    public function donations()
    {
       return $this->hasMany(GoalDonation::class,'goal_id');
    }

    public function successfulDonations()
    {
        return $this->donations()->where('status', GoalDonation::STATUS_SUCCESSFUL);
    }

    public function savedBy()
    {
        return $this->belongsToMany(Leader::class, 'saved_goals', 'goal_id', 'leader_id');
    }

    public function media()
    {
        return $this->hasMany(GoalMedia::class,'goal_id');
    }

    public function proofs()
    {
        return $this->hasMany(GoalProof::class,'goal_id');
    }

    public function broadcasts()
    {
      return $this->belongsToMany(Broadcast::class, 'broadcast_goals', 'goal_id', 'broadcast_id');
    }

    public static function scopeActiveUserGoals($query, $rookie_id)
    {
        return $query->where('status', '!=', self::STATUS_SUCCESSFUL)
            ->where('status', '!=', self::STATUS_CANCELLED)
            ->where('rookie_id', $rookie_id)
            ->where('end_date', '>', now());
    }

    public static function scopeWhereActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)->where('end_date', '>', now());
    }

    public static function scopeWhereNotActive($query)
    {
        return $query->where(function ($q) {
                $q->where('status', '!=', self::STATUS_ACTIVE)
                    ->orWhere('end_date', '<=', now());
            });
    }

    public function isActive(): bool
    {
        if($this->status !== self::STATUS_ACTIVE){
            return false;
        }

        return true;
    }

    public static function scopeTimeRangeInHours($query, $hour)
    {
        return $query->where('end_date', '<', now()->addHours($hour));
    }

    public static function calculateMaxAllowedDays(GoalType $goal_type, Carbon $start_date)
    {
        $result = 0;
        switch( $goal_type->duration_type ){
            case 'days':
               $result = $goal_type->duration_value;
                break;
            case 'weeks':
                $result = $goal_type->duration_value * 7;
                break;
            case 'months':
                $date_check = $start_date->copy();
                $max_date = $date_check->addMonths($goal_type->duration_value);
                $result = $max_date->diffInDays($start_date);
                break;
            default:
                $result = 0; // Technically shouldn't ever get here, if it does someone did something really risky like edit the DB
        }

        return $result;
    }

    public function getDonationSumAttribute(){
        return GoalDonation::query()->where('goal_id', $this->id)->sum('amount');
    }

    public function getBroadcastAttribute(){
        return $this->broadcasts()->where('is_goal', true)->first();
    }
}
