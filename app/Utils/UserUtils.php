<?php


namespace App\Utils;


use App\Models\Country;
use App\Models\Path;
use App\Models\User;
use App\Models\UserBlock;
use App\Models\UserPath;
use Illuminate\Support\Facades\Cache;

class UserUtils
{
    public static function handleUpdatePathAndSubpath($path_id, $subpath_id, $subpath, $user_id)
    {
        $subpath_id = self::getSubpathId($path_id, $user_id, $subpath_id, $subpath);

        if(isset($subpath_id)){

            $user_subpath = UserPath::where('user_id', $user_id)->where('is_subpath', true)->first();
            if(!$user_subpath){
                UserPath::create(['user_id' => $user_id, 'subpath_id' => $subpath_id, 'is_subpath' => true]);
                return;
            }

            $user_subpath->update(['path_id' => $subpath_id]);
        }
    }

    private static function getSubpathId($path_id, $user_id, $subpath_id = null, $subpath = null)
    {
        if(isset($subpath_id)){
            return $subpath_id;
        }

        if(isset($subpath)){

            $name = ucfirst(strtolower(trim($subpath)));
            $key_name = str_replace(' ', '', strtolower(trim($subpath)));

            $subpath_exists = Path::query()->where('key_name', $key_name)->where('is_subpath', true)->first();
            if(isset($subpath_exists)){
                return $subpath_exists->id;
            }

            $path = Path::create([
                'name' => $name, 'key_name' => $key_name,
                'created_by' => $user_id, 'parent_id' => $path_id, 'is_subpath' => true
            ]);

            Cache::tags('paths')->flush();

            return $path->id;
        }

        return null;
    }

    public static function handleUpdateRegion($country_id, $region_id, $region)
    {
        $country = Country::query()->find($country_id);

        $region_response = [
            'id' => null,
            'name' => null
        ];

        if(!$region_id && !$region){
            return $region_response;
        }

        if($country->has_childs){
            $region_response['id'] = $region_id;
            return $region_response;
        }

        $region_response['name'] = $region;

        return $region_response;
    }

    public static function getUsernameSuggestion($invalid_username)
    {
        $invalid_username = (string)preg_replace('/[^A-Za-z0-9_.]/', '', $invalid_username);
        $username_suggestion = $invalid_username;
        $counter = 0;

        while (User::where('username', $username_suggestion)->exists()){
            $counter++;
            $username_suggestion = $invalid_username . $counter;
        }

        return $username_suggestion;
    }

    public static function genUnicUnknowUsername()
    {
        $new_username = "unknow";
        $counter = 0;

        while (User::where('username', $new_username)->exists()){
            $counter++;
            $new_username = "unknow" . $counter;
        }

        return $new_username;
    }

    public static function genUnicUnknowEmail()
    {
        $new_email_name = "unknow";
        $counter = 0;

        while (User::where('email', $new_email_name . '@unknow.unknow')->exists()){
            $counter++;
            $new_email_name = "unknow" . $counter;
        }

        return $new_email_name . '@unknow.unknow';
    }

    public static function getUsersBlockIds(int $user_id): array
    {
        $blocked = UserBlock::query()
            ->where('from_user_id', $user_id)
            ->pluck('to_user_id')
            ->toArray();

        $blocked_me = UserBlock::query()
            ->where('to_user_id', $user_id)
            ->pluck('from_user_id')
            ->toArray();

        return array_merge(
            $blocked,
            $blocked_me
        );
    }
}
