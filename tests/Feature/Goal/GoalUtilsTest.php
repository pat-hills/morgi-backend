<?php

use Carbon\Carbon;
use App\Models\Goal;
use App\Models\GoalType;
use App\Utils\Goal\GoalUtils;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

uses(DatabaseTransactions::class);

it('Goal date validation blocks too long intervals', function () {
    $goal_type = GoalType::query()->where('type', GoalType::SMALL_SIZE_GOAL)->first();

    $start_date = Carbon::now();
    $calculated_max_days = Goal::calculateMaxAllowedDays($goal_type, $start_date);
    $end_date = Carbon::now()->addDays($calculated_max_days + 1);

    expect(
        function () use ($start_date, $end_date, $goal_type) {
            return GoalUtils::validateGoalDate($start_date, $end_date, $goal_type);
        }
    )->toThrow(
        BadRequestException::class
    );

})->group('utils');

it('Goal date validation blocks inverted intervals', function () {
    $goal_type = GoalType::query()->where('type', GoalType::SMALL_SIZE_GOAL)->first();

    $start_date = Carbon::now();
    $calculated_max_days = Goal::calculateMaxAllowedDays($goal_type, $start_date);
    $end_date = Carbon::now()->addDays($calculated_max_days + 1);

    expect(
        function () use ($end_date, $start_date, $goal_type) {
            return GoalUtils::validateGoalDate($end_date, $start_date, $goal_type);
        }
    )->toThrow(
        BadRequestException::class
    );
})->group('utils');
