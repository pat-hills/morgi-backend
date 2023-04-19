<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\NotificationType;

class AddNotificationTypesForCardIdVerification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        NotificationType::create([
            'user_type' => 'both',
            'type' => 'front_id_card_rejected',
            'title' => 'Front photo Identity Document rejected!',
            'content' => 'The Front photo of your Identity Document has been rejected, reason <reason>'
        ]);

        NotificationType::create([
            'user_type' => 'both',
            'type' => 'back_id_card_rejected',
            'title' => 'Back photo Identity Document rejected!',
            'content' => 'The Back photo of your Identity Document has been rejected, reason <reason>'
        ]);

        NotificationType::create([
            'user_type' => 'both',
            'type' => 'selfie_id_card_rejected',
            'title' => 'Selfie photo Identity Document rejected!',
            'content' => 'The Selfie photo of your Identity Document has been rejected, reason <reason>'
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
