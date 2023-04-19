<?php

namespace App\Utils\Admin;

use App\Enums\UserEnum;
use App\Models\Broadcast;
use App\Models\BroadcastMessage;
use App\Models\Goal;
use App\Models\GoalDonation;
use App\Models\GoalProof;
use App\Models\Rookie;
use App\Models\Transaction;
use App\Models\User;
use App\Transactions\MicroMorgi\TransactionGoalWithdraw;
use App\Utils\BroadcastUtils;
use App\Utils\NotificationUtils;
use Illuminate\Http\Response;

class AdminGoalUtils
{
    public static function approveProof(Goal $goal, GoalProof $goal_proof, User $user): void
    {
        if ($user->type !== UserEnum::TYPE_ADMIN) {
            throw new \Exception('Forbidden', 403);
        }

        $goal_proof->update([
            'status' => GoalProof::STATUS_APPROVED,
            'admin_id' => $user->id
        ]);

        NotificationUtils::sendNotification($goal->rookie_id, 'rookie_goal_proof_approved', now(), [
            'goal_id' => $goal->id
        ]);
    }

    public static function declineProof(Goal $goal, GoalProof $goal_proof, User $user, string $reason): void
    {
        if ($user->type !== UserEnum::TYPE_ADMIN) {
            throw new \Exception('Forbidden', 403);
        }

        $goal_proof->update([
            'status' => GoalProof::STATUS_DECLINED,
            'admin_id' => $user->id,
            'declined_reason' => $reason
        ]);

        self::setGoalToAwaitingProof($goal, $user, $reason);

        NotificationUtils::sendNotification($goal->rookie_id, 'rookie_goal_proof_declined', now(), [
            'goal_id' => $goal->id, 'reason' => $reason
        ]);
    }

    public static function updateStatus(Goal $goal, User $user, string $status, string $reason = null): void
    {
        switch ($status) {
            case Goal::STATUS_SUCCESSFUL:
                self::approveGoal($goal, $user);
                break;
            case Goal::STATUS_AWAITING_PROOF:
                self::waitingProofGoal($goal, $user, $reason);
                break;
            case Goal::STATUS_PROOF_DECLINED:
                self::proofDeclineGoal($goal, $user, $reason);
                break;
            case Goal::STATUS_CANCELLED:
                self::cancelGoal($goal, $user, $reason);
                break;
            case Goal::STATUS_SUSPENDED:
                self::suspendGoal($goal, $user, $reason);
                break;
            case Goal::STATUS_ACTIVE:
                self::activeGoal($goal);
                break;
            case Goal::STATUS_PENDING:
                self::pendingGoal($goal);
                break;
            case Goal::STATUS_PROOF_PENDING_APPROVAL:
                self::proofPendingApproval($goal);
                break;
            case Goal::STATUS_REVIEW:
                self::reviewGoal($goal);
                break;
        }
    }

    public static function approveGoal(Goal $goal, User $user): void
    {
        if ($user->type !== UserEnum::TYPE_ADMIN) {
            throw new \Exception('Forbidden', 403);
        }

        $goal->update([
            'status' => Goal::STATUS_SUCCESSFUL
        ]);

        if (
            !Transaction::query()
                ->where('goal_id', $goal->id)
                ->where('type', 'goal_withdraw')
                ->exists()
        ) {

            try {
                TransactionGoalWithdraw::create($goal->id);
            } catch (\Exception $exception) {
                throw new \Exception($exception->getMessage(), 500);
            }

            GoalProof::query()
                ->where('goal_id', $goal->id)
                ->where('status', GoalProof::STATUS_PENDING)
                ->update([
                    'status' => GoalProof::STATUS_APPROVED,
                    'admin_id' => $user->id
                ]);

            $goal_donations = GoalDonation::query()
                ->where('goal_id', $goal->id)
                ->where('status', GoalDonation::STATUS_SUCCESSFUL)
                ->groupBy('leader_id')
                ->get();

            foreach ($goal_donations as $goal_donation) {

                NotificationUtils::sendNotification($goal_donation->leader_id, 'leader_goal_completed', now(), [
                    'goal_id' => $goal->id,
                    'ref_user_id' => $goal->rookie_id
                ]);
            }
        }
    }

    public static function proofDeclineGoal(Goal $goal, User $user, string $reason): void
    {
        if ($user->type !== UserEnum::TYPE_ADMIN) {
            throw new \Exception('Forbidden', 403);
        }

        $goal->update([
            'status' => Goal::STATUS_PROOF_DECLINED,
            'cancelled_by_user_id' => $user->id,
            'cancelled_reason' => $reason
        ]);

        GoalProof::query()
            ->where('goal_id', $goal->id)
            ->where('status', GoalProof::STATUS_PENDING)
            ->update([
                'status' => GoalProof::STATUS_DECLINED,
                'declined_reason' => $reason,
                'admin_id' => $user->id
            ]);

        NotificationUtils::sendNotification($goal->rookie_id, 'rookie_goal_proof_declined', now(), [
            'goal_id' => $goal->id,'reason' => $reason
        ]);
    }

