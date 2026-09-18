<?php

namespace App\Modules\Notifications\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notifications\Services\NotificationService;
use App\Modules\Notifications\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(): View
    {
        $notifications = Notification::with(['customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications::index', compact('notifications'));
    }

    public function show(Notification $notification): View
    {
        $notification->load(['customer', 'notifiable']);
        return view('notifications::show', compact('notification'));
    }

    public function markAsRead(Notification $notification)
    {
        try {
            $this->notificationService->markAsRead($notification->id);
            return back()->with('success', 'Notification marked as read');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to mark as read: ' . $e->getMessage());
        }
    }

    public function markAllAsRead(Request $request)
    {
        $customerId = $request->input('customer_id');
        
        try {
            $count = $this->notificationService->markAllAsRead($customerId);
            return back()->with('success', "{$count} notifications marked as read");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to mark all as read: ' . $e->getMessage());
        }
    }

    public function retryFailed()
    {
        try {
            $count = $this->notificationService->retryFailedNotifications();
            return back()->with('success', "{$count} notifications retried successfully");
        } catch (\Exception $e) {
            return back()->with('error', 'Retry failed: ' . $e->getMessage());
        }
    }

    public function processScheduled()
    {
        try {
            $count = $this->notificationService->processScheduledNotifications();
            return back()->with('success', "{$count} scheduled notifications processed");
        } catch (\Exception $e) {
            return back()->with('error', 'Processing failed: ' . $e->getMessage());
        }
    }

    // API Methods
    public function apiIndex(Request $request): JsonResponse
    {
        $query = Notification::with(['customer']);

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('type')) {
            $query->where('notification_type', $request->type);
        }

        if ($request->has('channel')) {
            $query->where('channel', $request->channel);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('unread_only') && $request->unread_only) {
            $query->unread();
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return response()->json($notifications);
    }

    public function apiShow(Notification $notification): JsonResponse
    {
        $notification->load(['customer', 'notifiable']);
        return response()->json($notification);
    }

    public function apiByCustomer(Request $request, int $customerId): JsonResponse
    {
        $filters = $request->only(['type', 'channel', 'status', 'unread_only']);
        $notifications = $this->notificationService->getCustomerNotifications($customerId, $filters);
        
        return response()->json($notifications);
    }

    public function apiMarkAsRead(Notification $notification): JsonResponse
    {
        try {
            $this->notificationService->markAsRead($notification->id);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function apiMarkAllAsRead(Request $request): JsonResponse
    {
        $customerId = $request->input('customer_id');
        
        try {
            $count = $this->notificationService->markAllAsRead($customerId);
            return response()->json(['success' => true, 'count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function apiCreate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'notification_type' => 'required|string',
            'channel' => 'required|string',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string',
            'data' => 'nullable|array',
            'scheduled_at' => 'nullable|date',
            'priority' => 'nullable|integer|min:1|max:10',
            'metadata' => 'nullable|array',
        ]);

        try {
            $notification = $this->notificationService->createNotification($validated);
            return response()->json($notification, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function apiSend(Notification $notification): JsonResponse
    {
        try {
            $success = $this->notificationService->sendNotification($notification);
            return response()->json(['success' => $success]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
