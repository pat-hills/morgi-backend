<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FillComplaintsTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        $types = [
            "offensive_image_video",
            "suspicious_text",
            "offensive_text",
            "spamming",
            "harassment"
        ];

        foreach ($types as $type){
            \App\Models\ComplaintType::create([
                'name' => $type
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
        //
        \App\Models\ComplaintType::truncate();
    }
}
