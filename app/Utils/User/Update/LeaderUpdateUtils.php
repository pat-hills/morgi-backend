<?php

namespace App\Utils\User\Update;

use App\Models\Leader;
use App\Orazio\OrazioHandler;
use Illuminate\Http\Request;

class LeaderUpdateUtils
{
    private $request;
    private $attributes_to_update = [];
    private $leader;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->leader = Leader::find($this->request->user()->id);
    }

    public function update(): Leader
    {
        $this->updateInterestedInGenderId();
        $this->updateCarouselType();

        if(count($this->attributes_to_update)>0){
            $this->leader->update($this->attributes_to_update);
        }

        if(array_key_exists('interested_in_gender_id', $this->attributes_to_update)){
            try {
                OrazioHandler::freshSeen($this->leader->id, 'Updated interested gender in', true);
            }catch (\Exception $exception){
            }
        }

        return $this->leader;
    }

    private function updateCarouselType(): void
    {
        if (isset($this->request->carousel_type)) {
            $this->attributes_to_update['carousel_type'] = $this->request->carousel_type;
        }
    }

    private function updateInterestedInGenderId(): void
    {
        if(!isset($this->request->interested_in_gender_id) || $this->request->interested_in_gender_id === $this->leader->interested_in_gender_id){
            return;
        }

        $this->attributes_to_update['interested_in_gender_id'] = $this->request->interested_in_gender_id;
    }
}
