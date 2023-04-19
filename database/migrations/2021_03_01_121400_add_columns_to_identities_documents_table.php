<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToIdentitiesDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('identities_documents', function (Blueprint $table) {
            $table->bigInteger('admin_id')->after('user_id')->nullable();
            $table->text('reason')->after('verified')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('identities_documents', 'admin_id') && Schema::hasColumn('identities_documents', 'current') && Schema::hasColumn('identities_documents', 'reason')){
            Schema::table('identities_documents', function (Blueprint $table) {
                $table->dropColumn('admin_id');
                $table->dropColumn('current');
                $table->dropColumn('reason');
            });
        }
    }
}
