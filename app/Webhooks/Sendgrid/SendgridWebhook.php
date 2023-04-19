<?php


namespace App\Webhooks\Sendgrid;


use App\Models\User;
use App\Models\UserEmailSent;
use Illuminate\Http\Request;

class SendgridWebhook
{
    public function sendgridWebhook(Request $request)
    {
        $events = $request->all();
        foreach ($events as $event){

            if(!isset($event['event'])){
                continue;
            }

            $user_email_sent = UserEmailSent::query()->where('sendgrid_message_id', $event['sg_message_id'])->first();
            if(isset($user_email_sent)){

                if($event['event']==='click'){
                    $user_email_sent->update(['clicked_at' => now()]);
                }

                if($event['event']==='open'){
                    $user_email_sent->update(['opened_at' => now()]);
                }
            }

            if($event['event']==='click' && isset($event['url'])){

                $user = User::query()->where('email', $event['email'])->first();

                if(!isset($user)){
                    continue;
                }

                if($event['url']===env('FACEBOOK_LINK')){
                    $field_to_update = 'clicked_facebook_link';
                }

                if($event['url']===env('TELEGRAM_LINK')){
                    $field_to_update = 'clicked_telegram_link';
                }

                if($event['url']===env('INSTAGRAM_LINK')){
                    $field_to_update = 'clicked_instagram_link';
                }

                if(!isset($field_to_update)){
                    continue;
                }

                $user->update([$field_to_update => true]);
            }
        }

        return response()->json([]);
    }
}
