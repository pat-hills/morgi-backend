<?php

use App\Models\Goal;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('goals', function (Blueprint $table) {

            $statuses = [
                Goal::STATUS_ACTIVE,
                Goal::STATUS_CANCELLED,
                Goal::STATUS_PENDING,
                Goal::STATUS_SUCCESSFUL,
                Goal::STATUS_AWAITING_PROOF,
                Goal::STATUS_PROOF_DECLINED,
                Goal::STATUS_PROOF_PENDING_APPROVAL
            ];

            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->longText('details');
            $table->decimal('target_amount', 10)->comment('Amount in micro-morgie within the constraint of goaltypes\' min max value');
            $table->enum('currency_type', ['morgi', 'micro_morgi'])->default('micro_morgi');
            $table->unsignedBigInteger('rookie_id');
            $table->dateTime('start_date', 0)->nullable();
            $table->dateTime('end_date', 0);
            $table->longText('thank_you_message');
            $table->jsonb('proof_type')->comment('array proof type');
            $table->text('proof_note');
            $table->date('cancelled_at')->nullable();
            $table->enum('cancelled_reason', [
                'goal_not_reached',
                'cancelled_by_user',
                'other'
            ])->nullable();
            $table->unsignedBigInteger('type_id');
            $table->enum('status',$statuses)->default(Goal::STATUS_PENDING);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goals');
    }
}
