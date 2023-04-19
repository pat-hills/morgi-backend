<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use App\Models\City;
use App\Models\Country;
use App\Models\Goal;
use App\Models\Path;
use App\Models\Photo;
use App\Models\PhotoHistory;
use App\Models\Region;
use App\Models\RookiesConverterRequest;
use App\Models\RookieWinnerHistory;
use App\Models\UserPath;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use function Symfony\Component\Translation\t;

class RookieResource extends Resource
{
    /*
     * Added this attribute to don't repeat "$this->resources->pluck('id')->toArray()". This attribute boosts performance
     */
    private $resources_ids;

    public function __construct(Request $request, $resources)
    {
        parent::__construct($request, $resources);
        $this->resources_ids = $this->resources->pluck('id')->toArray();
    }

    public function small(): RookieResource
    {
        $this->attributes = [
            'id',
            'first_name',
            'last_name',
            'birth_date',
            'is_converter',
        ];

        $this->addHasPastGoalsToResource();
        $this->addGoalsToResource();
        $this->addPhotosAndPhotosCountToResource();
        $this->addCountryToResource();
        $this->addPathAndSubpathToResource();

        return $this;
    }

    public function regular(): RookieResource
    {
        $this->small();
        return $this;
    }

    public function extended(): RookieResource
    {
        $this->regular();
        return $this;
    }

    public function own(): RookieResource
    {
        $this->attributes = [
            'id',
            'first_name',
            'last_name',
            'birth_date',
            'is_converter',
            'untaxed_morgi_balance',
            'untaxed_micro_morgi_balance',
            'untaxed_withdrawal_balance',
            'morgi_balance',
            'micro_morgi_balance',
            'withdrawal_balance',
            'path_changes_count',
            'first_micromorgi_gift_leaders',
        ];

        $this->addHasPastGoalsToResource();
        $this->addOwnGoalsToResource();
        $this->addOwnPhotosAndPhotosCountToResource();
        $this->addCountryToResource();
        $this->addRegionToResource();
        $this->addCityToResource();
        $this->addPathAndSubpathToResource();
        $this->addIsWinnerToResource();
        $this->addConverterRequestToResource();

        return $this;
    }

    //TODO: fare la resource
    private function addConverterRequestToResource()
    {
        foreach ($this->resources as $resource){

            $converter_request = RookiesConverterRequest::query()->selectRaw("id, message, created_at")
                ->where('rookie_id', $resource->id)
                ->first();

            $resource->converter_request = $converter_request;
        }

        $this->attributes[] = 'converter_request';
    }

    private function addOwnGoalsToResource()
    {
        $goals = Goal::query()
            ->whereIn('rookie_id', $this->resources_ids)
            ->orderByDesc('created_at')
            ->get();

        $goals_resources = GoalResource::compute(
            $this->request,
            $goals,
            'small'
        )->get();

        foreach ($this->resources as $resource){
            $resource->goals = $goals_resources->where('rookie_id', $resource->id)->values();
        }

        $this->attributes[] = 'goals';
    }

    private function addGoalsToResource()
    {
        $goals = Goal::query()
            ->whereIn('rookie_id', $this->resources_ids)
            ->orderByDesc('created_at')
            ->get();

        $goals_resources = GoalResource::compute(
            $this->request,
            $goals,
            'regular'
        )->get();

        foreach ($this->resources as $resource){
            $resource->goals = $goals_resources->where('rookie_id', $resource->id)->values();
        }

        $this->attributes[] = 'goals';
    }

    private function addIsWinnerToResource()
    {
        foreach ($this->resources as $resource){

            $is_winner = RookieWinnerHistory::query()
                ->select('amount')
                ->where('rookie_id', $resource->id)
                ->whereNull('seen_at')
                ->whereDate('win_at', '>=', Carbon::now()->subDay())
                ->first();

            $resource->is_winner = (isset($is_winner))
                ? ['amount' => $is_winner->amount]
                : null;
        }

        $this->attributes[] = 'is_winner';
    }

    private function addCountryToResource()
    {
        $cache_reference = 'countries';
        $tags = ['resources', 'users'];

        $countries = Cache::tags($tags)->get($cache_reference);

        if (!isset($countries)) {
            $countries = CountryResource::compute(
                $this->request,
                Country::query()->get(),
                'small'
            )->get();

            Cache::tags($tags)->put($cache_reference, $countries, 86400);
        }

        foreach ($this->resources as $resource){
            $resource->country = $countries->where('id', $resource->country_id)->first();
        }

        $this->attributes[] = 'country';
    }

