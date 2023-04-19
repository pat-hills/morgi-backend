<?php

use App\Models\Country;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyToCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->string('currency')->default('USD')->after('dial');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('currency')->default('USD')->after('language');
        });

        $currencies = [
            'United Kingdom' => 'GBP',
            'Austria' => 'EUR',
            'Italy' => 'EUR',
            'Belgium' => 'EUR',
            'Latvia' => 'EUR',
            'Bulgaria' => 'EUR',
            'Lithuania' => 'EUR',
            'Croatia' => 'EUR',
            'Luxembourg' => 'EUR',
            'Cyprus' => 'EUR',
            'Malta' => 'EUR',
            'Czechia' => 'EUR',
            'Netherlands' => 'EUR',
            'Denmark' => 'EUR',
            'Poland' => 'EUR',
            'Estonia' => 'EUR',
            'Portugal' => 'EUR',
            'Finland' => 'EUR',
            'Romania' => 'EUR',
            'France' => 'EUR',
            'Slovakia' => 'EUR',
            'Germany' => 'EUR',
            'Slovenia' => 'EUR',
            'Greece' => 'EUR',
            'Spain' => 'EUR',
            'Hungary' => 'EUR',
            'Sweden' => 'EUR',
            'Ireland' => 'EUR'
        ];

        $countries_name = [
            'United Kingdom', 'Austria', 'Italy', 'Belgium', 'Latvia',
            'Bulgaria', 'Lithuania', 'Croatia', 'Luxembourg', 'Cyprus', 'Malta', 'Czechia', 'Netherlands', 'Denmark',
            'Poland', 'Estonia', 'Portugal', 'Finland', 'Romania', 'France', 'Slovakia',
            'Germany', 'Slovenia', 'Greece', 'Spain', 'Hungary', 'Sweden', 'Ireland'
        ];

        $countries = Country::whereIn('name', $countries_name)->get();

        foreach ($countries as $country){
            $country->update(['currency' => $currencies[$country->name]]);
        }

        $users = \App\Models\User::all();
        foreach ($users as $user){

            if($user->type!=='rookie'){
                continue;
            }

            $rookie = \App\Models\Rookie::find($user->id);
            if($rookie){
                $country = Country::find($rookie->country_id);
                $user->update(['currency' => $country->currency]);
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
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
}
