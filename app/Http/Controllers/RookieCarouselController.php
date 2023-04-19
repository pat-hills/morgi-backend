<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Country;
use App\Models\Leader;
use App\Models\Rookie;
use App\Models\User;
use App\Orazio\Orazio;
use App\Utils\RookieCarouselUtils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RookieCarouselController extends Controller
{
    public function getRookies(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'path_id' => ['sometimes', 'integer'],
            'subpath_ids' => ['sometimes', 'array'],
            'subpath_ids.*' => ['sometimes', 'integer'],
            'rookies_ids' => ['sometimes', 'array'],
            'rookies_ids.*' => ['sometimes', 'integer']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $request_type = RookieCarouselUtils::computeGetRookiesRequestType($request->path_id, $request->subpath_ids);
        $leader = Leader::find($request->user()->id);
        $rookies_to_skip = $request->get('rookies_ids', []);

        if(isset($request->path_id)) {
            $leader->addFilteredPath($request->path_id);
        }

        /*
         * Base orazio rookies stored in RookieSeen cache table
         */
        if ($request_type === 'orazio'){
            $users = User::query()->selectRaw('users.*')
                ->join('rookies_seen', 'rookies_seen.rookie_id', '=', 'users.id')
                ->where('rookies_seen.leader_id', $leader->id)
                ->whereNotIn('rookies_seen.rookie_id', $rookies_to_skip)
                ->orderBy('rookies_seen.id')
                ->groupBy('users.id')
                ->paginate(
                    $request->get('limit', 50)
                );

            $response = UserResource::compute(
                $request,
                $users
            )->get();

            return response()->json($response);
        }

        /*
         * Filtering carousel for path or subpath
         */

        $rookies_to_take = null;
        if($request_type === 'path' && !$leader->hasPath($request->path_id)){
            $rookies_to_take = 30;
        }

        $orazio = new Orazio();
        $rookies_ids = ($request_type === 'path')
            ? $orazio->getRookiesByPathId($rookies_to_skip, $request->path_id, $rookies_to_take)
            : $orazio->getRookiesBySubpathsIds($rookies_to_skip, $request->subpath_ids, $rookies_to_take);

        $users = User::query()
            ->whereIn('id', $rookies_ids)
            ->paginate(
                $request->get('limit', 50)
            );

        $response = UserResource::compute(
            $request,
            $users
        )->get();

        return response()->json($response);
    }

    public function rookiesTodayBirthdays(Request $request)
    {
        /*
         * Only for testing env
         */
        if(env('APP_ENV')!=='prod' && Rookie::whereMonth('birth_date', '=', Carbon::now()->format('m'))
                ->whereDay('birth_date', '=', Carbon::now()->format('d'))->count()<=50){
            Rookie::query()->inRandomOrder()->limit(50)->update(['birth_date' => Carbon::now()->subYears(20)]);
        }

        $orazio = new Orazio();
        $rookies_ids = $orazio->getRookiesBirthday(50);

        $users = User::query()->whereIn('id', $rookies_ids)->paginate(
            $request->get('limit', 50)
        );

        $response = UserResource::compute(
            $request,
            $users
        )->get();

        return response()->json($response);
    }

    public function getRookiesSeen(Request $request)
    {
        $requesting_user = $request->user();

        $users = User::query()->select('users.*')
            ->join('rookies_seen_histories', 'users.id', '=', 'rookies_seen_histories.rookie_id')
            ->join('photos', 'photos.user_id', '=', 'rookies_seen_histories.rookie_id')
            ->where('rookies_seen_histories.leader_id', $requesting_user->id)
            ->where('users.active', true)
            ->groupBy('rookies_seen_histories.rookie_id')
            ->orderByDesc('rookies_seen_histories.created_at')
            ->paginate(
                $request->get('limit', 50)
            );

        $response = UserResource::compute(
            $request,
            $users
        )->get();

        return response()->json($response);
    }

    public function getRookiesByCountry(Request $request, Country $country)
    {
        $orazio = new Orazio();
        $rookies_ids = $orazio->getRookiesByCountryId(50, $country->id);

        $users = User::query()->whereIn('id', $rookies_ids)->paginate(
            $request->get('limit', 50)
        );

        $response = UserResource::compute(
            $request,
            $users
        )->get();

        return response()->json($response);
    }

    public function getPublicRookies(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'path_id' => ['sometimes', 'integer'],
            'has_persona' => ['sometimes', 'boolean']
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $orazio = new Orazio();

        $rookies_ids = $orazio->getFavouriteRookies(5);

        if(isset($request->path_id)){
            $rookies_ids = array_merge(
                $rookies_ids,
                $orazio->getBestRookiesForPublicByPathId(20, $rookies_ids, $request->path_id)
            );
        }else{
            $rookies_ids = array_merge(
                $rookies_ids,
                $orazio->getBestRookiesForPublic(20, $rookies_ids)
            );
        }

        if(count($rookies_ids)<3){
            return response()->json([['type' => 'leader_tour_card']]);
        }

        $response = [];

        $users = User::query()->whereIn('id', $rookies_ids)->get();
        $users_resources = UserResource::compute(
            $request,
            $users
        )->get()->shuffle();

        $rookie_counter = 0;

        foreach ($users_resources as $user_resource){

            if($rookie_counter%4 === 0 && count($response)>0){
                $response[] = ['type' => 'leader_tour_card'];
            }

            $response[] = $user_resource;
            $rookie_counter++;
        }

        return response()->json(
            collect($response)->take(20)
        );
    }
}
