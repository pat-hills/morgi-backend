<?php


namespace App\Orazio;


use App\Models\Leader;
use App\Models\LeaderPath;
use App\Models\LeaderSawRookie;
use App\Models\OrazioSession;
use App\Models\PubnubChannel;
use App\Models\RookieSeen;
use App\Models\RookieSeenHistory;
use App\Models\User;
use App\Models\UserBlock;
use App\Models\UserPath;
use Illuminate\Support\Str;

class Orazio
{
    /*
     * Attributes
     */
    public $leader = null;
    public $leader_user = null;
    public $leader_type = null;
    public $unlocked_paths = null;
    public $leader_main_path_id = null;
    public $leader_common_paths_ids = null;

    public $log_uuid;

    public function __construct(int $leader_id = null)
    {
        $this->log_uuid = Str::orderedUuid();
        if(isset($leader_id)){

            $leader = Leader::find($leader_id);
            if(!isset($leader)){
                throw new \Exception("Leader not found");
            }

            $leader_user = User::find($leader_id);
            if(!isset($leader_user)){
                throw new \Exception("Leader user not found");
            }

            $this->leader = $leader;
            $this->leader_user = $leader_user;
            $this->unlocked_paths = $this->getUnlockedPaths();
            $this->leader_main_path_id = $this->getMainPathId();
            $this->leader_common_paths_ids = $this->getCommonPathsIds();
            $this->leader_type = $this->getLeaderType();
        }
    }

    private function getUnlockedPaths(): array
    {
        if(!isset($this->leader)){
            return [];
        }

        return UserPath::query()->select('id')
            ->where('user_id', $this->leader->id)
            ->pluck('id')
            ->toArray();
    }

    private function getLeaderType(): ?string
    {
        if(!isset($this->leader)){
            return null;
        }

        $total_subscriptions_count = $this->leader_user->total_subscriptions_count;
        $main_path_id = $this->leader_main_path_id;
        $common_paths_ids = $this->leader_common_paths_ids;

        /*
         * Leader never paid and without path
         */
        if($total_subscriptions_count===0 && !isset($main_path_id, $common_paths_ids)){
            return 'free_leader_no_path';
        }

        /*
         * Leader never paid with main path
         */
        if($total_subscriptions_count===0 && isset($main_path_id)){
            return 'free_leader_with_main_path';
        }

        /*
         * Leader never paid with main path and common paths
         */
        if($total_subscriptions_count===0 && isset($main_path_id, $common_paths_ids)){
            return 'free_leader_with_paths';
        }

        /*
         * Leader paid someone but seen 300 rookies look like him and did not pay anyone
         */
        $seen_similarity_count = RookieSeenHistory::query()
            ->where('leader_id', $this->leader->id)
            ->where('source', 'similarity')
            ->count();

        $paid_similarity = RookieSeenHistory::query()
                ->join('actions_tracking', 'actions_tracking.rookie_id', '=', 'rookies_seen_histories.rookie_id')
                ->where('rookies_seen_histories.leader_id', $this->leader->id)
                ->where('actions_tracking.leader_id', $this->leader->id)
                ->where('actions_tracking.paid_rookie', true)
                ->where('rookies_seen_histories.source', 'similarity')
                ->count() > 0;

        if($total_subscriptions_count>1 && ($seen_similarity_count>=300 && !$paid_similarity)){
            return 'paid_leader_did_not_paid_similarity';
        }

        /*
         * Leader paid someone
         */
        if($total_subscriptions_count>1){
            return 'paid_leader';
        }

        return 'free_leader_no_path';
    }

    private function getMainPathId(): ?int
    {
        if(!isset($this->leader)){
            return null;
        }

        $path = LeaderPath::query()->select('path_id')
            ->where('leader_id', $this->leader->id)
            ->where('is_main', true)
            ->first();

        if(!isset($path)){
            return null;
        }

        return $path->path_id;
    }

    private function getCommonPathsIds(): ?array
    {
        if(!isset($this->leader)){
            return null;
        }

        return LeaderPath::query()->select('path_id')
            ->where('leader_id', $this->leader->id)
            ->where('is_main', false)
            ->pluck('path_id')
            ->toArray();
    }

