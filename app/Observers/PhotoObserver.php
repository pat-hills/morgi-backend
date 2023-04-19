<?php

namespace App\Observers;

use App\Models\Photo;
use App\Models\RookieSeen;
use App\Models\User;

class PhotoObserver
{
    /**
     * Handle the Photo "created" event.
     *
     * @param  \App\Models\Photo  $photo
     * @return void
     */
    public function created(Photo $photo)
    {
        if($photo->main===true){
            Photo::query()->where('user_id', $photo->user_id)
                ->where('id', '!=', $photo->id)
                ->update(['main' => false]);

            return;
        }

        $has_main_photo = Photo::query()->where('user_id', $photo->user_id)
            ->where('id', '!=', $photo->id)
            ->where('main', true)
            ->exists();

        if(!$has_main_photo){
            $photo->update(['main' => true]);
        }
    }

    /**
     * Handle the Photo "deleted" event.
     *
     * @param  \App\Models\Photo  $photo
     * @return void
     */
    public function deleted(Photo $photo)
    {
        $user_has_avatar = Photo::query()
            ->where('user_id', $photo->user_id)
            ->where('main', true)
            ->exists();

        if($photo->main || !$user_has_avatar){

            //Set the latest photo as default
            $latest_photo = Photo::query()->where('user_id', $photo->user_id)->latest()->first();
            if(isset($latest_photo)){
                $latest_photo->update(['main' => true]);
            }else{

                /*
                 * Delete rows in rookies_seen because the rookie does not has images
                 */
                $user = User::find($photo->user_id);
                if($user->type==='rookie'){
                    RookieSeen::where('rookie_id', $user->id)->delete();
                }
            }
        }

    }
}
