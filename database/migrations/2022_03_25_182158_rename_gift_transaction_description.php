<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameGiftTransactionDescription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\TransactionType::query()->where('type', 'gift')->update([
            'description_leader' => 'MOnthly Recurring GIft to <a href="/rookie-profile/{{rookie_id}}">{{rookie_full_name}}</a>'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
