<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller {
    /**
     * Display a listing of the notifications.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function index(): JsonResponse {
        try {
            $user          = Auth::user();
            $notifications = $user->notifications()->latest()->get();
            $unread        = $user->unreadNotifications()->latest()->get();

            return Helper::jsonResponse(true, 'Notifications fetched successfully.', 200, [
                'all'    => $notifications,
                'unread' => $unread,
            ]);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Mark a notification as read.
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function markAsRead($id): JsonResponse {
        try {
            $user         = Auth::user();
            $notification = $user->notifications()->where('id', $id)->firstOrFail();
            $notification->markAsRead();

            return Helper::jsonResponse(true, 'Notification marked as read.', 200);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete a notification.
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id): JsonResponse {
        try {
            $user         = Auth::user();
            $notification = $user->notifications()->where('id', $id)->firstOrFail();
            $notification->delete();

            return Helper::jsonResponse(true, 'Notification deleted successfully.', 200);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
