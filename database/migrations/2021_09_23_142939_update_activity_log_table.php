<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateActivityLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activities_logs', function (Blueprint $table) {
            $table->renameColumn('to_user_id', 'rookie_id');
            $table->renameColumn('refund_date', 'refunded_at');
            $table->renameColumn('from_user_id', 'leader_id');
            $table->renameColumn('external_id', 'internal_id');
            $table->renameColumn('referal_external_id', 'transaction_internal_id');
            $table->dropColumn('amount_morgi');
            $table->dropColumn('amount_micromorgi');
            $table->double('morgi', 8, 2)->nullable(true)->after('from_user_id');
            $table->double('micromorgi', 8, 2)->nullable(true)->after('from_user_id');
            $table->double('dollars', 8, 2)->nullable(true)->after('from_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities_logs', function (Blueprint $table) {
            $table->renameColumn('internal_id', 'external_id');
            $table->renameColumn('refunded_at', 'refund_date');
            $table->renameColumn('transaction_internal_id', 'referal_external_id');
            $table->double('amount_micromorgi', 8, 2)->nullable();
            $table->double('amount_morgi', 8, 2)->nullable();
            $table->dropColumn('morgi');
            $table->dropColumn('micromorgi');
            $table->dropColumn('dollars');
            $table->renameColumn('rookie_id', 'to_user_id');
            $table->renameColumn('leader_id', 'from_user_id');
        });
    }
}
