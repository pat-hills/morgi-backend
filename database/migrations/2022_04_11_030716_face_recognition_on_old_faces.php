<?php

use App\FaceRecognition\AwsFaceRekognitionFacesUtils;
use App\Models\Photo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FaceRecognitionOnOldFaces extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('has_face_processed')->default(false)->after('total_subscriptions_count');
        });

        /*if(env('APP_ENV')==='prod' || env('APP_ENV')==='production'){
            $photos = Photo::query()
                ->where('is_face_recognition_processed', false)
                ->where('main', true)
                ->get();

            foreach ($photos as $photo){
                try {
                    (new AwsFaceRekognitionFacesUtils())->storePhotoFaces($photo);
                }catch (Exception $exception){
                }
            }
        }*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('has_face_processed');
        });
    }
}
