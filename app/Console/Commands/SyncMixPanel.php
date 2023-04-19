<?php

namespace App\Console\Commands;

use App\Mixpanel\Utils\UserProfileUtils;
use App\Models\User;
use Illuminate\Console\Command;

class SyncMixPanel extends Command
{
    protected $signature = 'mixpanel:sync';

    protected $description = 'Command description';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::query()->whereIn('type', ['rookie', 'leader'])->get();
        $this->alert("Found " . $users->count() . " users!");

        foreach ($users as $user){

            $this->info("Sync user {$user->id} | " . now()->timestamp);

            try {
                UserProfileUtils::storeOrUpdate($user->id);
            }catch (\Exception $exception){
                $this->error("Unable to sync user: {$user->id}");
            }
        }

        $this->alert("Mix panel sync finished!");
        return 0;
    }
}
