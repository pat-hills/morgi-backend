<?php

use App\Models\UserIdentityDocument;
use App\Models\UserIdentityDocumentHistory;
use App\Models\UserIdentityDocumentPhoto;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateUsersIdentitiesDocumentsAndUsersIdentitiesDocumentsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('users_identities_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('front_path_id')->after('user_id');
            $table->unsignedBigInteger('back_path_id')->nullable(true)->after('front_path_id');
            $table->unsignedBigInteger('selfie_path_id')->after('back_path_id');
        });

        $id_cards = UserIdentityDocument::all();

        foreach ($id_cards as $id_card){
            $id_photo = UserIdentityDocumentPhoto::create([
                'type' => 'front',
                'user_id' => $id_card->user_id,
                'path_location' => $id_card->path_location,
                'status' => 'approved'
            ]);

            $id_b_photo = UserIdentityDocumentPhoto::create([
                'type' => 'selfie',
                'user_id' => $id_card->user_id,
                'path_location' => $id_card->path_location,
                'status' => 'approved'
            ]);

            $id_card->update(['front_path_id' => $id_photo->id, 'selfie_path_id' => $id_b_photo->id]);
        }

        Schema::table('users_identities_documents', function (Blueprint $table) {
            $table->dropColumn('path_location');
        });

        Schema::table('users_identities_documents_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('front_path_id')->after('user_id');
            $table->unsignedBigInteger('back_path_id')->nullable(true)->after('front_path_id');
            $table->unsignedBigInteger('selfie_path_id')->after('back_path_id');
        });

        DB::statement("ALTER TABLE users_identities_documents_histories CHANGE COLUMN verified status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");

        $id_history_cards = UserIdentityDocumentHistory::all();

        foreach ($id_history_cards as $id_h_card){
            $id_h_photo = UserIdentityDocumentPhoto::create([
                'type' => 'front',
                'user_id' => $id_h_card->user_id,
                'path_location' => $id_h_card->path_location,
                'status' => $id_h_card->status,
                'admin_id' => $id_h_card->admin_id,
                'decline_reason' => $id_h_card->reason
            ]);

            $id_h_b_photo = UserIdentityDocumentPhoto::create([
                'type' => 'selfie',
                'user_id' => $id_h_card->user_id,
                'path_location' => $id_h_card->path_location,
                'status' => $id_h_card->status,
                'admin_id' => $id_h_card->admin_id,
                'decline_reason' => $id_h_card->reason
            ]);

            $id_h_card->update(['front_path_id' => $id_h_photo->id, 'selfie_path_id' => $id_h_b_photo->id]);
        }

        Schema::table('users_identities_documents_histories', function (Blueprint $table) {
            $table->dropColumn('path_location');
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
            $table->string('path_location')->after('user_id');
        });

        $id_cards = UserIdentityDocument::all();

        foreach ($id_cards as $id_card){
            $path = UserIdentityDocumentPhoto::find($id_card->front_path_id);
            $id_card->update(['path_location' => $path]);
        }

        Schema::table('users_identities_documents', function (Blueprint $table) {
            $table->dropColumn('front_path_id');
            $table->dropColumn('back_path_id');
            $table->dropColumn('selfie_path_id');
        });


        DB::statement("ALTER TABLE users_identities_documents_histories CHANGE COLUMN status verified ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");

        Schema::table('users_identities_documents_histories', function (Blueprint $table) {
            $table->string('path_location')->after('user_id');
        });

        $id_h_cards = UserIdentityDocumentHistory::all();

        foreach ($id_h_cards as $id_h_card){
            $path_h = UserIdentityDocumentPhoto::find($id_h_card->front_path_id);
            $id_h_card->update(['path_location' => $path_h]);
        }

        Schema::table('users_identities_documents_histories', function (Blueprint $table) {
            $table->dropColumn('front_path_id');
            $table->dropColumn('back_path_id');
            $table->dropColumn('selfie_path_id');
        });
    }
}
