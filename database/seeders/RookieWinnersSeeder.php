<?php

namespace Database\Seeders;

use App\Models\Rookie;
use App\Models\RookieWinnerHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RookieWinnersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RookieWinnerHistory::truncate();
        for($i = 0; $i<150; $i++){


            $rookies = Rookie::join('users', 'users.id', '=', 'rookies.id')
                ->where('users.status', '=', 'accepted')
                ->inRandomOrder()->limit(3)->get();

            foreach ($rookies as $rookie){
                RookieWinnerHistory::create([
                    'rookie_id' => $rookie->id,
                    'amount' => rand(200, 300),
                    'balance_transaction_id' => rand(300, 40000),
                    'win_at' =>  Carbon::now()->subDays($i*7),
                    'seen_at' =>  Carbon::now()->subDays($i*7)
                ]);
            }

        }
    }
}
