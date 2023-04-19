<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatedIdentitiesDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('identities_documents', function (Blueprint $table) {
            $table->dropColumn('image_path');
            $table->string('path_location')->after('admin_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('identities_documents', function (Blueprint $table) {
            $table->dropColumn('path_location');
            $table->string('image_path')->after('admin_id');
        });
    }
}
