<?php

namespace App\Utils\User\Update;

use App\Enums\CarouselTypeEnum;
use App\Rules\CityIdValidation;
use App\Rules\PhotoValidation;
use App\Rules\RegionIdValidation;
use App\Rules\SubpathIdValidation;
use App\Rules\SubPathValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class ValidationRulesUtils
{
    private $request;
    private $user;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->user = $this->request->user();
    }

    private function getUserRules(): array
    {
        return [
            'username' => ['sometimes', 'string', "unique:users,username,{$this->user->id}", 'max:16', 'min:3'],
            'description' => ['sometimes', 'string', 'min:3', 'max:120'],
            'path_location' => ['sometimes', new PhotoValidation()],
            'video_path_location' => ['sometimes', 'unique:videos,path_location'],
            'remove_avatar' => ['sometimes', 'boolean'],
            'remove_video' => ['sometimes', 'boolean'],
            'gender_id' => ['sometimes', 'integer', 'exists:genders,id'],
            'favorite_activities_ids' => ['required','array','max:5'],
            'chat_topics_ids' => ['required','array','max:5']
        ];
    }

    

    private function getRookieRules(): array
    {
        return $this->getUserRules() + [
                'video_path_location' => ['sometimes', 'unique:videos,path_location'],
                'subpath' => ['sometimes', 'string', new SubpathValidation()],
                'subpath_id' => ['sometimes', 'exists:paths,id', new SubpathIdValidation($this->request->path_id)],
                'first_name' => ['string'],
                'last_name' => ['string'],
                'birth_date' => ['date', 'before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d')],
                'country_id' => ['integer'],
                'region_id' => ['sometimes', 'integer', new RegionIdValidation($this->request->country_id)],
                'region' => ['sometimes', 'string'],
                'city_id' => ['sometimes', 'integer', 'exists:cities,id', new CityIdValidation($this->request->country_id)],
                'city' => ['sometimes', 'string'],
                'zip_code' => ['sometimes', 'string'],
                'street' => ['sometimes', 'string'],
                'apartment_number' => ['sometimes', 'string'],
                'phone_number' => ['sometimes', 'string'],
            ];
    }

    private function getLeaderRules(): array
    {
        return $this->getUserRules() + [
                'interested_in_gender_id' => ['sometimes', 'exists:genders,id'],
                'carousel_type' => ['sometimes', 'string', Rule::in(CarouselTypeEnum::TYPES_FILLABLE)]
            ];
    }

    public static function getRules(Request $request): array
    {
        $utils = new ValidationRulesUtils($request);

        return ($request->type==='rookie')
            ? $utils->getRookieRules()
            : $utils->getLeaderRules();
    }

    public static function getCustomErrorMessages(): array
    {
        return [
            'birth_date.before_or_equal' => "You must be 18 years or older to open an account"
        ];
    }
}

