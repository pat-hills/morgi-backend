<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'last_read' => ['sometimes', 'date']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = $request->user();

        $notifications = Notification::query()
            ->where('user_id', $user->id)
            ->when($request->last_read, function ($query, $last_read){
                $query->whereDate('created_at', '>=', $last_read);
            })
            ->orderByDesc('created_at')
            ->paginate($request->query('limit', 15));

        $notifications = NotificationResource::compute($request, $notifications)->get();

        return response()->json($notifications);
    }

    public function lastRead(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications_count = Notification::query()
            ->where('user_id', $user->id)
            ->whereNull('seen_at')
            ->count();

        $notifications_last_read = Notification::query()
            ->select('seen_at')
            ->where('user_id', $user->id)
            ->whereNotNull('seen_at')
            ->latest('seen_at')
            ->first();

        return response()->json([
            'count' => $notifications_count,
            'last_read' => (isset($notifications_last_read))
                ? Carbon::create($notifications_last_read->seen_at)
                : null
        ]);
    }

    public function seen(Request $request)
    {
        $user = $request->user();

        Notification::query()->where('user_id', $user->id)
            ->whereNull('seen_at')
            ->update(['seen_at' => now()]);

        return response()->json(['last_read' => now()]);
    }
}
