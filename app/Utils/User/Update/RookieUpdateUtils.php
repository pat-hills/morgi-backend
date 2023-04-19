<?php

namespace App\Utils\User\Update;

use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use App\Models\Rookie;
use App\Models\UserPath;
use App\Models\ChatTopic;
use App\Models\FavoriteActivity;
use App\Utils\StorageUtils;
use App\Utils\UserUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class RookieUpdateUtils
{
    private $request;
    private $attributes_to_update = [];
    private $rookie;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->rookie = Rookie::find($this->request->user()->id);
    }

    public function update(): Rookie
    {
               
        try {
            $this->removeVideo();
            $this->setVideo();
            $this->updatePath();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        $this->updateFirstName();
        $this->updateLastName();
        $this->updateMisc();
        $this->updateRegionName();
        $this->updateRegionId();
        $this->updateCityId();
        $this->updateSubPath();

        if(count($this->attributes_to_update)>0){
            $this->rookie->update($this->attributes_to_update);
        }
        
        return $this->rookie;
        
    }

    private function removeVideo(): void
    {
        if($this->request->remove_avatar){
            $this->rookie->removeVideo();
        }
    }

    private function setVideo(): void
    {
        if($this->request->video_path_location){
            $this->rookie->removeVideo();

            $response = StorageUtils::assignObject($this->request->video_path_location, 'video', $this->rookie);
            if($response['status']==='error'){
                throw new \Exception($response['message']);
            }

            try {
                $this->rookie->addVideo($response['path_location']);
            }catch (\Exception $exception){
                throw new \Exception($exception->getMessage());
            }
        }
    }

    

    private function updateFirstName(): void
    {
        if(!isset($this->request->first_name) || $this->request->first_name===$this->rookie->first_name){
            return;
        }

        $this->attributes_to_update['first_name'] = ucfirst(strtolower($this->request->first_name));
    }

    private function updateLastName(): void
    {
        if(!isset($this->request->last_name) || $this->request->last_name===$this->rookie->last_name){
            return;
        }

        $this->attributes_to_update['last_name'] = ucfirst(strtolower($this->request->last_name));
    }

    private function updateMisc(): void
    {
        $this->attributes_to_update = array_merge(
            $this->attributes_to_update,
            $this->request->only('birth_date', 'country_id', 'zip_code', 'street', 'apartment_number', 'phone_number')
        );
    }

    private function updateRegionName(): void
    {
        if(!isset($this->request->region) || $this->request->region === $this->rookie->region){
            return;
        }

        $this->attributes_to_update['region_name'] = ucfirst(
            strtolower(
                trim($this->request->region)
            )
        );
    }

    private function updateRegionId(): void
    {
        if(!isset($this->request->region_id)){
            return;
        }

        $country = Country::query()->find($this->request->country_id);
        $region = Region::query()->find($this->request->region_id);
        if(isset($country) && $country->has_childs && $region->country_id === $country->id){
            $this->attributes_to_update['region_id'] = $region->id;
        }
    }

    private function updateCityId(): void
    {
        if(!isset($this->request->city_id) && !isset($this->request->city)){
            return;
        }

        if(isset($this->request->city_id)){

            $city = City::query()->where('id', $this->request->city_id)->where('country_id', $this->request->country_id)->first();
            if(isset($city)){
                $this->attributes_to_update['city_id'] = $city->id;
                return;
            }
        }

        if(isset($this->request->city)){

            $name = ucfirst(strtolower(trim($this->request->city)));
            $city = City::query()->where('name', $name)->where('country_id', $this->request->country_id)->first();
            if(isset($city)){
                $this->attributes_to_update['city_id'] = $city->id;
                return;
            }

            $city = City::create([
                'name' => $name,
                'created_by' => $this->rookie->id,
                'country_id' => $this->request->country_id
            ]);

            Cache::tags('cities')->flush();
            Cache::tags(['resources', 'users'])->forget('cities');

            $this->attributes_to_update['city_id'] = $city->id;
        }
    }

    private function updatePath(): void
    {
        $path = UserPath::where('user_id', $this->rookie->id)->where('is_subpath', false)->first();
        if(!isset($path) || !isset($this->request->path_id) || $path->path_id === $this->request->path_id){
            return;
        }

        if($this->rookie->path_changes_count >= 1){
            throw new \Exception("You can change the path only once");
        }

        $path->update(['path_id' => $this->request->path_id]);
        $this->rookie->increment('path_changes_count');
    }

    private function updateSubPath(): void
    {
        UserUtils::handleUpdatePathAndSubpath($this->request->path_id, $this->request->subpath_id, $this->request->subpath, $this->rookie->id);
    }
}
