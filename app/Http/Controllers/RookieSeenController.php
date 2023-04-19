<?php

namespace App\Http\Controllers;

use App\Logger\Logger;
use App\Models\Leader;
use App\Models\LeaderSawRookie;
use App\Models\Photo;
use App\Models\Rookie;
use App\Models\RookieSeen;
use App\Models\RookieSeenHistory;
use App\Orazio\OrazioHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class RookieSeenController extends Controller
{

    public function seenRookie(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'rookies_ids' => ['required', 'array'],
            'rookies_ids.*' => ['integer']
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 404);
        }

        $leader_user = $request->user();
        $rookies = Rookie::query()->whereIn( 'id', $request->rookies_ids)->get();

        LeaderSawRookie::storeOrUpdate($leader_user->id, $request->rookies_ids);

        Leader::query()
            ->where('id', $leader_user->id)
            ->whereIn('first_rookie', $request->rookies_ids)
            ->update(['seen_first_rookie' => true]);

        $rookies_seen = RookieSeen::query()
            ->where('leader_id', $leader_user->id)
            ->whereIn('rookie_id', $request->rookies_ids)
            ->get();

        $rookies_seen_histories = RookieSeenHistory::query()
            ->where('leader_id', $leader_user->id)
            ->whereIn('rookie_id', $request->rookies_ids)
            ->get();

        $public_avatars = Photo::query()
            ->whereIn('user_id', $request->rookies_ids)
            ->where('main', true)
            ->get();

        $rookies_seen_to_delete = [];

        foreach ($rookies as $rookie) {

            $rookie_seen = $rookies_seen->where('rookie_id', $rookie->id)->first();
            $rookie_seen_history = $rookies_seen_histories->where('rookie_id', $rookie->id)->first();

            $source = (isset($rookie_seen)) ? $rookie_seen->source : 'other';
            $leader_type = (isset($rookie_seen)) ? $rookie_seen->leader_type : 'other';

            /*
             * Rookies in rookies_seen are computed by orazio, if isset delete the row
             */
            if(isset($rookie_seen)) {
                $rookies_seen_to_delete[] = $rookie_seen->id;
            }

            /*
             * If leader already saw rookie, save the last seen
             */
            if(isset($rookie_seen_history)) {
                $rookie_seen_history->update([
                    'seen_at' => Carbon::now(),
                    'source' => $source,
                    'leader_type' => $leader_type
                ]);
                continue;
            }

            $public_avatar = $public_avatars->where('user_id', $rookie->id)->first();

            RookieSeenHistory::query()->create([
                'leader_id' => $leader_user->id,
                'rookie_id' => $rookie->id,
                'photo_id' => isset($public_avatar) ? $public_avatar->id : null,
                'seen_at' => now(),
                'source' => $source,
                'leader_type' => $leader_type,
                'session_id' => isset($rookie_seen) ? $rookie_seen->session_id : null
            ]);
        }

        RookieSeen::query()->whereIn('id', $rookies_seen_to_delete)->delete();

        /*
         * Refresh orazio if needed
         */
        $orazio_rookies_count = RookieSeen::query()
            ->where('leader_id', $leader_user->id)
            ->count();

        if($orazio_rookies_count <= 5) {
            try {
                OrazioHandler::freshSeen($leader_user->id, "Fast swipe leader, few rookies to see", true);
            }catch (\Exception $exception){
                Logger::logException($exception);
                throw new \Exception($exception->getMessage());
            }
        }

        return response()->json([]);
    }
}
