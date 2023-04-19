<?php

use App\Models\UserIdentityDocument;
use App\Models\UserIdentityDocumentHistory;
use App\Models\Video;
use App\Models\VideoHistory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RestoreVideosHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(env('APP_ENV')==='prod'){

            DB::beginTransaction();
            try {

                $video_histories = VideoHistory::query()->where('status', 'approved')->get();
                foreach ($video_histories as $video_history){

                    $exists = Video::query()
                        ->where('user_id', $video_history->user_id)
                        ->where('path_location', $video_history->path_location)
                        ->exists();

                    if($exists){
                        continue;
                    }

                    Video::query()->where('user_id', $video_history->user_id)->delete();
                    Video::query()->create([
                        'user_id' => $video_history->user_id,
                        'path_location' => $video_history->path_location
                    ]);
                }

                $document_histories = UserIdentityDocumentHistory::query()->where('verified', 'approved')->get();
                foreach ($document_histories as $document_history){

                    $exists = UserIdentityDocument::query()
                        ->where('user_id', $document_history->user_id)
                        ->where('path_location', $document_history->path_location)
                        ->exists();

                    if($exists){
                        continue;
                    }

                    UserIdentityDocument::query()->where('user_id', $document_history->user_id)->delete();
                    UserIdentityDocument::query()->create([
                        'user_id' => $document_history->user_id,
                        'path_location' => $document_history->path_location
                    ]);
                }

                DB::commit();
            }catch (Exception $exception){
                DB::rollBack();
                throw new Exception("Error " . $exception->getMessage());
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

    }
}
