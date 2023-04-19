<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use Illuminate\Contracts\View\View;

class GoalController extends Controller
{

    public function index(): view
    {
        $goal_status = Goal::STATUS;

        $review_count = Goal::query()
            ->where('status', Goal::STATUS_REVIEW)
            ->count();

        $pending_approval_proof_count = Goal::query()
            ->where('status', Goal::STATUS_PROOF_PENDING_APPROVAL)
            ->count();

        $pending_count = Goal::query()
            ->where('status', Goal::STATUS_PENDING)
            ->count();

        $all_status = [];
        foreach ($goal_status as $item){

            switch ($item){
                case Goal::STATUS_REVIEW:
                    $counter = $review_count;
                    break;
                case Goal::STATUS_PROOF_PENDING_APPROVAL:
                    $counter = $pending_approval_proof_count;
                    break;
                case Goal::STATUS_PENDING:
                    $counter = $pending_count;
                    break;
                default:
                    $counter = null;
                    break;
            }
            $all_status[] = array('name' => $item, 'counter' => $counter);
        }

        return view('admin.admin-pages.goal.index', compact('all_status'));
    }

    public function show(Goal $goal): view
    {
        $admin_available_statues = Goal::AVAILABLE_STATUS_BY_STATUS[$goal->status] ?? null;
        return view('admin.admin-pages.goal.show', compact('goal', 'admin_available_statues'));
    }
}
