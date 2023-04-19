<?php


namespace App\Orazio;


use App\Enums\SystemSettingEnum;
use App\Models\ConverterCarouselPosition;
use App\Models\OrazioSession;
use App\Models\Rookie;
use App\Models\RookieSeen;
use App\Models\SystemSetting;

class OrazioCompute
{
    /*
     * Config attributes
     */
    public $generic_rookies_to_take = 0;
    public $best_rookies_to_take = 0;
    public $leader_similarity_to_take = 0;
    public $paid_rookies_similarity_to_take = 0;
    public $paths_clusters_rookies_to_take = 0;
    public $main_path_rookies_to_take = 0;
    public $common_paths_rookies_to_take = 0;


    /*
     * Attributes
     */
    private $leader = null;
    private $orazio;
    private $rookies_ids = [];
    private $rookies_seen_to_insert = [];
    private $orazio_session = "";
    private $reason = null;

    public function __construct(Orazio $orazio)
    {
        $this->leader = $orazio->leader;
        $this->orazio = $orazio;

        $leader_type = $orazio->leader_type;

        /*
         * Lets setup configuration's attributes
         */
        switch ($leader_type){

            case "free_leader_no_path":

                $this->generic_rookies_to_take = 30;
                $this->best_rookies_to_take = 0;
                $this->leader_similarity_to_take = 10;
                $this->paid_rookies_similarity_to_take = 0;
                $this->paths_clusters_rookies_to_take = 10;
                $this->main_path_rookies_to_take = 0;
                $this->common_paths_rookies_to_take = 0;
                break;

            case "free_leader_with_main_path":
            case "free_leader_with_paths":

                $this->generic_rookies_to_take = 0;
                $this->best_rookies_to_take = 5;
                $this->leader_similarity_to_take = 5;
                $this->paid_rookies_similarity_to_take = 0;
                $this->paths_clusters_rookies_to_take = 10;
                $this->main_path_rookies_to_take = 15;
                $this->common_paths_rookies_to_take = 15;
                break;

            case "paid_leader":

                $this->generic_rookies_to_take = 0;
                $this->best_rookies_to_take = 5;
                $this->leader_similarity_to_take = 5;
                $this->paid_rookies_similarity_to_take = 5;
                $this->paths_clusters_rookies_to_take = 5;
                $this->main_path_rookies_to_take = 15;
                $this->common_paths_rookies_to_take = 15;
                break;

            case "paid_leader_did_not_paid_similarity":

                $this->generic_rookies_to_take = 0;
                $this->best_rookies_to_take = 10;
                $this->leader_similarity_to_take = 0;
                $this->paid_rookies_similarity_to_take = 5;
                $this->paths_clusters_rookies_to_take = 5;
                $this->main_path_rookies_to_take = 15;
                $this->common_paths_rookies_to_take = 15;
                break;

            default:

                $this->generic_rookies_to_take = 25;
                $this->best_rookies_to_take = 25;
                $this->leader_similarity_to_take = 0;
                $this->paid_rookies_similarity_to_take = 0;
                $this->paths_clusters_rookies_to_take = 0;
                $this->main_path_rookies_to_take = 0;
                $this->common_paths_rookies_to_take = 0;
                break;
        }
    }

