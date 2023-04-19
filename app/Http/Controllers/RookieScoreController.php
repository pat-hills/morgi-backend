<?php

namespace App\Http\Controllers;

use App\Models\RookieScore;
use Illuminate\Http\Request;

class RookieScoreController extends Controller
{
    public function profilePerformance(Request $request)
    {
        $user = $request->user();

        $rookie_score = RookieScore::query()->where('rookie_id', $user->id)->first();
        if(!isset($rookie_score)){
            return response()->json([], 404);
        }

        $max_values = RookieScore::getAttributesMaxScore();

        $performance = $rookie_score->getPerformanceBasedData();
        $performance_based = [
            'points' => $performance['score'],
            'max' => $max_values['performance_based_max_score'],
            'label' => 'Performance Based Profile Points',
            'data' => $performance['data'],
        ];

        $action = $rookie_score->getActionBasedData();
        $action_based = [
            'points' => $action['score'],
            'max' => $max_values['action_based_max_score'],
            'label' => 'Action Based Profile Points',
            'data' => $action['data']
        ];

        $data = [
            'max' => $max_values['total_max_score'],
            'points' => $performance_based['points'] + $action_based['points'],
            'performance' =>  $performance_based,
            'visibility' => $action_based
        ];

        return response()->json($data);
    }
}
