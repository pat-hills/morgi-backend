<?php

namespace App\Utils\User\Signup;

use App\Enums\UserEnum;
use App\Models\User;
use App\Rules\AgeValidation;
use App\Rules\CityIdValidation;
use App\Rules\EmailValidation;
use App\Rules\PathValidation;
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

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    private function getUserRules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'unique:users,email', new EmailValidation()],
            'type' => ['required', Rule::in(UserEnum::TYPE_LEADER, UserEnum::TYPE_ROOKIE)],
            'referral_code' => ['sometimes', 'string'],
            'gender_id' => ['sometimes', 'exists:genders,id'],
            'description' => ['sometimes', 'string', 'min:3', 'max:120'],
            'public_group' => ['sometimes', 'exists:users_ab_groups,id'],
            'password' => ['required', 'string', 'min:6', 'max:32'],
            'favorite_activities_ids' => ['required','array','max:5'],
            'chat_topics_ids' => ['required','array','max:5']
        ];
    }

    private function getRookieRules(): array
    {
        return $this->getUserRules() + [
                'video_path_location' => ['sometimes', 'unique:videos,path_location'],
                'age_confirmation' => ['boolean', new AgeValidation],
                'path_id' => ['exists:paths,id', new PathValidation()],
                'subpath' => ['sometimes', 'string', new SubpathValidation()],
                'subpath_id' => ['sometimes', 'exists:paths,id', new SubpathIdValidation($this->request->path_id)],
                'first_name' => ['string'],
                'last_name' => ['string'],
                'birth_date' => ['date', 'before_or_equal:' . Carbon::now()->subYears(18)->addDay()->format('Y-m-d')],
                'country_id' => ['integer'],
                'region_id' => ['sometimes', 'integer', new RegionIdValidation($this->request->country_id)],
                'region' => ['sometimes', 'string'],
                'city_id' => ['sometimes', 'integer', 'exists:cities,id', new CityIdValidation($this->request->country_id)],
                'city' => ['sometimes', 'string'],
                'zip_code' => ['sometimes', 'string'],
                'street' => ['sometimes', 'string'],
                'apartment_number' => ['sometimes', 'string'],
                'phone_number' => ['sometimes', 'string'],
                'path_location' => ['required', new PhotoValidation()]
            ];
    }

    private function getLeaderRules(): array
    {
        return $this->getUserRules() + [
                'interested_in_gender_id' => ['sometimes', 'exists:genders,id'],
                'first_rookie' => ['sometimes', 'string', 'exists:users,username']
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
            'path_location.required' => "You must upload a profile picture",
            'email.unique' => "Email already exists. Please log in or enter a different email",
            'birth_date.before_or_equal' => "You must be 18 years or older to open an account"
        ];
    }
}