    public function get(string $reason = null): array
    {
        $this->reason = $reason;

        if($this->generic_rookies_to_take>0){
            $generic_rookies = array_unique($this->orazio->getGenericRookies($this->generic_rookies_to_take, $this->rookies_ids));
            $generic_rookies_count = count($generic_rookies);
            $this->addToInsert('generic_rookies', $generic_rookies);
            $this->orazio_session .= "Generic rookies $generic_rookies_count/$this->generic_rookies_to_take" . PHP_EOL;

            if($this->generic_rookies_to_take > $generic_rookies_count){
                $rookies_to_take = $this->generic_rookies_to_take - $generic_rookies_count;
                $this->addFallbackRookies($rookies_to_take, 'generic_rookies', $this->rookies_ids);
            }
        }

        if($this->best_rookies_to_take>0){
            $best_rookies = array_unique($this->orazio->getBestRookies($this->best_rookies_to_take, $this->rookies_ids));
            $best_rookies_count = count($best_rookies);
            $this->addToInsert('best_rookies', $best_rookies);
            $this->orazio_session .= "Best rookies $best_rookies_count/$this->best_rookies_to_take" . PHP_EOL;

            if($this->best_rookies_to_take > $best_rookies_count){
                $rookies_to_take = $this->best_rookies_to_take - $best_rookies_count;
                $this->addFallbackRookies($rookies_to_take, 'best_rookies', $this->rookies_ids);
            }
        }

        if($this->leader_similarity_to_take>0){
            $similar_rookies = array_unique($this->orazio->getRookiesSimilarToLeader($this->leader_similarity_to_take, $this->rookies_ids));
            $similar_rookies_count = count($similar_rookies);
            $this->addToInsert('similarity', $similar_rookies);
            $this->orazio_session .= "Similarity $similar_rookies_count/$this->leader_similarity_to_take" . PHP_EOL;

            if($this->leader_similarity_to_take > $similar_rookies_count){
                $rookies_to_take = $this->leader_similarity_to_take - $similar_rookies_count;
                $this->addFallbackRookies($rookies_to_take, 'similarity', $this->rookies_ids);
            }
        }

        if($this->paid_rookies_similarity_to_take>0){
            $similar_rookies_paid = array_unique($this->orazio->getRookiesSimilarToPaidRookies($this->paid_rookies_similarity_to_take, $this->rookies_ids));
            $similar_rookies_paid_count = count($similar_rookies_paid);
            $this->addToInsert('paid_rookies_similarity', $similar_rookies_paid);
            $this->orazio_session .= "Paid rookies similarity $similar_rookies_paid_count/$this->paid_rookies_similarity_to_take" . PHP_EOL;

            if($this->paid_rookies_similarity_to_take > $similar_rookies_paid_count){
                $rookies_to_take = $this->paid_rookies_similarity_to_take - $similar_rookies_paid_count;
                $this->addFallbackRookies($rookies_to_take, 'paid_rookies_similarity', $this->rookies_ids);
            }
        }

        if($this->paths_clusters_rookies_to_take>0){
            $paths_cluster_rookies = array_unique($this->orazio->getRookiesByMainPath($this->paths_clusters_rookies_to_take, $this->rookies_ids));
            $paths_cluster_rookies_count = count($paths_cluster_rookies);
            $this->addToInsert('paths_clusters_rookies', $paths_cluster_rookies);
            $this->orazio_session .= "Paths clusters $paths_cluster_rookies_count/$this->paths_clusters_rookies_to_take" . PHP_EOL;

            if($this->paths_clusters_rookies_to_take > $paths_cluster_rookies_count){
                $rookies_to_take = $this->paths_clusters_rookies_to_take - $paths_cluster_rookies_count;
                $this->addFallbackRookies($rookies_to_take, 'paths_clusters_rookies', $this->rookies_ids);
            }
        }

        if($this->main_path_rookies_to_take>0){
            $main_path_rookies = array_unique($this->orazio->getRookiesByMainPath($this->main_path_rookies_to_take, $this->rookies_ids));
            $main_path_rookies_count = count($main_path_rookies);
            $this->addToInsert('main_path_rookies', $main_path_rookies);
            $this->orazio_session .= "Main path $main_path_rookies_count/$this->main_path_rookies_to_take" . PHP_EOL;

            if($this->main_path_rookies_to_take > $main_path_rookies_count){
                $rookies_to_take = $this->main_path_rookies_to_take - $main_path_rookies_count;
                $this->addFallbackRookies($rookies_to_take, 'main_path_rookies', $this->rookies_ids);
            }
        }

        if($this->common_paths_rookies_to_take>0){
            $common_paths_rookies = array_unique($this->orazio->getRookiesByCommonPaths($this->common_paths_rookies_to_take, $this->rookies_ids));
            $common_paths_rookies_count = count($common_paths_rookies);
            $this->addToInsert('common_paths_rookies', $common_paths_rookies);
            $this->orazio_session .= "Common paths $common_paths_rookies_count/$this->common_paths_rookies_to_take" . PHP_EOL;

            if($this->common_paths_rookies_to_take > $common_paths_rookies_count){
                $rookies_to_take = $this->common_paths_rookies_to_take - $common_paths_rookies_count;
                $this->addFallbackRookies($rookies_to_take, 'common_paths_rookies', $this->rookies_ids);
            }
        }

        if(count($this->rookies_seen_to_insert)<50){
            $this->addFallbackRookies(50 - count($this->rookies_seen_to_insert), 'final', $this->rookies_ids);
        }

        $this->insert();

        return RookieSeen::query()
            ->where('leader_id', $this->leader->id)
            ->pluck('rookie_id')
            ->toArray();
    }

