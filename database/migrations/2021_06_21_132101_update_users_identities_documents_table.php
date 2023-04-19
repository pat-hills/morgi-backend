<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersIdentitiesDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_identities_documents', function (Blueprint $table) {

            $table->dropColumn('admin_id');
            $table->dropColumn('verified');
            $table->dropColumn('reason');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_identities_documents', function (Blueprint $table) {

            $table->bigInteger('admin_id')->nullable();
            $table->enum('verified', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reason')->nullable();

        });
    }
}
