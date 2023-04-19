<?php


namespace App\Utils;

use App\Models\GlobalGroup;

class GlobalGroupUtils
{
    public static function createUniqueGlobalId($user_id)
    {
        $global = substr(abs(crc32(uniqid('', true))), 0, 8);
        $exist = GlobalGroup::where('global_id', $user_id . $global)->exists();

        while($exist == true){

            $global = substr(abs(crc32(uniqid('', true))), 0, 8);
            $exist = GlobalGroup::where('global_id', $user_id . $global)->exists();
        }

        return $user_id . $global;
    }
}
