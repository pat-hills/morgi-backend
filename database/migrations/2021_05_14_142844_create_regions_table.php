<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('country_id');
        });

        /*$usa_regions = [
            ['name' => 'Alabama'],
            ['name' => 'Alaska'],
            ['name' => 'American Samoa'],
            ['name' => 'Arizona'],
            ['name' => 'Arkansas'],
            ['name' => 'California'],
            ['name' => 'Colorado'],
            ['name' => 'Connecticut'],
            ['name' => 'Delaware'],
            ['name' => 'District of Columbia'],
            ['name' => 'Florida'],
            ['name' => 'Georgia'],
            ['name' => 'Guam'],
            ['name' => 'Hawaii'],
            ['name' => 'Idaho'],
            ['name' => 'Illinois'],
            ['name' => 'Indiana'],
            ['name' => 'Iowa'],
            ['name' => 'Kansas'],
            ['name' => 'Kentucky'],
            ['name' => 'Louisiana'],
            ['name' => 'Maine'],
            ['name' => 'Maryland'],
            ['name' => 'Massachusetts'],
            ['name' => 'Michigan'],
            ['name' => 'Minnesota'],
            ['name' => 'Mississippi'],
            ['name' => 'Missouri'],
            ['name' => 'Montana'],
            ['name' => 'Nebraska'],
            ['name' => 'Nevada'],
            ['name' => 'New Hampshire'],
            ['name' => 'New Jersey'],
            ['name' => 'New Mexico'],
            ['name' => 'New York'],
            ['name' => 'North Carolina'],
            ['name' => 'North Dakota'],
            ['name' => 'Northern Mariana Islands'],
            ['name' => 'Ohio'],
            ['name' => 'Oklahoma'],
            ['name' => 'Oregon'],
            ['name' => 'Pennsylvania'],
            ['name' => 'Puerto Rico'],
            ['name' => 'Rhode Island'],
            ['name' => 'South Carolina'],
            ['name' => 'South Dakota'],
            ['name' => 'Tennessee'],
            ['name' => 'Texas'],
            ['name' => 'U.S. Minor Outlying Islands'],
            ['name' => 'Utah'],
            ['name' => 'Vermont'],
            ['name' => 'Virginia'],
            ['name' => 'Washington'],
            ['name' => 'West Virginia'],
            ['name' => 'Wisconsin'],
            ['name' => 'Wyoming']
        ];

        $australian_regions = [
            ['name' => 'New South Wales'],
            ['name' => 'Northern Territory'],
            ['name' => 'Queensland'],
            ['name' => 'South Australia'],
            ['name' => 'Tasmania'],
            ['name' => 'Victoria'],
            ['name' => 'Western Australia']
        ];

        $canada_regions = [
            ['name' => 'British Columbia'],
            ['name' => 'Alberta'],
            ['name' => 'Saskatchewan'],
            ['name' => 'Manitoba'],
            ['name' => 'Ontario'],
            ['name' => 'Quebec'],
            ['name' => 'New Brunswick'],
            ['name' => 'Prince Edward Island'],
            ['name' => 'Nova Scotia'],
            ['name' => 'Newfoundland and Labrador'],
            ['name' => 'Yukon'],
            ['name' => 'Northwest Territories'],
            ['name' => 'Nunavut']
        ];

        $usa_id = DB::table('countries')->where('name', 'United States')->first()->id;
        $autralia_id = DB::table('countries')->where('name', 'Australia')->first()->id;
        $canada_id = DB::table('countries')->where('name', 'Canada')->first()->id;

        foreach ($usa_regions as $region){
            DB::table('regions')->insert(['name' => $region['name'], 'country_id' => $usa_id]);
        }

        foreach ($australian_regions as $region){
            DB::table('regions')->insert(['name' => $region['name'], 'country_id' => $autralia_id]);
        }

        foreach ($canada_regions as $region){
            DB::table('regions')->insert(['name' => $region['name'], 'country_id' => $canada_id]);
        }*/


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('regions');
    }
}
