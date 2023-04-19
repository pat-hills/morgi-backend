<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTransactionTypeDescription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('transactions_types', function (Blueprint $table) {
            $table->string('description_rookie')->nullable(true)->change();
        });

        \App\Models\TransactionType::query()->where('type', 'bought_micromorgi')->update([
            'description_leader' => "Purchase of <amount_micromorgi> micromorgi",
            'description_rookie' => null,
        ]);

        \App\Models\TransactionType::query()->where('type', 'gift')->update([
            'description_leader' => "Recurring monthly gift of <amount_morgi> Morgis to <user_to>",
            'description_rookie' => "Recurring monthly gift of <amount_morgi> Morgis from <user_from>",
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions_types', function (Blueprint $table) {
            $table->string('description_rookie')->nullable(false)->change();
        });
    }
}
