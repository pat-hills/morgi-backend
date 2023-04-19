<?php

use App\Models\Photo;
use App\Models\Video;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSoftDeletes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Video::query()->whereNotNull('deleted_at')->delete();
        Photo::query()->whereNotNull('deleted_at')->delete();

        Schema::table("videos", function ($table) {
            $table->dropSoftDeletes();
        });

        Schema::table("photos", function ($table) {
            $table->dropSoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("videos", function ($table) {
            $table->softDeletes();
        });

        Schema::table("photos", function ($table) {
            $table->softDeletes();
        });
    }
}
