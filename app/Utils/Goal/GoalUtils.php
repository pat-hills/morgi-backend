<?php

namespace App\Utils\Goal;

use App\Models\Goal;
use App\Models\GoalDonation;
use App\Models\GoalProof;
use App\Models\GoalType;
use App\Models\Transaction;
use App\Utils\TransactionRefundUtils;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GoalUtils
{

    public static function validateGoalDate(Carbon $start_date, Carbon $end_date, GoalType $goal_type) : void
    {
        $now = Carbon::now();

        if ($start_date->gt($end_date)){
            throw new BadRequestException( "End date should be greater than start date.");
        }

        if ($now->gt($end_date)){
            throw new BadRequestException( "End date should be greater time now.");
        }

        $calculated_max_days = Goal::calculateMaxAllowedDays($goal_type, $start_date);
        $max_date = $start_date->copy()->addDays($calculated_max_days);

        if ($end_date->gt($max_date)){
            throw new BadRequestException( "Days diffence must be in the range of goal type allowed period");
        }
    }

    public static function validateGoalProof(Goal $goal, array $proofs){

        if(!in_array($goal->status, [Goal::STATUS_AWAITING_PROOF, Goal::STATUS_PROOF_DECLINED])){
            throw new BadRequestHttpException("Goal is not awaiting proof");
        }

        $total_donation_amount = $goal->donations()->where('status', 'successful')->sum('amount');
        $goal_donations_percentage = ($total_donation_amount / $goal->target_amount) * 100;

        if ($goal_donations_percentage < Goal::MINIMUM_SUCCESS_PERCENTAGE) {
            throw new BadRequestHttpException(trans('rookie.proof_submission_not_allow'));
        }

        if(empty($proofs)){
            throw new BadRequestHttpException('No proof submitted');
        }

        // It's not the best way but I'm leaving for a week tomorrow, so I'll just write it in a way that works
        // and figure out how to rewrite in a readable way when I come back

        $has_video = false;
        $has_image = false;

        foreach ($proofs as $proof) {
            if( empty($proof['type']) || !in_array($proof['type'], GoalProof::FILE_TYPES) ){
                throw new BadRequestHttpException('Invalid proof type uploaded');
            }
            if( empty($proof['url']) ){
                throw new BadRequestHttpException('Proof has no valid url');
            }
            if ($proof['type'] == GoalProof::TYPE_IMAGE){
                $has_image = true;
            }
            if ($proof['type'] == GoalProof::TYPE_VIDEO){
                $has_video = true;
            }
        }

        if($goal->has_image_proof && !$has_image){
            throw new BadRequestHttpException('There is no image proof');
        }
        if($goal->has_video_proof && !$has_video){
            throw new BadRequestHttpException('There is no video proof');
        }
    }

    public static function refundGoalDonations(int $goal_id): void
    {
        $goal_donations = GoalDonation::query()->where('goal_id', $goal_id)
            ->where('status', 'successful')
            ->get();

        if($goal_donations->isEmpty()){
            return;
        }

        $goal_donations_ids = $goal_donations->pluck('id')->toArray();

        /*
         * Refund leaders transactions
         */
        $transactions_ids = $goal_donations->pluck('transaction_id')->toArray();
        $transactions = Transaction::query()->findMany($transactions_ids);
        if($transactions->isEmpty()){
            return;
        }

        foreach ($transactions as $transaction){
            TransactionRefundUtils::config($transaction)->refund(null, 'Goal not reached 75%');
        }

        /*
         * Refund goal's donations
         */
        GoalDonation::query()->whereIn('id', $goal_donations_ids)->update(['status' => 'refund']);
    }
}
