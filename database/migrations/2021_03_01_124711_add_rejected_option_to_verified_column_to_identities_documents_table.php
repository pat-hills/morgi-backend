<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddRejectedOptionToVerifiedColumnToIdentitiesDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE identities_documents CHANGE COLUMN verified verified ENUM('yes', 'no', 'rejected') NOT NULL DEFAULT 'no'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE identities_documents CHANGE COLUMN verified verified ENUM('yes', 'no') NOT NULL DEFAULT 'no'");
    }
}
