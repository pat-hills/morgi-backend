<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeNewFkUnsigned extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activities_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('rookie_id')->change();
            $table->unsignedBigInteger('leader_id')->change();
            $table->unsignedBigInteger('admin_id')->change();
            $table->unsignedBigInteger('transaction_internal_id')->change();
            $table->unsignedBigInteger('internal_id')->change();
        });

        Schema::table('leaders_packages', function (Blueprint $table) {
            $table->unsignedBigInteger('transaction_id')->change();
        });

        Schema::table('leaders_packages_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('transaction_id')->change();

        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('rookie_id')->change();
            $table->unsignedBigInteger('leader_id')->change();
            $table->unsignedBigInteger('admin_id')->change();
            $table->unsignedBigInteger('refunded_by')->change();
            $table->unsignedBigInteger('referal_internal_id')->change();
            $table->unsignedBigInteger('internal_id')->change();
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
            $table->bigInteger('rookie_id')->change();
            $table->bigInteger('leader_id')->change();
            $table->bigInteger('admin_id')->change();
            $table->bigInteger('transaction_internal_id')->change();
            $table->bigInteger('internal_id')->change();
        });

        Schema::table('leaders_packages', function (Blueprint $table) {
            $table->bigInteger('transaction_id')->change();
        });

        Schema::table('leaders_packages_transactions', function (Blueprint $table) {
            $table->bigInteger('transaction_id')->change();

        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->bigInteger('rookie_id')->change();
            $table->bigInteger('leader_id')->change();
            $table->bigInteger('admin_id')->change();
            $table->bigInteger('refunded_by')->change();
            $table->bigInteger('referal_internal_id')->change();
            $table->bigInteger('internal_id')->change();
        });
    }
}