    public function getRookiesToSkip(): array
    {
        if(!isset($this->leader)){
            return [];
        }

        $leader_id = $this->leader->id;

        $rookies_seen_ids = RookieSeen::query()->select('rookie_id')
            ->where('leader_id', $leader_id)
            ->pluck('rookie_id')
            ->toArray();

        $latest_sessions_ids = OrazioSession::query()->where('leader_id', $leader_id)->select('id')
            ->orderByDesc('id')
            ->take(2)
            ->pluck('id')
            ->toArray();

        $rookies_seen_history_ids = RookieSeenHistory::query()->select('rookie_id')
            ->where('leader_id', $leader_id)
            ->whereNotIn('session_id', $latest_sessions_ids)
            ->pluck('rookie_id')
            ->toArray();

        $rookies_saw_three_times = LeaderSawRookie::query()->select('rookie_id')
            ->where('leader_id', $leader_id)
            ->where('count', '>=', 2)
            ->pluck('rookie_id')
            ->toArray();

        $rookies_with_opened_connection = PubnubChannel::query()->select('rookie_id')
            ->where('leader_id', $leader_id)
            ->pluck('rookie_id')
            ->toArray();

        $rookies_blocks_ids = UserBlock::query()
            ->select('from_user_id')
            ->whereNull('deleted_at')
            ->where('to_user_id', $leader_id)
            ->pluck('from_user_id')
            ->toArray();

        return array_unique(
            array_merge(
                $rookies_seen_history_ids,
                $rookies_seen_ids,
                $rookies_blocks_ids,
                $rookies_saw_three_times,
                $rookies_with_opened_connection
            )
        );
    }

    private function computeOrazioQuery(): OrazioQuery
    {
        if(!isset($this->leader)){
            return new OrazioQuery();
        }

        return new OrazioQuery($this);
    }

    /**
     *
     * Get best rookies from main path ordered by score,
     * active subscriptions count and that uploaded a video
     *
     * @param int $rookies_to_take
     * @return array
     */
    public function getBestRookies(int $rookies_to_take, array $rookie_to_skip = null): array
    {
        $query = $this->computeOrazioQuery();

        if(isset($this->leader_main_path_id)){
            $query->whereInPaths([$this->leader_main_path_id]);
        }

        $query->online();

        if(!empty($rookie_to_skip)){
            $query->skipRookies($rookie_to_skip);
        }

        $query->orderByBestRookies();
        $query->take($rookies_to_take);

        return $query->getIds();
    }

    /**
     *
     * Get generic rookies ordered by score
     *
     * @param int $rookies_to_take
     * @return array
     */
    public function getGenericRookies(int $rookies_to_take, array $rookie_to_skip = null): array
    {
        $query = $this->computeOrazioQuery();

        $query->online();

        if(!empty($rookie_to_skip)){
            $query->skipRookies($rookie_to_skip);
        }

        $query->orderBy('rookies_score', 'desc');
        $query->take($rookies_to_take);

        return $query->getIds();
    }

    /**
     *
     * Get fallback rookies ordered by score
     *
     * @param int $rookies_to_take
     * @return array
     */
    public function getFallbackGenericRookies(int $rookies_to_take, array $rookie_to_skip = null): array
    {
        $query = $this->computeOrazioQuery();

        $query->orderByBestRookies();
        $query->online();

        if(!empty($rookie_to_skip)){
            $query->skipRookies($rookie_to_skip);
        }

        $query->take($rookies_to_take);

        return $query->getIds();
    }

    /**
     *
     * Get rookies similar to leader
     *
     * @param int $rookies_to_take
     * @return array
     */
    public function getRookiesSimilarToLeader(int $rookies_to_take, array $rookie_to_skip = null): array
    {
        $query = $this->computeOrazioQuery();

        $query->similarToLeader();

        if(!empty($rookie_to_skip)){
            $query->skipRookies($rookie_to_skip);
        }

        $query->orderByBestRookies();
        $query->take($rookies_to_take);

        return $query->getIds();
    }

    /**
     *
     * Get rookies similar to rookies already paid by leader
     *
     * @param int $rookies_to_take
     * @return array
     */
    public function getRookiesSimilarToPaidRookies(int $rookies_to_take, array $rookie_to_skip = null): array
    {
        $query = $this->computeOrazioQuery();

        $query->similarToPaidRookies();

        if(!empty($rookie_to_skip)){
            $query->skipRookies($rookie_to_skip);
        }

        $query->orderByBestRookies();
        $query->take($rookies_to_take);

        return $query->getIds();
    }

    /**
     *
     * Get newest rookies ordered by score and subscriptions count
     *
     * @param int $rookies_to_take
     * @return array
     */
    public function getNewestRookies(int $rookies_to_take): array
    {
        $query = $this->computeOrazioQuery();

        $query->online();
        $query->orderBy('rookies_score', 'desc');
        $query->orderBy('subscriptions_count', 'asc');
        $query->take($rookies_to_take);

        return $query->getIds();
    }

    /**
     *
     * Get trusted rookies ordered by score and subscriptions count
     *
     * @param int $rookies_to_take
     * @return array
     */
    public function getTrustedRookies(int $rookies_to_take): array
    {
        $query = $this->computeOrazioQuery();

        $query->online();
        $query->orderBy('rookies_score', 'desc');
        $query->orderBy('subscriptions_count', 'desc');
        $query->take($rookies_to_take);

        return $query->getIds();
    }

