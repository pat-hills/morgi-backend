<?php

namespace App\Console\Commands;

use App\Models\GlobalGroup;
use App\Models\Leader;
use App\Models\LeaderCcbillData;
use App\Utils\GlobalGroupUtils;
use Illuminate\Console\Command;

class GlobalIdAlgorithm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'algorithm:global_id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $select = [
            'leaders.id',
            'leaders.global_id',
            'leaders_ccbill_data.paymentAccount as ucid',
            'leaders_ccbill_data.firstName as first_name',
            'leaders_ccbill_data.lastName as last_name',
            'leaders_ccbill_data.billingCountry as location',
            'leaders_ccbill_data.email as billing_email'
        ];

        $ccbill_data = LeaderCcbillData::query()
            ->select($select)
            ->join('leaders', 'leaders.id', '=', 'leaders_ccbill_data.leader_id')
            ->whereNotNull('leaders_ccbill_data.paymentAccount')
            ->whereNull('global_id')
            ->groupBy('leader_id')
            ->get();

        foreach ($ccbill_data as $ccbill){

            if(!is_null(Leader::query()->find($ccbill->id)->global_id)){
                continue;
            }

            $match = LeaderCcbillData::query()
                ->where('paymentAccount', $ccbill->ucid)
                ->groupBy('leader_id')
                ->pluck('leader_id');

            if(!empty($match) and count($match) > 1){
                $global_id_collection = Leader::query()
                    ->whereIn('id', $match)
                    ->whereNotNull('global_id')
                    ->first();

                if(isset($global_id_collection)){

                    $global_id = $global_id_collection->global_id;

                }else{

                    $new_global_id = GlobalGroup::query()
                        ->create([
                            'global_id' => GlobalGroupUtils::createUniqueGlobalId($ccbill->id)
                        ]);

                    $global_id = $new_global_id->id;
                }

                foreach ($match as $user){
                    Leader::find($user)->update(['global_id' => $global_id]);
                }

            }

        }

        return 0;
    }
}
