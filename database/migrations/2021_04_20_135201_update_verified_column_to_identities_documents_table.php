<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateVerifiedColumnToIdentitiesDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE identities_documents CHANGE COLUMN verified verified ENUM('approved', 'pending', 'rejected') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE identities_documents CHANGE COLUMN verified verified ENUM('yes', 'no', 'rejected') NOT NULL DEFAULT 'no'");
    }
}