    //TODO: inserire i region_name dentro la table region
    private function addRegionToResource()
    {
        $cache_reference = 'regions';
        $tags = ['resources', 'users'];

        $regions = Cache::tags($tags)->get($cache_reference);

        if (!isset($regions)) {
            $regions = RegionResource::compute(
                $this->request,
                Region::query()->get(),
                'small'
            )->get();

            Cache::put($cache_reference, $regions, 86400);
        }

        foreach ($this->resources as $resource){

            if($resource->region_id){
                $resource->region = $regions->where('id', $resource->region_id)->first();
                continue;
            }

            if($resource->region_name){
                $resource->region = [
                    'name' => $resource->region_name
                ];
                continue;
            }


            $resource->region = null;
        }

        $this->attributes[] = 'region';
    }

    private function addCityToResource()
    {
        $cache_reference = 'cities';
        $tags = ['resources', 'users'];

        $cities = Cache::tags($tags)->get($cache_reference);

        if (!isset($cities)) {
            $cities = CityResource::compute(
                $this->request,
                City::query()->get(),
                'small'
            )->get();

            Cache::tags($tags)->put($cache_reference, $cities, 86400);
        }

        foreach ($this->resources as $resource){
            $resource->city = $cities->where('id', $resource->city_id)->first();
        }

        $this->attributes[] = 'city';
    }

    private function addPathAndSubpathToResource()
    {
        $paths = Path::query()->selectRaw('paths.*')
            ->join('users_paths', 'users_paths.path_id', '=', 'paths.id')
            ->whereIn('users_paths.user_id', $this->resources_ids)
            ->get();

        $paths_resources = PathResource::compute(
            $this->request,
            $paths,
            'small'
        )->get();

        $rookies_paths = UserPath::query()->whereIn('user_id', $this->resources_ids)->get();

        foreach ($this->resources as $resource){

            $rookie_paths = $rookies_paths->where('user_id', $resource->id);
            $rookie_path = $rookie_paths->where('is_subpath', false)->first();
            $rookie_subpath = $rookie_paths->where('is_subpath', true)->first();

            $resource->path = (isset($rookie_path))
                ? $paths_resources->where('id', $rookie_path->path_id)->first()
                : null;

            $resource->subpath = (isset($rookie_subpath))
                ? $paths_resources->where('id', $rookie_subpath->path_id)->first()
                : null;
        }

        $this->attributes[] = 'path';
        $this->attributes[] = 'subpath';
    }

    private function addPhotosAndPhotosCountToResource()
    {
        $photos = PhotoResource::compute(
            $this->request,
            Photo::query()->whereIn('user_id', $this->resources_ids)->get(),
            'small'
        )->get();

        foreach ($this->resources as $resource){
            $resource->photos = $photos->where('user_id', $resource->id)->values();
            $resource->photos_count = $resource->photos->count();
        }

        $this->attributes[] = 'photos_count';
        $this->attributes[] = 'photos';
    }

    private function addOwnPhotosAndPhotosCountToResource()
    {
        $photos = PhotoResource::compute(
            $this->request,
            Photo::query()->whereIn('user_id', $this->resources_ids)->get(),
            'small'
        )->get();

        $photos_history = PhotoHistory::query()
            ->whereIn('user_id', $this->resources_ids)
            ->whereNotIn('status', ['approved', 'declined'])
            ->get();

        $photos_history_resources = PhotoHistoryResource::compute(
            $this->request,
            $photos_history,
            'small'
        )->get();

        foreach ($this->resources as $resource){
            $resource_photo = $photos->where('user_id', $resource->id);
            $resource_photo_history = $photos_history_resources->where('user_id', $resource->id);
            $resource->photos = $resource_photo->merge($resource_photo_history)->take(10);
            $resource->photos_count = $resource->photos->count();
        }

        $this->attributes[] = 'photos_count';
        $this->attributes[] = 'photos';
    }

    private function addHasPastGoalsToResource()
    {
        $goals = Goal::query()->selectRaw("id, rookie_id")
            ->where(function($query) {
                $query->whereIn('status', [Goal::STATUS_SUCCESSFUL, Goal::STATUS_AWAITING_PROOF, Goal::STATUS_PROOF_PENDING_APPROVAL])
                    ->orWhere('end_date', '<=', now());
            })
            ->whereIn('rookie_id', $this->resources_ids)
            ->get();

        foreach ($this->resources as $resource){
            $goal = $goals->where('rookie_id', $resource->id)->first();
            $resource->has_past_goals = isset($goal);
        }

        $this->attributes[] = 'has_past_goals';
    }

    public static function compute(Request $request, $resources, string $response_type = null): Resource
    {
        $class = static::class;
        $class_instance = new $class($request, $resources);

        if(isset($response_type) && $response_type === 'own'){
            $class_instance->own();
            return $class_instance;
        }

        return parent::compute($request, $resources, $response_type);
    }
}
