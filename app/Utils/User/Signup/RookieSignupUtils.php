<?php

namespace App\Utils\User\Signup;

use App\Logger\Logger;
use App\Models\City;
use App\Models\Country;
use App\Models\Path;
use App\Models\Region;
use App\Models\Rookie;
use App\Models\RookieScore;
use App\Models\RookieStats;
use App\Models\User;
use App\Models\UserPath;
use App\Utils\StorageUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Exception;

class RookieSignupUtils
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function validate(): void
    {
        if(!StorageUtils::objectExists($this->request->path_location)){
            throw new \Exception('Invalid profile picture uploaded!');
        }

        if(isset($this->request->video_path_location) && !StorageUtils::objectExists($this->request->video_path_location)){
            throw new \Exception('Invalid video uploaded!');
        }
    }

    public function create(User $user): Rookie
    {
        $region_id = $this->getRegionId();
        $region_name = (isset($region_id)) ? null : $this->getRegionName();

        try {
            $rookie = Rookie::create([
                'age_confirmation' => true,
                'id' => $user->id,
                'user_id' => $user->id,
                'birth_date' => $this->request->birth_date,
                'country_id' => $this->request->country_id,
                'zip_code' => $this->request->zip_code,
                'street' => $this->request->street,
                'apartment_number' => $this->request->apartment_number,
                'phone_number' => $this->request->phone_number,
                'region_id' => $region_id,
                'region_name' => $region_name,
                'first_name' => ucfirst(strtolower($this->request->first_name)),
                'last_name' => ucfirst(strtolower($this->request->last_name)),
                'city_id' => $this->getCityId($user)
            ]);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        /*
         * Create rows also in rookies_stats and rookies_score
         */
        RookieStats::create(['rookie_id' => $rookie->id]);
        RookieScore::create(['rookie_id' => $rookie->id]);

        try {
            $user->setDescription($this->request->description);
        }catch (Exception $exception){
        }

        try {
            $this->setAvatar($user);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        try {
            $this->setVideo($user, $rookie);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        try {
            $this->setPath($user);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        try {
            $this->setSubPath($user);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }
        
        return $rookie;
    }

    private function getRegionId(): ?int
    {
        if(!isset($this->request->region_id)){
            return null;
        }

        $country = Country::query()->find($this->request->country_id);
        $region = Region::query()->find($this->request->region_id);
        if(isset($country) && $country->has_childs && $region->country_id === $country->id){
            return $region->id;
        }

        return null;
    }

    private function getRegionName(): ?string
    {
        if(!isset($this->request->region)){
            return null;
        }

        return ucfirst(strtolower(trim($this->request->region)));
    }

    private function getCityId(User $user): ?int
    {
        if(!isset($this->request->city_id) && !isset($this->request->city)){
            return null;
        }

        if(isset($this->request->city_id)){

            $city = City::query()->where('id', $this->request->city_id)->where('country_id', $this->request->country_id)->first();
            if(isset($city)){
                return $city->id;
            }
        }

        if(isset($this->request->city)){

            $name = ucfirst(strtolower(trim($this->request->city)));
            $city = City::query()->where('name', $name)->where('country_id', $this->request->country_id)->first();
            if(isset($city)){
                return $city->id;
            }

            $city = City::create([
                'name' => $name,
                'created_by' => $user->id,
                'country_id' => $this->request->country_id
            ]);

            Cache::tags('cities')->flush();
            Cache::tags(['resources', 'users'])->forget('cities');

            return $city->id;
        }

        return null;
    }

    private function setVideo(User $user, Rookie $rookie): void
    {
        if(!isset($this->request->video_path_location)){
            return;
        }

        $response = StorageUtils::assignObject($this->request->video_path_location, 'video', $user);
        if($response['status']==='error'){
            return;
        }

        $rookie->addVideo($response['path_location']);
    }

    private function setAvatar(User $user): void
    {
        $response = StorageUtils::assignObject($this->request->path_location, 'photo', $user);
        if(isset($response['path_location'])){

            try {
                $user->addPhoto($response['path_location'], true);
            }catch (\Exception $exception){
            }
        }
    }

    private function setPath(User $user): void
    {
        UserPath::create(['user_id' => $user->id, 'path_id' => $this->request->path_id]);
    }

    private function setSubPath(User $user): void
    {
        if(isset($this->request->subpath_id)){
            UserPath::create(['user_id' => $user->id, 'path_id' => $this->request->subpath_id, 'is_subpath' => true]);
            return;
        }

        if(isset($this->request->subpath)){

            $name = ucfirst(strtolower(trim($this->request->subpath)));
            $key_name = str_replace(' ', '', strtolower(trim($this->request->subpath)));

            $subpath = Path::query()->where('key_name', $key_name)->where('is_subpath', true)->first();
            if(isset($subpath)){
                UserPath::create(['user_id' => $user->id, 'path_id' => $subpath->id, 'is_subpath' => true]);
                return;
            }

            $subpath = Path::create([
                'name' => $name,
                'key_name' => $key_name,
                'created_by' => $user->id,
                'parent_id' => $this->request->path_id,
                'is_subpath' => true
            ]);

            Cache::tags('paths')->flush();

            UserPath::create(['user_id' => $user->id, 'path_id' => $subpath->id, 'is_subpath' => true]);
        }
    }
}
