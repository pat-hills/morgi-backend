<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEventRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_requests', function (Blueprint $table) {
            $table->dropColumn('event_audience_id');
            $table->enum('event_audience', ['students', 'housewives', 'business_people', 'other'])->after('id');
            $table->dropColumn('guests_count');
            $table->bigInteger('party_type_id')->nullable()->after('event_type');
            $table->dropColumn('reason');
            $table->text('other_reason')->nullable()->change();
            $table->text('state')->nullable()->change();
        });

        Schema::table('event_requests', function (Blueprint $table) {
            $table->enum('guests_count', ['1-10', '11-20', '21-30', '31-40', 'more_than_40'])->after('event_audience');
            $table->enum('reason', ['morgi_promote', 'financial_help', 'other'])->after('party_type_id');
        });

        Schema::create('party_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key_name');
        });

        /*\Illuminate\Support\Facades\DB::table('party_types')->insert([
            ['key_name' => 'house_party', 'name' => 'House Party'], ['key_name' => 'club_party', 'name' => 'Club Party'],
            ['key_name' => 'beach_party', 'name' => 'Beach Party'], ['key_name' => 'pool_party', 'name' => 'Pool Party'],
            ['key_name' => 'cocktail_party', 'name' => 'Cocktail Party'], ['key_name' => 'pajama_party', 'name' => 'Pajama Party'],
            ['key_name' => 'causal_party', 'name' => 'Causal Party'], ['key_name' => 'formal_party', 'name' => 'Formal Party'],
            ['key_name' => 'street_party', 'name' => 'Street Party'], ['key_name' => 'college_party', 'name' => 'College Party'],
            ['key_name' => 'other', 'name' => 'Other']
        ]);*/

        Schema::dropIfExists('event_audiences');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_requests', function (Blueprint $table) {
            $table->dropColumn('event_audience');
            $table->bigInteger('event_audience_id')->after('id');
            $table->dropColumn('guests_count');
            $table->dropColumn('party_type_id');
            $table->dropColumn('reason');
        });

        Schema::table('event_requests', function (Blueprint $table) {
            $table->integer('guests_count')->after('event_audience_id');
            $table->integer('reason');
        });

        Schema::create('event_audiences', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key_name');
        });

        Schema::dropIfExists('party_types');
    }
}
