<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement("ALTER TABLE transactions CHANGE
            COLUMN type type ENUM('chat','gift','withdrawal','refund','bonus','withdrawal_rejected','bought_micromorgi')
            NOT NULL");

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('amount_morgi');
            $table->dropColumn('amount_micromorgi');
            $table->bigInteger('to_user_id')->nullable(true)->change();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->double('amount_morgi')->after('transaction_type_id');
            $table->double('amount_micromorgi')->after('transaction_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        DB::statement("ALTER TABLE transactions CHANGE
            COLUMN type type ENUM('chat','gift','withdrawal','refund','bonus','withdrawal_rejected')
            NOT NULL");

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('amount_morgi');
            $table->dropColumn('amount_micromorgi');
            $table->bigInteger('to_user_id')->nullable(false)->change();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('amount_morgi')->after('transaction_type_id');
            $table->integer('amount_micromorgi')->after('transaction_type_id');
        });
    }
}
