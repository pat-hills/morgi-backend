<?php

namespace App\Http\Controllers;

use App\Http\Resources\RookieOfTheDayResource;
use App\Http\Resources\RookieWinnerHistoryResource;
use App\Models\RookieOfTheDay;
use App\Models\RookieWinnerHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RookieWinnerHistoryController extends Controller
{
    public function morgiWinners(Request $request)
    {
        $rookie_winners_history = RookieWinnerHistory::query()
            ->select('id', 'win_at', 'rookie_id')
            ->whereDate('win_at', '<=', Carbon::now()->subDay())
            ->orderByDesc('win_at')
            ->paginate(
                $request->query('limit', 15)
            );

        $response = RookieWinnerHistoryResource::compute(
            $request,
            $rookie_winners_history
        )->get();

        return response()->json($response);
    }

    public function rookieOfTheDay(Request $request)
    {
        $rookies_of_the_days = RookieOfTheDay::query()
            ->orderByDesc('created_at')
            ->paginate($request->get('limit', 15));

        $response = RookieOfTheDayResource::compute(
            $request,
            $rookies_of_the_days
        )->get();

        return response()->json($response);
    }
}
