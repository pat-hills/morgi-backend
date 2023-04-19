<?php

namespace App\Enums;

class RookieSeenEnum
{
    const GENERIC_ROOKIES = 'generic_rookies';
    const MAIN_PATH_ROOKIES = 'main_path_rookies';
    const COMMON_PATHS_ROOKIES = 'common_paths_rookies';
    const FALLBACK_GENERIC_ROOKIES = 'fallback_generic_rookies';

    const GENERAL_SOURCES = [
        self::GENERIC_ROOKIES,
        self::MAIN_PATH_ROOKIES,
        self::COMMON_PATHS_ROOKIES,
        self::FALLBACK_GENERIC_ROOKIES
    ];

    const SIMILARITY = 'similarity';
    const PAID_ROOKIES_SIMILARITY = 'paid_rookies_similarity';
    const PATHS_CLUSTERS_ROOKIES = 'paths_clusters_rookies';
    const BEST_ROOKIES = 'best_rookies';

    const TESTING_SOURCES = [
        self::SIMILARITY,
        self::PAID_ROOKIES_SIMILARITY,
        self::PATHS_CLUSTERS_ROOKIES,
        self::BEST_ROOKIES
    ];
}
