<?php

namespace App\Utils;
class RookieCarouselUtils
{
    public static function computeGetRookiesRequestType($path_id, $subpath_ids)
    {
        if(isset($path_id)) {
            return 'path';
        }

        if(isset($subpath_ids)) {
            return 'subpath';
        }

        return 'orazio';
    }
}
