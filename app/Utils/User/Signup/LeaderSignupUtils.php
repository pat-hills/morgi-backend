<?php

namespace App\Utils\User\Signup;

use App\Enums\CarouselTypeEnum;
use App\Logger\Logger;
use App\Models\CarouselSetting;
use App\Models\Leader;
use App\Models\Path;
use App\Models\RookieSeen;
use App\Models\User;
use App\Orazio\OrazioHandler;
use Illuminate\Http\Request;

class LeaderSignupUtils
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function validate(): void
    {
    }

    public function create(User $user): Leader
    {
        try {
            $leader = Leader::create([
                'id' => $user->id,
                'user_id' => $user->id,
                'interested_in_gender_id' => $this->request->interested_in_gender_id,
                'first_rookie' => $this->getFirstRookieSeenId(),
                'carousel_type' => CarouselSetting::getActiveType()
            ]);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        /*
         * If leader has first_rookie set this rookie as first in carousel
         */
        if(isset($leader->first_rookie)){
            RookieSeen::create([
                'source' => 'first_rookie',
                'leader_id' => $leader->id,
                'rookie_id' => $leader->first_rookie
            ]);
        }

        /*
         * Set leader main path if isset path_id in request
         */
        $this->setMainPath($leader);

        /*
         * Run orazio for the first time
         */
        try {
            OrazioHandler::freshSeen($leader->id, 'First session');
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return $leader;
    }

    private function setMainPath(Leader $leader): void
    {
        if(!isset($this->request->path_id)){
            return;
        }

        $path = Path::query()->where('id', $this->request->path_id)->where('is_subpath', false)->first();
        if(isset($path)){
            $leader->setMainPath($path->id, 'signup');
        }
    }

    private function getFirstRookieSeenId(): ?int
    {
        if(!isset($this->request->first_rookie)){
            return null;
        }

        $first_rookie_seen = User::query()->where('type', 'rookie')
            ->where('active', true)
            ->where('username', $this->request->first_rookie)
            ->first();
        if(!isset($first_rookie_seen)){
            return null;
        }

        return $first_rookie_seen->id;
    }
}
