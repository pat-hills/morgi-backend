<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveProofJsonFromGoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropColumn("proof_type");
            $table->boolean("has_image_proof");
            $table->boolean("has_video_proof");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropColumn("has_image_proof");
            $table->dropColumn("has_video_proof");
            $table->json("proof_type")->nullable()->after("status");
        });
    }
}
