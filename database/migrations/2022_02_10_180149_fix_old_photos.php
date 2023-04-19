<?php

use App\Models\Photo;
use App\Models\RookieSeen;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixOldPhotos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = $users = User::query()->selectRaw("users.id")
            ->join('photos', 'users.id', '=', 'photos.user_id')
            ->where('users.type', 'rookie')
            ->groupBy('users.id')
            ->get();

        foreach ($users as $user){

            $user_has_avatar = Photo::query()
                ->where('user_id', $user->id)
                ->where('main', true)
                ->exists();

            if(!$user_has_avatar){

                //Set the latest photo as default
                $latest_photo = Photo::query()->where('user_id', $user->id)->latest()->first();
                if(isset($latest_photo)){
                    $latest_photo->update(['main' => true]);
                }
            }
        }
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
