<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get my notifications
     */
    public function index()
    {
        $user = auth()->user();

        $notifications = $user->notifications()
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->data['title'] ?? '',
                    'body' => $n->data['body'] ?? '',
                    'type' => $n->data['type'] ?? 'general',
                    'is_read' => $n->read_at !== null,
                    'created_at' => $n->created_at->format('Y-m-d H:i:s'),
                    'time_ago' => $n->created_at->diffForHumans(),
                ];
            });

        return response()->api('Notifications retrieved', $notifications);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(string $id)
    {
        $user = auth()->user();

        $notification = $user->notifications()->find($id);

        if (!$notification) {
            return response()->api('Notification not found', null, 404);
        }

        $notification->markAsRead();

        return response()->api('Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = auth()->user();
        $user->unreadNotifications->markAsRead();

        return response()->api('All notifications marked as read');
    }

    /**
     * Unread count
     */
    public function unreadCount()
    {
        $user = auth()->user();

        return response()->api('Unread count', [
            'count' => $user->unreadNotifications()->count(),
        ]);
    }
}
