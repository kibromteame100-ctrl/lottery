<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->userNotifications()
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $notifications->map(fn($n) => [
                'id'         => $n->id,
                'type'       => $n->type,
                'title'      => $n->title,
                'message'    => $n->message,
                'data'       => $n->data,
                'read_at'    => $n->read_at?->toIso8601String(),
                'created_at' => $n->created_at->toIso8601String(),
            ]),
            'unread_count' => $request->user()->userNotifications()->unread()->count(),
        ]);
    }

    public function markRead(Request $request, int $id): JsonResponse
    {
        $notification = $request->user()->userNotifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true, 'message' => __('notifications.marked_read')]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->userNotifications()->unread()->update(['read_at' => now()]);

        return response()->json(['success' => true, 'message' => __('notifications.all_marked_read')]);
    }
}