    public static function waitingProofGoal(Goal $goal, User $user, string $reason): void
    {
        self::setGoalToAwaitingProof($goal, $user, $reason);

        GoalProof::query()
            ->where('goal_id', $goal->id)
            ->where('status', GoalProof::STATUS_PENDING)
            ->update([
                'status' => GoalProof::STATUS_DECLINED,
                'declined_reason' => $reason,
                'admin_id' => $user->id
            ]);
    }

    public static function cancelGoal(Goal $goal, User $user, string $reason): void
    {
        try {
            if ($goal->donation_sum) {
                \App\Utils\Goal\GoalUtils::refundGoalDonations($goal->id);
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $goal->update([
            'status' => Goal::STATUS_CANCELLED,
            'cancelled_by_user_id' => $user->id,
            'cancelled_reason' => $reason
        ]);

        GoalProof::query()
            ->where('goal_id', $goal->id)
            ->where('status', GoalProof::STATUS_PENDING)
            ->update([
                'status' => GoalProof::STATUS_DECLINED,
                'declined_reason' => $reason,
                'admin_id' => $user->id
            ]);

        NotificationUtils::sendNotification($goal->rookie_id, 'rookie_goal_cancelled', now(), [
            'goal_id' => $goal->id, 'reason' => $reason
        ]);
    }

    public static function suspendGoal(Goal $goal, User $user, string $reason): void
    {
        $goal->update([
            'status' => Goal::STATUS_SUSPENDED,
            'cancelled_by_user_id' => $user->id,
            'cancelled_reason' => $reason
        ]);

        NotificationUtils::sendNotification($goal->rookie_id, 'rookie_goal_suspended', now(), [
            'goal_id' => $goal->id, 'reason' => $reason
        ]);
    }

    public static function setGoalToAwaitingProof(Goal $goal, User $user, string $reason): void
    {
        if ($goal->status !== Goal::STATUS_AWAITING_PROOF) {
            $goal->update([
                'status' => Goal::STATUS_AWAITING_PROOF,
                'cancelled_by_user_id' => $user->id,
                'cancelled_reason' => $reason
            ]);
        }
    }

    public static function activeGoal(Goal $goal): void
    {
        if ($goal->status === Goal::STATUS_ACTIVE) {
            return;
        }

        $goal->update([
            'status' => Goal::STATUS_ACTIVE
        ]);

        if ($goal->broadcasts->isEmpty()) {
            $goal_broadcast = Broadcast::create([
                'is_goal' => true,
                'sender_id' => $goal->rookie_id,
                'display_name' => $goal->name
            ]);
            $goal_broadcast->goals()->attach($goal);

            $notify_broadcast = Broadcast::create([
                'is_goal' => false,
                'sender_id' => $goal->rookie_id,
                'display_name' => $goal->name
            ]);

            $rookie = Rookie::find($goal->rookie_id);

            $subscribed_leaders = $rookie->subscribedLeaders()->get()->pluck('id')->toArray();
            $leaders_donate_goals = Goal::query()->selectRaw('goal_donations.leader_id')
                ->join('goal_donations', 'goals.id', '=', 'goal_donations.goal_id')
                ->where('rookie_id', $goal->rookie_id)
                ->get()
                ->pluck('leader_id')
                ->toArray();

            BroadcastUtils::broadcastMessage(
                [],
                array_unique(array_merge($subscribed_leaders, $leaders_donate_goals)),
                "I published a new goal!",
                $goal->rookie,
                $notify_broadcast,
                [
                    "goal_id" => $goal->id,
                    "type" => BroadcastMessage::METADATA_TYPE_GOAL
                ]
            );
        }

        NotificationUtils::sendNotification($goal->rookie_id, 'rookie_goal_activated', now(), [
            'goal_id' => $goal->id
        ]);
    }

    public static function pendingGoal(Goal $goal): void
    {
        if ($goal->status !== Goal::STATUS_PENDING) {

            $goal->update([
                'status' => Goal::STATUS_PENDING
            ]);
        }
    }

    public static function proofPendingApproval(Goal $goal): void
    {
        if ($goal->status !== Goal::STATUS_PROOF_PENDING_APPROVAL) {

            $goal->update([
                'status' => Goal::STATUS_PROOF_PENDING_APPROVAL
            ]);
        }
    }

    public static function reviewGoal(Goal $goal): void
    {
        if ($goal->status !== Goal::STATUS_REVIEW) {

            $goal->update([
                'status' => Goal::STATUS_REVIEW
            ]);
        }
    }

    public static function isReasonRequired(string $new_goal_status): bool
    {
        switch ($new_goal_status) {
            case Goal::STATUS_SUSPENDED:
            case Goal::STATUS_PROOF_DECLINED:
            case Goal::STATUS_CANCELLED:
            case Goal::STATUS_AWAITING_PROOF:
                return true;
            default:
                return false;
        }
    }
}
