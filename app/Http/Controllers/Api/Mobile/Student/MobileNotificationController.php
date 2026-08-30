<?php

namespace App\Http\Controllers\Api\Mobile\Student;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

/**
 * Serves the student's notification feed (stored in database_notifications table).
 */
class MobileNotificationController extends Controller
{
    use ApiResponse;

    // ── Index ─────────────────────────────────────────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->notifications()
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->paginated(
            $notifications,
            fn ($items) => collect($items)->map(fn ($n) => $this->format($n))->all()
        );
    }

    // ── Unread Count ──────────────────────────────────────────────────────────

    public function unreadCount(Request $request): JsonResponse
    {
        $count = $request->user()->unreadNotifications()->count();

        return $this->success(['unread_count' => $count]);
    }

    // ── Mark Single Read ──────────────────────────────────────────────────────

    public function markRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->find($id);

        if (! $notification) {
            return $this->notFound('Notification not found.');
        }

        $notification->markAsRead();

        return $this->success(null, 'Notification marked as read');
    }

    // ── Mark All Read ─────────────────────────────────────────────────────────

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return $this->success(null, 'All notifications marked as read');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function format(DatabaseNotification $n): array
    {
        return [
            'id' => $n->id,
            'type' => class_basename($n->type),
            'title' => $n->data['title'] ?? null,
            'body' => $n->data['body'] ?? $n->data['message'] ?? null,
            'data' => $n->data,
            'read_at' => $n->read_at?->toISOString(),
            'created_at' => $n->created_at->toISOString(),
        ];
    }
}
