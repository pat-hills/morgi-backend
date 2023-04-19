<?php


namespace App\Orazio;


use App\Enums\RookieScoreEnum;
use App\Models\Gender;
use App\Models\LeaderFaceRecognitionMatch;
use App\Models\Rookie;
use App\Models\RookieFaceRecognitionMatch;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class OrazioQuery
{
    public $query;
    private $limit = null;
    private $orazio = null;

    public function __construct(Orazio $orazio = null)
    {

        $rookie_points_query = RookieScoreEnum::BEST_SCORE_SELECT_QUERY;
        $this->query = Rookie::query()->selectRaw("
                rookies.id as id, users.active, users.gender_id,
                $rookie_points_query as rookies_score,
                users_paths.path_id as path,
                COUNT(photos.id) as photos_count,
                COUNT(videos.id) as videos_count,
                COUNT(subscriptions.id) as subscriptions_count,
                rookies.likely_receive_score as likely_receive_score,
                rookies.intelligence_score as intelligence_score,
                rookies.beauty_score as beauty_score
            ")
            ->join('rookies_score', 'rookies_score.rookie_id', '=', 'rookies.id')
            ->leftJoin('users_paths', 'users_paths.user_id', '=', 'rookies.id')
            ->join('users', 'users.id', '=', 'rookies.id')
            ->join('photos', 'rookies.id', '=', 'photos.user_id')
            ->leftJoin('videos', 'rookies.id', '=', 'videos.user_id')
            ->leftJoin('subscriptions', 'rookies.id', '=', 'subscriptions.rookie_id')
            ->where('users.active', true)
            ->where('photos.main', true)
            ->where('rookies.is_converter', false)
            ->groupBy('rookies.id');

        if(isset($orazio)){

            $this->orazio = $orazio;
            if(isset($orazio->leader->interested_in_gender_id)){
                $this->filterByGender($orazio->leader->interested_in_gender_id);
            }

            $rookies_to_skip = $orazio->getRookiesToSkip();
            if(count($rookies_to_skip)>0){
                $this->skipRookies($rookies_to_skip);
            }
        }
    }

    public function skipRookies(array $rookies_ids): void
    {
        $this->query = $this->query->whereNotIn('rookies.id', $rookies_ids);
    }

    public function whereCountryId(int $country_id): void
    {
        $this->query = $this->query->where('rookies.country_id', $country_id);
    }

    public function whereBirthday(): void
    {
        $this->query = $this->query->whereMonth('rookies.birth_date', '=', Carbon::now()->format('m'))
            ->whereDay('rookies.birth_date', '=', Carbon::now()->format('d'));
    }

    public function filterByGender(int $gender_id, string $operator = '='): void
    {
        $all_gender = Gender::query()->where('key_name', 'all')->first();

        if(!isset($all_gender) || $all_gender->id!==$gender_id){
            $this->query = $this->query->where('users.gender_id', $operator, $gender_id);
        }
    }

    public function inRandomOrder(): void
    {
        $this->query = $this->query->inRandomOrder();
    }

    public function count(): int
    {
        return $this->query->get()->count();
    }

    public function isFavourite(): void
    {
        $this->query = $this->query->where('rookies.is_favourite', true);
    }

    public function whereInPaths(array $paths_ids): void
    {
        $this->query = $this->query
            ->whereIn('users_paths.path_id', $paths_ids)
            ->where('users_paths.is_subpath', false);
    }

    public function whereInSubpaths(array $subpaths_ids): void
    {
        $this->query = $this->query
            ->whereIn('users_paths.path_id', $subpaths_ids)
            ->where('users_paths.is_subpath', true);
    }

    public function orderByPaths(array $paths_ids, string $direction = 'DESC'): void
    {
        if (count($paths_ids)>0) {
            $ids = implode(',', $paths_ids);
            $this->query = $this->query->orderByRaw("FIELD(path, $ids) $direction");
        }
    }

    public function similarToLeader(): void
    {
        $rookies_ids = LeaderFaceRecognitionMatch::query()
            ->where('leader_id', $this->orazio->leader->id)
            ->pluck('rookie_id')
            ->toArray();

        $this->query = $this->query->whereIn('rookies.id', $rookies_ids);
    }

    public function similarToPaidRookies(): void
    {
        $paid_rookies_ids = Subscription::query()
            ->where('leader_id', $this->orazio->leader->id)
            ->pluck('rookie_id')
            ->toArray();

        $rookies_ids = RookieFaceRecognitionMatch::query()
            ->whereIn('rookie_id', $paid_rookies_ids)
            ->pluck('to_rookie_id')
            ->toArray();

        $this->query = $this->query->whereIn('rookies.id', $rookies_ids);
    }

    public function online(): void
    {
        $online_query = clone $this->query;
        if($online_query->whereNotNull('users.joined_telegram_bot_at')
                ->where('users.last_activity_at', '>=', Carbon::now()->subDays(30))
                ->get()
                ->count()>10){

            $this->query = $this->query->whereNotNull('users.joined_telegram_bot_at')
                ->where('users.last_activity_at', '>=', Carbon::now()->subDays(30));
            return;
        }

        $fallback_online_query = clone $this->query;
        if($fallback_online_query->where('users.last_activity_at', '>=', Carbon::now()->subDays(30))
                ->get()
                ->count()>10){

            $this->query = $this->query->where('users.last_activity_at', '>=', Carbon::now()->subDays(30));
        }

        //$this->query = $this->query->where('users.last_activity_at', '>=', Carbon::now()->subMonth());
    }

    public function orderBy(string $order_by, string $order_direction): void
    {
        $this->query = $this->query->orderBy($order_by, $order_direction);
    }

    public function orderByBestRookies(): void
    {
        $this->query = $this->query->orderBy('beauty_score', 'desc');
        $this->query = $this->query->orderBy('intelligence_score', 'desc');
        $this->query = $this->query->orderBy('likely_receive_score', 'desc');
        $this->query = $this->query->orderBy('is_favourite', 'desc');
        $this->query = $this->query->orderBy('untaxed_withdrawal_balance', 'desc');
        $this->query = $this->query->orderBy('rookies_score', 'desc');
        $this->query = $this->query->orderBy('subscriptions_count', 'desc');
        $this->query = $this->query->orderBy('videos_count', 'desc');
        $this->query = $this->query->orderBy('users.last_activity_at', 'desc');
    }

    public function take(int $rookies_to_take): void
    {
        $this->limit = $rookies_to_take;
    }

    public function get(): Collection
    {
        /*
         * Gender balancer
         */
        $all_gender = Gender::query()->where('key_name', 'all')->first();
        if(isset($all_gender, $this->orazio, $this->orazio->leader) && $all_gender->id === $this->orazio->leader->interested_in_gender_id){

            $male_gender_id = Gender::query()->where('key_name', 'male')->first()->id;
            $female_gender_id = Gender::query()->where('key_name', 'female')->first()->id;
            $other_gender_id = Gender::query()->where('key_name', 'other')->first()->id;

            $male_count = $this->getGendersCount($this->query, $male_gender_id);
            $female_count = $this->getGendersCount($this->query, $female_gender_id);
            $other_count = $this->getGendersCount($this->query, $other_gender_id);

            $total = $male_count + $female_count + $other_count;

            $male_percentage = $this->getGenderCountPercentage($total, $male_count);
            $female_percentage = $this->getGenderCountPercentage($total, $female_count);
            $other_percentage = $this->getGenderCountPercentage($total, $other_count);

            $male_rookies = $this->query->clone()
                ->where('users.gender_id', $male_gender_id)
                ->take($this->getGenderPercentageToTake($male_percentage))
                ->get();

            $female_rookies = $this->query->clone()
                ->where('users.gender_id', $female_gender_id)
                ->take($this->getGenderPercentageToTake($female_percentage))
                ->get();

            $other_rookies = $this->query->clone()
                ->where('users.gender_id', $other_gender_id)
                ->take($this->getGenderPercentageToTake($other_percentage))
                ->get();

            return $male_rookies->merge($female_rookies)->merge($other_rookies);
        }

        if(isset($this->limit)) {
            $this->query = $this->query->take($this->limit);
        }

        return $this->query->get();
    }

    public function getIds(): array
    {
        return $this->get()->pluck('id')->toArray();
    }

    private function getGendersCount(Builder $query, int $gender_id): int
    {
        return $query->clone()->where('users.gender_id', $gender_id)->get()->count();
    }

    private function getGenderCountPercentage(int $total, int $value): int
    {
        if($total===0){
            return 0;
        }

        return round(($value / $total) * 100);
    }

    private function getGenderPercentageToTake(int $percentage): int
    {
        return round(($this->limit / 100) * $percentage);
    }
}