    private function addFallbackRookies(int $rookies_to_take, string $type, array $rookie_to_skip = null): void
    {
        if($rookies_to_take<=0){
            return;
        }

        $fallback_rookies = array_unique($this->orazio->getFallbackGenericRookies($rookies_to_take, $rookie_to_skip));
        $fallback_rookies_count = count($fallback_rookies);
        $this->addToInsert("fallback_$type", $fallback_rookies);
        $this->orazio_session .= "Fallback $type $fallback_rookies_count" . PHP_EOL;
    }

    private function addToInsert(string $source, array $rookies_ids): void
    {
        $this->rookies_ids = array_unique(
            array_merge(
                $this->rookies_ids,
                $rookies_ids
            )
        );

        foreach ($rookies_ids as $rookie_id) {
            $this->rookies_seen_to_insert[$rookie_id] = [
                'leader_id' => $this->leader->id,
                'rookie_id' => $rookie_id,
                'source' => $source,
                'leader_type' => $this->orazio->leader_type,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        $this->rookies_seen_to_insert = array_values($this->rookies_seen_to_insert);
    }

    private function insertConverters(): void
    {
        if($this->leader->orazio_sessions_count >= 3 || $this->leader->has_converter_chat){
            return;
        }

        $converters_carousel_order = SystemSetting::query()->first()->converters_carousel_order;
        $converters_carousel_positions = ConverterCarouselPosition::query()->get();

        $converters = Rookie::query()->selectRaw('rookies.*')
            ->join('users', 'users.id', '=', 'rookies.id')
            ->where('users.active', true)
            ->where('rookies.is_converter', true);

        if($converters_carousel_order === SystemSettingEnum::CONVERTERS_CAROUSEL_ORDER_CUSTOM){
            $converters = $converters->whereIn('rookies.converter_carousel_position_id', $converters_carousel_positions->pluck('id'));
        }

        $converters = $converters->get()->keyBy('id');

        foreach ($converters_carousel_positions as $converters_carousel_position){

            $converter = ($converters_carousel_order === SystemSettingEnum::CONVERTERS_CAROUSEL_ORDER_CUSTOM)
                ? $converters->where('converter_carousel_position_id', $converters_carousel_position->id)->first()
                : $converters->random(1)->first();
            if(!isset($converter)){
                continue;
            }

            $this->rookies_seen_to_insert[$converters_carousel_position->position] = [
                'leader_id' => $this->leader->id,
                'rookie_id' => $converter->id,
                'source' => 'converters',
                'leader_type' => $this->orazio->leader_type,
                'created_at' => now(),
                'updated_at' => now()
            ];

            $converters->forget($converter->id);
        }

        $this->orazio_session .= "Converters {$converters->count()}" . PHP_EOL;
    }

    private function insert(): void
    {
        shuffle($this->rookies_seen_to_insert);
        $this->insertConverters();
        $this->orazio_session .= "Total rookies inserted " . count($this->rookies_seen_to_insert) . PHP_EOL;

        if(isset($this->leader)){
            $orazio_session = OrazioSession::query()->create([
                'leader_id' => $this->leader->id,
                'session' => $this->orazio_session,
                'reason' => $this->reason,
                'leader_type' => $this->orazio->leader_type
            ]);
        }

        foreach ($this->rookies_seen_to_insert as $key => $rookie_seen_to_insert){
            $this->rookies_seen_to_insert[$key]['session_id'] = $orazio_session->id;
        }

        RookieSeen::query()->insert($this->rookies_seen_to_insert);
    }
}
