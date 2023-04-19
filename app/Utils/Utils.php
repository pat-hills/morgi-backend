<?php


namespace App\Utils;

use DateInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Utils {

    const USERS_TABLES_BUTTON_STATUS_CLASS = [
        'pending' => 'btn btn-warning',
        'accepted' => 'btn btn-light',
        'rejected' => 'btn btn-danger',
        'untrusted' => 'btn btn-secondary',
        'blocked' => 'btn btn-danger',
        'new' => 'btn btn-success',
        'deleted' => 'btn btn-danger',
        'fraud' => 'btn btn-danger',
        'suspend' => 'btn btn-secondary',
        'under_review' => 'btn btn-secondary'
    ];

    static function truncateString($str, $chars, $to_space, $replacement = "...")
    {
        if($chars > strlen($str)){
            return $str;
        }

        $str = substr($str, 0, $chars);
        $space_pos = strrpos($str, " ");
        if($to_space && $space_pos >= 0) {
            $str = substr($str, 0, strrpos($str, " "));
        }

        return($str . $replacement);
    }

    public static function ipInfo($ip = null, $purpose = "country", $deep_detect = true)
    {
        $output = null;
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            $ip = $_SERVER["REMOTE_ADDR"];
            if ($deep_detect) {
                if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                }
                if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                }
            }
        }

        $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), null, strtolower(trim($purpose)));
        $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
        $continents = array(
            "AF" => "Africa",
            "AN" => "Antarctica",
            "AS" => "Asia",
            "EU" => "Europe",
            "OC" => "Australia (Oceania)",
            "NA" => "North America",
            "SA" => "South America"
        );

        if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
            $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
            if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
                switch ($purpose) {
                    case "location":
                        $output = array(
                            "city"           => @$ipdat->geoplugin_city,
                            "state"          => @$ipdat->geoplugin_regionName,
                            "country"        => @$ipdat->geoplugin_countryName,
                            "country_code"   => @$ipdat->geoplugin_countryCode,
                            "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                            "continent_code" => @$ipdat->geoplugin_continentCode
                        );
                        break;
                    case "address":
                        $address = array($ipdat->geoplugin_countryName);
                        if (@strlen($ipdat->geoplugin_regionName) >= 1) {
                            $address[] = $ipdat->geoplugin_regionName;
                        }
                        if (@strlen($ipdat->geoplugin_city) >= 1) {
                            $address[] = $ipdat->geoplugin_city;
                        }
                        $output = implode(", ", array_reverse($address));
                        break;
                    case "city":
                        $output = @$ipdat->geoplugin_city;
                        break;
                    case "state":
                    case "region":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "country":
                        $output = @$ipdat->geoplugin_countryName;
                        break;
                    case "countrycode":
                        $output = @$ipdat->geoplugin_countryCode;
                        break;
                }
            }
        }

        return $output;
    }

    public static function formatInterval(DateInterval $interval)
    {
        $result = "";

        if ($interval->y) { $result .= $interval->format("%y years "); }
        if ($interval->m) { $result .= $interval->format("%m months "); }
        if ($interval->d) { $result .= $interval->format("%d days "); }
        if ($interval->h) { $result .= $interval->format("%h hours "); }
        if ($interval->i) { $result .= $interval->format("%i minutes "); }

        return $result;
    }

    public static function getRealIp(Request $request): string
    {
        return $request->ip() ?? '127.0.0.1';
    }

    public static function getNumberNumerals(int $number): string
    {
        $ends = ['th','st','nd','rd','th','th','th','th','th','th'];
        $calc = $number % 100;

        if ($calc >= 11 && $calc <= 13){
            return 'th';
        }

        return $ends[$number % 10];
    }

    public static function validateCollection(collection $collection, $class_name): bool
    {
        return get_class($collection->first()) === $class_name;
    }

    public static function getPaymentColor(string $status, bool $hex = false) : string
    {
        if (in_array($status, ['new', 'completed', 'successful'])) {
            return ($hex) ? 'green' : 'success';
        }

        if ($status === 'pending') {
            return ($hex) ? 'orange' : 'warning';
        }

        if($status === 'declined') {
            return ($hex) ? 'red' : 'danger';
        }

        if($hex){
            return 'grey';
        }

        return 'secondary';
    }
}
