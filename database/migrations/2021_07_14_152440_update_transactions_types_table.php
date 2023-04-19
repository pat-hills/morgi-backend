<?php

use App\Models\TransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTransactionsTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions_types', function (Blueprint $table) {
            $table->renameColumn('description', 'description_rookie');
            $table->text('description_leader')->nullable(true)->after('type');
        });

        TransactionType::truncate();

        $types = [
            ['type' => 'gift', 'lang' => 'EN', 'description_rookie' => 'Recurring Morgi montly gift from <user_from>',
                'description_leader' => 'Recurring Morgi montly gift to <user_to>'],
            ['type' => 'chat', 'lang' => 'EN', 'description_rookie' => 'Gift Micro Morgi on chat from <user_from>',
                'description_leader' => 'Gift Micro Morgi on chat to <user_to>'],
            ['type' => 'bought_micromorgi', 'lang' => 'EN', 'description_rookie' => 'Purchase of n.<amount_micromorgi> micromorgi (<amount_dollars>$)',
                'description_leader' => 'Purchase of n.<amount_micromorgi> micromorgi (<amount_dollars>$)'],
            ['type' => 'refund', 'lang' => 'EN', 'description_rookie' => 'System refund following an error to <user_to> (#<referal_external_id>)',
                'description_leader' => 'System refund following an error to <user_to> (#<referal_external_id>)'],
            ['type' => 'withdrawal', 'lang' => 'EN', 'description_rookie' => 'Withdrawal <payment_method> (<payment_info>)',
                'description_leader' => null],
            ['type' => 'withdrawal_rejected', 'lang' => 'EN', 'description_rookie' => 'Withdrawal <payment_method> rejected (original payment: #<referal_external_id>)',
                'description_leader' => null],
            ['type' => 'bonus', 'lang' => 'EN', 'description_rookie' => 'Bonus from Morgi',
                'description_leader' => 'Bonus from Morgi'],
        ];

        foreach ($types as $type){

            $transaction_type = TransactionType::where('type', $type['type'])->where('lang', $type['lang'])->first();

            if(!$transaction_type){
                TransactionType::create($type);
                continue;
            }

            $transaction_type->update($type);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions_types', function (Blueprint $table) {
            $table->renameColumn('description_rookie', 'description');
            $table->dropColumn('description_leader');
        });
    }
}
