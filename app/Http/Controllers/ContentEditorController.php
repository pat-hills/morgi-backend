<?php

namespace App\Http\Controllers;

use App\Models\ContentEditor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ContentEditorController extends Controller
{
    public function getInspirationContents(Request $request)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 15);

        $cache_reference = "inspiration_contents:page:{$page}:limit:{$limit}";
        $contents = Cache::tags('content_editor')->get($cache_reference);
        if (isset($contents)) {
            return response()->json($contents);
        }

        $contents = ContentEditor::query()
            ->where('type', 'inspiration')
            ->orderByDesc('created_at')
            ->paginate($request->query('limit', 15));

        Cache::tags('content_editor')->put($cache_reference, $contents, 86400);

        return response()->json($contents);
    }

    public function getNewsUpdateContents(Request $request)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 15);

        $cache_reference = "news_update_contents:page:{$page}:limit:{$limit}";
        $contents = Cache::tags('content_editor')->get($cache_reference);
        if (isset($contents)) {
            return response()->json($contents);
        }

        $contents = ContentEditor::query()
            ->where('type', 'news_update')
            ->orderByDesc('created_at')
            ->paginate($request->query('limit', 15));

        Cache::tags('content_editor')->put($cache_reference, $contents, 86400);

        return response()->json($contents);
    }
}
