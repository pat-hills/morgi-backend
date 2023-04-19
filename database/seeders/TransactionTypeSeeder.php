<?php

namespace Database\Seeders;

use App\Models\TransactionType;
use Illuminate\Database\Seeder;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        TransactionType::truncate();

        $types = [
            ['type' => 'gift', 'lang' => 'EN', 'description_leader' => 'Recurring Morgi montly gift from <user_from>'],
            ['type' => 'chat', 'lang' => 'EN', 'description_rookie' => 'Gift Micro Morgi on chat from <user_from>'],
            ['type' => 'bought_micromorgi', 'lang' => 'EN', 'description_leader' => 'Purchase of n.<amount_micromorgi> micromorgi (<amount_dollars>)'],
            ['type' => 'refund', 'lang' => 'EN', 'description_rookie' => 'System refund following an error to <user_to> (#<referal_external_id>)'],
            ['type' => 'withdrawal', 'lang' => 'EN', 'description_rookie' => 'Withdrawal <payment_method> (<payment_info>)'],
            ['type' => 'withdrawal_rejected', 'lang' => 'EN', 'description_rookie' => 'Withdrawal <payment_method> rejected (original payment: #<referal_external_id>)'],
            ['type' => 'bonus', 'lang' => 'EN', 'description_rookie' => 'Bonus from Morgi'],
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
}
