<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DbTablesNamesRefractor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('bad_words', 'chat_bad_words');
        Schema::rename('blocked_users_histories', 'users_blocked_histories');
        Schema::rename('chat_report_categories', 'chat_reports_categories');
        if(Schema::hasTable('daily_rookies_of_the_days')){
            Schema::rename('daily_rookies_of_the_days', 'rookies_of_the_days');
        }
        Schema::rename('descriptions_histories', 'users_descriptions_histories');
        Schema::rename('event_photos', 'events_photos');
        Schema::rename('event_photos_histories', 'events_photos_histories');
        Schema::rename('event_requests', 'events_requests');
        Schema::rename('event_status', 'events_statuses');
        Schema::rename('failed_transactions', 'transactions_failed');
        Schema::rename('identities_documents', 'users_identities_documents');
        Schema::rename('leader_payment_methods', 'leaders_ccbill_data');
        Schema::rename('login_users_histories', 'users_login_histories');
        Schema::rename('merch_action_histories', 'merch_actions_histories');
        Schema::rename('notes', 'users_notes');
        Schema::rename('party_types', 'parties_types');
        Schema::rename('points', 'rookies_points');
        Schema::rename('profiles_saved', 'rookies_saved');
        Schema::rename('users_groups', 'users_ab_groups');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('chat_bad_words', 'bad_words');
        Schema::rename('users_blocked_histories', 'blocked_users_histories');
        Schema::rename('chat_reports_categories', 'chat_report_categories');
        if(Schema::hasTable('rookies_of_the_days')){
            Schema::rename('rookies_of_the_days', 'daily_rookies_of_the_days');
        }
        Schema::rename('users_descriptions_histories', 'descriptions_histories');
        Schema::rename('events_photos', 'event_photos');
        Schema::rename('events_photos_histories', 'event_photos_histories');
        Schema::rename('events_requests', 'event_requests');
        Schema::rename('events_statuses', 'event_status');
        Schema::rename('transactions_failed', 'failed_transactions');
        Schema::rename('users_identities_documents', 'identities_documents');
        Schema::rename('leaders_ccbill_data', 'leader_payment_methods');
        Schema::rename('users_login_histories', 'login_users_histories');
        Schema::rename('merch_actions_histories', 'merch_action_histories');
        Schema::rename('users_notes', 'notes');
        Schema::rename('parties_types', 'party_types');
        Schema::rename('rookies_points', 'points');
        Schema::rename('rookies_saved', 'profiles_saved');
        Schema::rename('users_ab_groups', 'users_groups');
    }
}
