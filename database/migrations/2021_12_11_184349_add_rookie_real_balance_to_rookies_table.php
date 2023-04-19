<?php

use App\Models\Rookie;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRookieRealBalanceToRookiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies', function (Blueprint $table) {
            $table->double('untaxed_withdrawal_balance', 8, 2)->default(0)->after('withdrawal_balance');
            $table->double('untaxed_micro_morgi_balance', 8, 2)->default(0)->after('withdrawal_balance');
            $table->double('untaxed_morgi_balance', 8, 2)->default(0)->after('withdrawal_balance');
        });

        $rookies = Rookie::all();
        foreach ($rookies as $rookie){
            $rookie->update([
                'untaxed_withdrawal_balance' => $rookie->withdrawal_balance,
                'untaxed_micro_morgi_balance' => $rookie->micro_morgi_balance,
                'untaxed_morgi_balance' => $rookie->morgi_balance,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rookies', function (Blueprint $table) {
            $table->dropColumn('untaxed_withdrawal_balance');
            $table->dropColumn('untaxed_micro_morgi_balance');
            $table->dropColumn('untaxed_morgi_balance');
        });
    }
}
