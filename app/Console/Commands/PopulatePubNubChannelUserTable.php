<?php

namespace App\Console\Commands;

use App\Models\PubnubChannel;
use Illuminate\Console\Command;
use App\Models\PubnubChannelUser;

class PopulatePubNubChannelUserTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pubnubchannelusers:populate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'populate pubnubchannel_users table with data from pubnubs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $pubnubs = PubnubChannel::all();

        foreach ($pubnubs as $pubnub) {
            PubnubChannelUser::upsert([
                ['channel_id' => $pubnub->id, 'user_id' => $pubnub->rookie_id],
                ['channel_id' => $pubnub->id, 'user_id' => $pubnub->leader_id]
            ], ['channel_id', 'user_id'], []);
        }
        return 0;
    }
}
