<?php

use App\Models\Video;
use App\Models\VideoHistory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixOldVideos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $videos = array_merge(
            Video::query()->where('is_processed', false)->pluck('path_location')->toArray(),
            VideoHistory::query()->where('is_processed', false)->pluck('path_location')->toArray()
        );

        foreach ($videos as $video){

            $path_exploded = explode("/", $video);

            if(count($path_exploded)!==3){
                continue;
            }

            $folder = $path_exploded[0] . '/' . $path_exploded[1];
            $filename = $path_exploded[2];

            try {
                \App\Utils\StorageUtils::createTranscoderJob($folder, $filename);
            }catch (Exception $exception){
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
