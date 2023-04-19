<?php

use App\Models\TransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFineTypeToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE transactions CHANGE
            COLUMN type type ENUM('chat','gift','withdrawal','refund','bonus','withdrawal_rejected','bought_micromorgi','withdrawal_pending', 'fine')
            NOT NULL");

        DB::statement("ALTER TABLE transactions_types CHANGE
            COLUMN type type ENUM('chat','gift','withdrawal','refund','bonus','withdrawal_rejected','bought_micromorgi','rookie_block_leader','refund_bonus','chargeback','withdrawal_pending', 'fine')
            NOT NULL");

        TransactionType::create([
            'type' => "fine",
            'lang' => "EN",
            'description_leader' => "Morgi system decrease",
            'description_rookie' => "Morgi system decrease"
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE transactions CHANGE
            COLUMN type type ENUM('chat','gift','withdrawal','refund','bonus','withdrawal_rejected','bought_micromorgi','withdrawal_pending')
            NOT NULL");

        TransactionType::where('type', 'fine')->first()->delete();

        DB::statement("ALTER TABLE transactions_types CHANGE
            COLUMN type type ENUM('chat','gift','withdrawal','refund','bonus','withdrawal_rejected','bought_micromorgi','rookie_block_leader','refund_bonus','chargeback','withdrawal_pending')
            NOT NULL");

    }
}
