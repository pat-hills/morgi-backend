<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use App\Models\Gender;
use App\Models\LeaderCcbillData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LeaderResource extends Resource
{
    public function small(): LeaderResource
    {
        $this->attributes = [
            'id',
        ];

        $this->addInterestedInGenderToResource();

        return $this;
    }

    public function regular(): LeaderResource
    {
        $this->small();
        return $this;
    }

    public function extended(): LeaderResource
    {
        $this->regular();
        return $this;
    }

    public function own(): LeaderResource
    {
        $this->attributes = [
            'id',
            'coupons',
            'has_converter_chat',
            'has_approved_transaction',
            'micro_morgi_balance',
            'total_coupons_got',
            'carousel_type'
        ];

        $this->addActiveCreditCardToResource();
        $this->addHasNewCreditCardToResource();
        $this->addIsFirstTransactionToResource();
        $this->addInterestedInGenderToResource();

        return $this;
    }

    //TODO ottimizzare
    private function addActiveCreditCardToResource()
    {
        foreach ($this->resources as $resource) {
            $cc = LeaderCcbillData::where('leader_id', $resource->id)
                ->where('active', true)
                ->latest()
                ->first();

            $resource->active_credit_card = (isset($cc))
                ? [
                    'cardType' => $cc->cardType,
                    'last4' => $cc->last4,
                    'expDate' => $cc->expDate
                ]
                : null;
        }

        $this->attributes[] = 'active_credit_card';
    }

    //TODO ottimizzare
    private function addHasNewCreditCardToResource()
    {
        foreach ($this->resources as $resource) {
            $resource->has_new_credit_card = $resource->hasNewCreditCard();
        }

        $this->attributes[] = 'has_new_credit_card';
    }

    //TODO ottimizzare
    private function addIsFirstTransactionToResource()
    {
        foreach ($this->resources as $resource) {
            $resource->is_first_transaction = !LeaderCcbillData::query()
                ->where('leader_id', $resource->id)
                ->whereNotNull('subscriptionId')
                ->exists();
        }

        $this->attributes[] = 'is_first_transaction';
    }

    private function addInterestedInGenderToResource()
    {
        $cache_reference = 'genders';
        $tags = ['resources', 'users'];

        $genders = Cache::tags($tags)->get($cache_reference);

        if (!isset($genders)){
            $genders = GenderResource::compute(
                $this->request,
                Gender::query()->get(),
                'small'
            )->get();

            Cache::tags($tags)->put($cache_reference, $genders, 86400);
        }

        foreach ($this->resources as $resource) {
            $resource->interested_in_gender = $genders->where('id', $resource->interested_in_gender_id)->first();
        }

        $this->attributes[] = 'interested_in_gender';
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
