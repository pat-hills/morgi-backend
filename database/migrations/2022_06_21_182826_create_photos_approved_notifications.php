<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhotosApprovedNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\NotificationType::query()->create(['user_type' => 'both',
            'type' => 'photos_approved', 'title' => 'Photos approved!',
            'content' => 'Congratulations, your photos was approved!']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Models\NotificationType::query()->where('type', 'photos_approved')->delete();
    }
}
