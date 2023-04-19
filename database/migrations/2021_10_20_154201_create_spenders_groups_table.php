<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpendersGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spenders_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('days_on_site');
            $table->integer('total_charged');
            $table->integer('morgi_per_new_rookie');
            $table->integer('edit_morgi');
            $table->integer('monthly_morgi_spent');
            $table->integer('limit_daily_micromorgi');
            $table->integer('limit_monthly_micromorgi');
            $table->timestamps();
        });

        $insert = [
            [ 'name' => 'new', 'days_on_site' => 60, 'total_charged' => 100, 'morgi_per_new_rookie' => 100,
                'edit_morgi' => 250, 'monthly_morgi_spent' => 2500, 'limit_daily_micromorgi' => 100, 'limit_monthly_micromorgi' => 1000,
                'created_at' => now(), 'updated_at' => now() ],
            [ 'name' => 'lasting', 'days_on_site' => 150, 'total_charged' => 1000, 'morgi_per_new_rookie' => 250,
                'edit_morgi' => 500, 'monthly_morgi_spent' => 5000, 'limit_daily_micromorgi' => 250, 'limit_monthly_micromorgi' => 2500,
            'created_at' => now(), 'updated_at' => now() ],
            [ 'name' => 'trusted', 'days_on_site' => 365, 'total_charged' => 5000, 'morgi_per_new_rookie' => 250,
                'edit_morgi' => 1000, 'monthly_morgi_spent' => 10000, 'limit_daily_micromorgi' => 250, 'limit_monthly_micromorgi' => 5000,
                'created_at' => now(), 'updated_at' => now() ],
            [ 'name' => 'veteran', 'days_on_site' => 366, 'total_charged' => 5001, 'morgi_per_new_rookie' => 500,
                'edit_morgi' => 2000, 'monthly_morgi_spent' => 20000, 'limit_daily_micromorgi' => 500, 'limit_monthly_micromorgi' => 10000,
                'created_at' => now(), 'updated_at' => now() ],
        ];

        \App\Models\SpenderGroup::insert($insert);

        Schema::table('leaders', function (Blueprint $table) {
            $table->unsignedBigInteger('spender_group_id')->default(1)->after('has_approved_transaction');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spenders_groups');

        Schema::table('leaders', function (Blueprint $table) {
            $table->dropColumn('spender_group_id');
        });
    }
}
