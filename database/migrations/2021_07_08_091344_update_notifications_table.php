<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications_types', function (Blueprint $table) {
            $table->dropColumn('content_template_key_name');
            $table->renameColumn('content_template', 'content');
            $table->renameColumn('name', 'type');
            $table->enum('user_type', ['rookie', 'leader'])->after('id');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('seen');
            $table->timestamp('seen_at')->after('notification_type_id')->nullable(true);
            $table->dropColumn('ref_entity_1');
            $table->dropColumn('ref_entity_1_id');
            $table->dropColumn('ref_entity_2');
            $table->dropColumn('ref_entity_2_id');
            $table->bigInteger('ref_user_id')->nullable(true)->after('notification_type_id');
            $table->double('amount_micromorgi')->nullable(true)->after('notification_type_id');
            $table->double('amount_morgi')->nullable(true)->after('notification_type_id');
            $table->double('old_amount')->nullable(true)->after('notification_type_id');
            $table->double('amount')->nullable(true)->after('notification_type_id');
            $table->string('currency')->nullable(true)->after('notification_type_id');
            $table->timestamp('event_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications_types', function (Blueprint $table) {
            $table->text('content_template_key_name');
            $table->renameColumn('type', 'name');
            $table->renameColumn('content', 'content_template');
            $table->dropColumn('user_type');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('seen_at');
            $table->timestamp('seen')->after('notification_type_id')->nullable(true);
            $table->bigInteger('ref_entity_1');
            $table->bigInteger('ref_entity_1_id');
            $table->bigInteger('ref_entity_2');
            $table->bigInteger('ref_entity_2_id');
            $table->dropColumn('ref_user_id');
            $table->dropColumn('amount_micromorgi');
            $table->dropColumn('amount_morgi');
            $table->dropColumn('old_amount');
            $table->dropColumn('amount');
            $table->dropColumn('event_at');
            $table->dropColumn('currency');
        });
    }
}
