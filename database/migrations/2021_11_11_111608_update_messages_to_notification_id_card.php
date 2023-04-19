<?php

use App\Models\NotificationType;
use Illuminate\Database\Migrations\Migration;

class UpdateMessagesToNotificationIdCard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $notifications = NotificationType::query()
            ->whereIn('type', [
                'front_id_card_rejected',
                'back_id_card_rejected',
                'selfie_id_card_rejected'
            ])
            ->get();

        foreach ($notifications as $notification){

            $notification->update([
                'content' => '<reason>'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        $notifications = NotificationType::query()
            ->whereIn('type', [
                'front_id_card_rejected',
                'back_id_card_rejected',
                'selfie_id_card_rejected'
            ])
            ->get();

        foreach ($notifications as $notification){

            switch ($notification->type) {
                case 'front_id_card_rejected':
                    $content = 'The Front photo of your Identity Document has been rejected, reason <reason>';
                    break;
                case 'back_id_card_rejected':
                    $content = 'The Back photo of your Identity Document has been rejected, reason <reason>';
                    break;
                case'selfie_id_card_rejected':
                    $content = 'The Selfie photo of your Identity Document has been rejected, reason <reason>';
                    break;
            }

            $notification->update([
                'content' => $content
            ]);
        }
    }
}
