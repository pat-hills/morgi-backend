<?php

namespace App\Console\Commands;

use App\Http\Resources\BroadcastResource;
use App\Http\Resources\ChatAttachmentResource;
use App\Http\Resources\CityResource;
use App\Http\Resources\CountryResource;
use App\Http\Resources\GenderResource;
use App\Http\Resources\GivebackResource;
use App\Http\Resources\GoalResource;
use App\Http\Resources\LeaderCcbillDataResource;
use App\Http\Resources\LeaderResource;
use App\Http\Resources\MicromorgiPackagesResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\PathResource;
use App\Http\Resources\PhotoHistoryResource;
use App\Http\Resources\PhotoResource;
use App\Http\Resources\PubnubBroadcastResource;
use App\Http\Resources\PubnubChannelResource;
use App\Http\Resources\PubnubGroupResource;
use App\Http\Resources\RegionResource;
use App\Http\Resources\RookieOfTheDayResource;
use App\Http\Resources\RookieResource;
use App\Http\Resources\RookieWinnerHistoryResource;
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\TransactionHandshakeResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\VideoHistoriesResource;
use App\Http\Resources\VideoResource;
use App\Models\Broadcast;
use App\Models\ChatAttachment;
use App\Models\City;
use App\Models\Country;
use App\Models\Gender;
use App\Models\Giveback;
use App\Models\Goal;
use App\Models\Leader;
use App\Models\LeaderCcbillData;
use App\Models\MicromorgiPackage;
use App\Models\Notification;
use App\Models\Path;
use App\Models\Photo;
use App\Models\PhotoHistory;
use App\Models\PubnubBroadcast;
use App\Models\PubnubChannel;
use App\Models\PubnubGroup;
use App\Models\Region;
use App\Models\Rookie;
use App\Models\RookieOfTheDay;
use App\Models\RookieWinnerHistory;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\TransactionHandshake;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PerformanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:performance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

    private function toTime(array  $array)
    {
        $time = 0;
        foreach ($array as $item){
            $time += $item['time'];
        }

        return $time/1000;
    }

    public function handle()
    {
        $request = new Request();
        $limit = 1;
        DB::connection()->enableQueryLog();


        //User
        $users = User::query()->limit($limit)->get();
        DB::flushQueryLog();
        UserResource::compute(
            $request,
            $users
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($users);
        echo ("User \t\t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //Broadcast
        $broadcasts = Broadcast::query()->limit($limit)->get();
        DB::flushQueryLog();
        BroadcastResource::compute(
            $request,
            $broadcasts
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($broadcasts);
        echo ("Broadcast \t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //ChatAttachment
        $chatAttachments = ChatAttachment::query()->limit($limit)->get();
        DB::flushQueryLog();
        ChatAttachmentResource::compute(
            $request,
            $chatAttachments
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($chatAttachments);
        echo ("ChatAttachment \t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //City
        $cities = City::query()->limit($limit)->get();
        DB::flushQueryLog();
        CityResource::compute(
            $request,
            $cities
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($cities);
        echo ("City \t\t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //Country
        $countries = Country::query()->limit($limit)->get();
        DB::flushQueryLog();
        CountryResource::compute(
            $request,
            $countries
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($countries);
        echo ("Country \t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //Gender
        $genders = Gender::query()->limit($limit)->get();
        DB::flushQueryLog();
        GenderResource::compute(
            $request,
            $genders
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($genders);
        echo ("Genders \t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //GivebackResource
        $givebacks = Giveback::query()->limit($limit)->get();
        DB::flushQueryLog();
        GivebackResource::compute(
            $request,
            $givebacks
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($givebacks);
        echo ("Giveback \t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //GoalResource
        $goals = Goal::query()->limit($limit)->get();
        DB::flushQueryLog();
        GoalResource::compute(
            $request,
            $goals
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($goals);
        echo ("Goal \t\t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //LeaderCcbillData
        $leaderccbills = LeaderCcbillData::query()->limit($limit)->get();
        DB::flushQueryLog();
        LeaderCcbillDataResource::compute(
            $request,
            $leaderccbills
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($leaderccbills);
        echo ("LeaderCcbill \t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //Leader
        $leaders = Leader::query()->limit($limit)->get();
        DB::flushQueryLog();
        LeaderResource::compute(
            $request,
            $leaders
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($leaders);
        echo ("Leaders \t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //MicromorgiPackages
        $micro_morgi_packages = MicromorgiPackage::query()->limit($limit)->get();
        DB::flushQueryLog();
        MicromorgiPackagesResource::compute(
            $request,
            $micro_morgi_packages
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($micro_morgi_packages);
        echo ("MicroMorgiPackages \t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //Notification
        $notifications = Notification::query()->limit($limit)->get();
        DB::flushQueryLog();
        NotificationResource::compute(
            $request,
            $notifications
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($notifications);
        echo ("Notification \t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //Path
        $paths = Path::query()->limit($limit)->get();
        DB::flushQueryLog();
        PathResource::compute(
            $request,
            $paths
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($paths);
        echo ("Path \t\t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //PhotoHistory
        $photo_histories = PhotoHistory::query()->limit($limit)->get();
        DB::flushQueryLog();
        PhotoHistoryResource::compute(
            $request,
            $photo_histories
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($photo_histories);
        echo ("PhotoHistory \t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //Photo
        $photo = Photo::query()->limit($limit)->get();
        DB::flushQueryLog();
        PhotoResource::compute(
            $request,
            $photo
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($photo);
        echo ("Photo \t\t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //PubnubBroadcast
        $pubnubs_broadcasts = PubnubBroadcast::query()->limit($limit)->get();
        DB::flushQueryLog();
        PubnubBroadcastResource::compute(
            $request,
            $pubnubs_broadcasts
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($pubnubs_broadcasts);
        echo ("PubnubBroadcast \t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //PubnubChannel
        $pubnubs_channels = PubnubChannel::query()->limit($limit)->get();
        DB::flushQueryLog();
        PubnubChannelResource::compute(
            $request,
            $pubnubs_channels
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($pubnubs_channels);
        echo ("PubnubChannel \t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //PubnubGroup
        $pubnubs_groups = PubnubGroup::query()->limit($limit)->get();
        DB::flushQueryLog();
        PubnubGroupResource::compute(
            $request,
            $pubnubs_groups
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($pubnubs_groups);
        echo ("PubnubGroup \t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //Region
        $regions = Region::query()->limit($limit)->get();
        DB::flushQueryLog();
        RegionResource::compute(
            $request,
            $regions
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($regions);
        echo ("Region \t\t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //RookieOfTheDay
        $rookies_of_the_day = RookieOfTheDay::query()->limit($limit)->get();
        DB::flushQueryLog();
        RookieOfTheDayResource::compute(
            $request,
            $rookies_of_the_day
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($rookies_of_the_day);
        echo ("RookieOfTheDay \t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //Rookie
        $rookies = Rookie::query()->limit($limit)->get();
        DB::flushQueryLog();
        RookieResource::compute(
            $request,
            $rookies
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($rookies);
        echo ("Rookie \t\t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //RookieWinner
        $rookies_winners = RookieWinnerHistory::query()->limit($limit)->get();
        DB::flushQueryLog();
        RookieWinnerHistoryResource::compute(
            $request,
            $rookies_winners
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($rookies_winners);
        echo ("RookieWinner \t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //Subscription
        $subscriptions = Subscription::query()->limit($limit)->get();
        DB::flushQueryLog();
        SubscriptionResource::compute(
            $request,
            $subscriptions
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($subscriptions);
        echo ("Subscription \t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //TransactionHandShake
        $transaction_hand_shake = TransactionHandshake::query()->limit($limit)->get();
        DB::flushQueryLog();
        TransactionHandshakeResource::compute(
            $request,
            $transaction_hand_shake
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($transaction_hand_shake);
        echo ("TransactionHandShake \t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //Transaction
        $transactions = Transaction::query()->limit($limit)->get();
        DB::flushQueryLog();
        TransactionResource::compute(
            $request,
            $transactions
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($transactions);
        echo ("Transaction \t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //VideoHistory
        $videos_history = VideoHistory::query()->limit($limit)->get();
        DB::flushQueryLog();
        VideoHistoriesResource::compute(
            $request,
            $videos_history
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($videos_history);
        echo ("VideoHistory \t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");


        //Video
        $videos = Video::query()->limit($limit)->get();
        DB::flushQueryLog();
        VideoResource::compute(
            $request,
            $videos
        )->get();
        $queries = DB::getQueryLog();
        $time = $this->toTime($queries);
        $queries_count = count($queries);
        $objects_count = count($videos);
        echo ("Video \t\t\t queries {$queries_count} \t time {$time} \t objects {$objects_count}\n");
    }
}
