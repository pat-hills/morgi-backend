<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('external_id', 'internal_id');
            $table->renameColumn('referal_external_id', 'referal_internal_id');
            $table->renameColumn('to_user_id', 'rookie_id');
            $table->renameColumn('from_user_id', 'leader_id');
            $table->renameColumn('admin_id', 'refunded_by');
            $table->renameColumn('refund_date', 'refunded_at');
            $table->dropColumn('amount_morgi');
            $table->dropColumn('amount_micromorgi');
            $table->dropColumn('amount_dollars');
            $table->dropColumn('balance_transaction_id');
            $table->text('admin_description')->nullable(true)->after('notes');
            $table->double('morgi', 8, 2)->nullable(true)->after('from_user_id');
            $table->double('micromorgi', 8, 2)->nullable(true)->after('from_user_id');
            $table->double('dollars', 8, 2)->nullable(true)->after('from_user_id');
            $table->double('taxed_morgi', 8, 2)->nullable(true)->after('from_user_id');
            $table->double('taxed_micromorgi', 8, 2)->nullable(true)->after('from_user_id');
            $table->double('taxed_dollars', 8, 2)->nullable(true)->after('from_user_id');
            $table->enum('internal_status', ['pending', 'approved', 'declined'])->default('pending')->after('leader_payment_id');
            $table->text('internal_status_reason')->nullable(true)->after('internal_status');
            $table->bigInteger('internal_status_by')->nullable(true)->after('internal_status_reason');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->bigInteger('admin_id')->nullable(true)->after('leader_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('admin_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('internal_id', 'external_id');
            $table->renameColumn('referal_internal_id', 'referal_external_id');
            $table->renameColumn('rookie_id', 'to_user_id');
            $table->renameColumn('leader_id', 'from_user_id');
            $table->renameColumn('refunded_by', 'admin_id');
            $table->renameColumn('refunded_at', 'refund_date');
            $table->bigInteger('balance_transaction_id')->nullable();
            $table->double('amount_morgi', 8, 2)->nullable();
            $table->double('amount_micromorgi', 8, 2)->nullable();
            $table->double('amount_dollars', 8, 2)->nullable();
            $table->dropColumn('admin_description');
            $table->dropColumn('morgi');
            $table->dropColumn('micromorgi');
            $table->dropColumn('dollars');
            $table->dropColumn('taxed_morgi');
            $table->dropColumn('taxed_micromorgi');
            $table->dropColumn('taxed_dollars');
            $table->dropColumn('internal_status');
            $table->dropColumn('internal_status_reason');
            $table->dropColumn('internal_status_by');
        });
    }
}