    /**
     *
     * Get rookies from main path ordered by score
     *
     * @param int $rookies_to_take
     * @return array
     */
    public function getRookiesByMainPath(int $rookies_to_take, array $rookie_to_skip = null): array
    {
        $query = $this->computeOrazioQuery();

        if(isset($this->leader_main_path_id)){
            $query->whereInPaths([$this->leader_main_path_id]);
        }

        $query->online();

        if(!empty($rookie_to_skip)){
            $query->skipRookies($rookie_to_skip);
        }

        $query->orderBy('rookies_score', 'desc');
        $query->take($rookies_to_take);

        return $query->getIds();
    }

    /**
     *
     * Get rookies from common paths ordered by score
     *
     * @param int $rookies_to_take
     * @return array
     */
    public function getRookiesByCommonPaths(int $rookies_to_take, array $rookie_to_skip = null): array
    {
        $query = $this->computeOrazioQuery();

        if(isset($this->leader_common_paths_ids) && count($this->leader_common_paths_ids)>0){
            $query->whereInPaths($this->leader_common_paths_ids);
        }

        $query->online();

        if(!empty($rookie_to_skip)){
            $query->skipRookies($rookie_to_skip);
        }

        $query->orderBy('rookies_score', 'desc');
        $query->take($rookies_to_take);

        return $query->getIds();
    }

    /**
     *
     * Get favourite rookies (is_favourite===true) for public carousel
     *
     * @param int $rookies_to_take
     * @return array
     */
    public function getFavouriteRookies(int $rookies_to_take): array
    {
        $query = $this->computeOrazioQuery();
        $query->isFavourite();
        $query->inRandomOrder();
        $query->take($rookies_to_take);

        return $query->getIds();
    }

    /**
     *
     * Get best rookies for public side
     *
     * @param int $rookies_to_take
     * @param array $rookies_to_skip
     * @return array
     */
    public function getBestRookiesForPublic(int $rookies_to_take, array $rookies_to_skip): array
    {
        $query = $this->computeOrazioQuery();
        $query->skipRookies($rookies_to_skip);
        $query->online();
        $query->orderByBestRookies();
        $query->take($rookies_to_take);

        return $query->getIds();
    }

    /**
     *
     * Get the best rookies for public side by path id
     *
     * @param int $rookies_to_take
     * @param array $rookies_to_skip
     * @param int|null $path_id
     * @return array
     */
    public function getBestRookiesForPublicByPathId(int $rookies_to_take, array $rookies_to_skip, int $path_id): array
    {
        $query = $this->computeOrazioQuery();

        $query->skipRookies($rookies_to_skip);
        $query->whereInPaths([$path_id]);
        $query->online();
        $query->orderByBestRookies();
        $query->take($rookies_to_take);

        return $query->getIds();
    }

    public function getRookiesByPathId(array $rookies_to_skip, int $path_id, int $rookies_to_take = null): array
    {
        $query = $this->computeOrazioQuery();

        $query->skipRookies($rookies_to_skip);
        $query->whereInPaths([$path_id]);
        $query->orderByBestRookies();

        if(isset($rookies_to_take)){
            $query->take($rookies_to_take);
        }

        return $query->getIds();
    }

    public function getRookiesBySubpathsIds(array $rookies_to_skip, array $subpaths_ids, int $rookies_to_take = null): array
    {
        $query = $this->computeOrazioQuery();

        $query->skipRookies($rookies_to_skip);
        $query->whereInSubpaths($subpaths_ids);
        $query->orderByBestRookies();

        if(isset($rookies_to_take)){
            $query->take($rookies_to_take);
        }

        return $query->getIds();
    }

    public function getRookiesByCountryId(int $rookies_to_take, int $country_id): array
    {
        $query = $this->computeOrazioQuery();
        $query->whereCountryId($country_id);
        $query->online();
        $query->orderByBestRookies();
        $query->take($rookies_to_take);

        return $query->getIds();
    }

    public function getRookiesBirthday(int $rookies_to_take): array
    {
        $query = $this->computeOrazioQuery();
        $query->whereBirthday();
        $query->online();
        $query->orderByBestRookies();
        $query->take($rookies_to_take);

        return $query->getIds();
    }

    /**
     * @param string|null $reason
     * @return array
     */
    public function finalize(string $reason = null): array
    {
        $response = (new OrazioCompute($this))->get($reason);

        if(isset($this->leader)){
            $this->leader->increment('orazio_sessions_count');
        }

        return $response;
    }
}
