<?php

use App\Enums\ComplaintTypeEnum;
use App\Models\ComplaintType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeyNameToComplaintsTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('complaints_types', function (Blueprint $table) {
            $table->string('key_name')->after('name');
        });

        ComplaintType::truncate();
        foreach (ComplaintTypeEnum::TYPES as $key_name => $type){
            ComplaintType::create(['key_name' => $key_name, 'name' => $type]);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        ComplaintType::truncate();
        foreach (ComplaintTypeEnum::TYPES as $key_name => $type){
            ComplaintType::create(['name' => $key_name]);
        }

        Schema::table('complaints_types', function (Blueprint $table) {
            $table->dropColumn('key_name');
        });
    }
}
