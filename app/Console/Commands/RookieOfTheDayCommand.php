<?php

namespace App\Console\Commands;

use App\Enums\RookieScoreEnum;
use App\Models\Rookie;
use App\Models\RookieOfTheDay;
use App\Models\RookieScore;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RookieOfTheDayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rookies:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rookie of the day job';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today_winners = RookieOfTheDay::query()
            ->whereDate('created_at', now())
            ->exists();

        if($today_winners){
            $this->error('That job already runned today!');
            return 0;
        }

        $rookie_points_query = RookieScoreEnum::SCORE_SELECT_QUERY;

        $rookie = Rookie::query()->selectRaw("
                rookies.id,
                $rookie_points_query as rookies_score,
                rookies_score.morgi_last_seven_days as amount_morgi,
                rookies_score.micromorgi_last_seven_days as amount_micro_morgi
            ")
            ->join('rookies_score', 'rookies_score.rookie_id', '=', 'rookies.id')
            ->join('users', 'users.id', '=', 'rookies.id')
            ->where('users.active', true)
            ->whereDate('users.last_activity_at', '>=', now()->subMonth()->toDateString())
            ->groupBy('rookies.id')
            ->orderByDesc('rookies_score')
            ->orderByDesc('amount_morgi')
            ->orderByDesc('amount_micro_morgi')
            ->first();

        if(!isset($rookie)){
            return 0;
        }

        RookieOfTheDay::create([
            'rookie_id' => $rookie->id,
            'morgi' => $rookie->amount_morgi,
            'micro_morgi' => $rookie->amount_micro_morgi,
            'score' => $rookie->rookies_score,
            'max_score' => RookieScore::getAttributesMaxScore()['total_max_score']
        ]);

        return 0;
    }
}
