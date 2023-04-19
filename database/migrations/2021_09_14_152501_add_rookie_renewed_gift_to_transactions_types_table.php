<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRookieRenewedGiftToTransactionsTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\NotificationType::create(['user_type' => 'rookie', 'type' => 'rookie_renewed_gift',
            'title' => 'Renewed gift!', 'content' => '<ref_username> is once again gifting you with <amount_morgi> Morgi per month!']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Models\NotificationType::where('type', 'rookie_renewed_gift')->delete();
    }
}
