<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Display the Notification Center view with filtering, metrics, and pagination.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // If user has zero notifications, auto-seed realistic GTMS notifications
        if ($user && $user->notifications()->count() === 0) {
            $this->seedDefaultNotifications($user);
        }

        $filter = $request->get('filter', 'all'); // all, unread, read
        $category = $request->get('category', 'all'); // all, lease, mining, survey, drone, environment, accounts, system
        $search = trim($request->get('search', ''));

        // Query user's notifications using our Notification model
        $query = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id);

        // Status filter
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        // Category filter
        if ($category !== 'all' && !empty($category)) {
            $query->where(function ($q) use ($category) {
                $q->where('data->category', $category)
                  ->orWhere('data->type', $category);
            });
        }

        // Search keyword
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('data->title', 'LIKE', "%{$search}%")
                  ->orWhere('data->message', 'LIKE', "%{$search}%")
                  ->orWhere('data->description', 'LIKE', "%{$search}%")
                  ->orWhere('data->entity_id', 'LIKE', "%{$search}%");
            });
        }

        // Sorting: Unread first, then newest first
        $notifications = $query->orderByRaw('CASE WHEN read_at IS NULL THEN 0 ELSE 1 END')
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        // KPI Metrics and Summary Counts
        $allUserNotifs = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->get();

        $stats = [
            'total' => $allUserNotifs->count(),
            'unread' => $allUserNotifs->whereNull('read_at')->count(),
            'read' => $allUserNotifs->whereNotNull('read_at')->count(),
            'lease' => $allUserNotifs->filter(fn($n) => ($n->data['category'] ?? '') === 'lease')->count(),
            'mining' => $allUserNotifs->filter(fn($n) => ($n->data['category'] ?? '') === 'mining')->count(),
            'survey' => $allUserNotifs->filter(fn($n) => in_array($n->data['category'] ?? '', ['survey', 'dgps', 'drone']))->count(),
            'accounts' => $allUserNotifs->filter(fn($n) => ($n->data['category'] ?? '') === 'accounts')->count(),
            'environment' => $allUserNotifs->filter(fn($n) => in_array($n->data['category'] ?? '', ['environment', 'ec']))->count(),
            'action_required' => $allUserNotifs->whereNull('read_at')->filter(fn($n) => in_array($n->data['priority'] ?? '', ['high', 'urgent']))->count(),
        ];

        return view('pages.notifications.index', compact('notifications', 'stats', 'filter', 'category', 'search'));
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead($id, Request $request)
    {
        $user = auth()->user();
        $notification = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read.',
                'unread_count' => $user->unreadNotifications()->count(),
            ]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark a single notification as unread.
     */
    public function markAsUnread($id, Request $request)
    {
        $user = auth()->user();
        $notification = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $notification->update(['read_at' => null]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as unread.',
                'unread_count' => $user->unreadNotifications()->count(),
            ]);
        }

        return back()->with('success', 'Notification marked as unread.');
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = auth()->user();
        $user->unreadNotifications->markAsRead();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read.',
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a single notification.
     */
    public function destroy($id, Request $request)
    {
        $user = auth()->user();
        $notification = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $notification->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully.',
                'unread_count' => $user->unreadNotifications()->count(),
            ]);
        }

        return back()->with('success', 'Notification deleted successfully.');
    }

    /**
     * Clear all notifications or all read notifications.
     */
    public function clearAll(Request $request)
    {
        $user = auth()->user();
        $scope = $request->input('scope', 'all');

        if ($scope === 'read') {
            $user->readNotifications()->delete();
            $message = 'All read notifications have been cleared.';
        } else {
            $user->notifications()->delete();
            $message = 'All notifications have been cleared.';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'unread_count' => $user->unreadNotifications()->count(),
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Re-seed sample GTMS notifications for testing or initial view.
     */
    public function seedDemo(Request $request)
    {
        $user = auth()->user() ?? User::first();
        if ($user) {
            $this->seedDefaultNotifications($user, true);
        }

        return back()->with('success', 'Demo notifications reloaded successfully.');
    }

    /**
     * Seed initial realistic GTMS notifications for a user.
     */
    public function seedDefaultNotifications(?User $user = null, bool $force = false): void
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return;
        }

        if ($force) {
            $user->notifications()->delete();
        }

        $sampleNotifications = [
            [
                'id' => (string) Str::uuid(),
                'type' => 'App\Notifications\LeaseApplicationSubmitted',
                'data' => [
                    'title' => 'New Lease Application',
                    'message' => 'Application submitted for scrutiny review for Granite Quarry parcel LA-2026-0001.',
                    'category' => 'lease',
                    'action_url' => route('application.index'),
                    'entity_id' => 'LA-2026-0001',
                    'priority' => 'high',
                    'icon' => 'bi-file-earmark-text',
                ],
                'read_at' => null,
                'created_at' => Carbon::now()->subMinutes(10),
                'updated_at' => Carbon::now()->subMinutes(10),
            ],
            [
                'id' => (string) Str::uuid(),
                'type' => 'App\Notifications\MiningPlanUpdated',
                'data' => [
                    'title' => 'Mining Plan Update',
                    'message' => 'Draft document uploaded for verification under progressive mine closure scheme.',
                    'category' => 'mining',
                    'action_url' => route('miningplan.index'),
                    'entity_id' => 'MP-2026-0014',
                    'priority' => 'high',
                    'icon' => 'bi-geo-alt',
                ],
                'read_at' => null,
                'created_at' => Carbon::now()->subHour(),
                'updated_at' => Carbon::now()->subHour(),
            ],
            [
                'id' => (string) Str::uuid(),
                'type' => 'App\Notifications\SurveyCompleted',
                'data' => [
                    'title' => 'Survey Completed',
                    'message' => 'DGPS survey boundary coordinates approved and pillar demarcations verified.',
                    'category' => 'survey',
                    'action_url' => route('dgps-survey.index'),
                    'entity_id' => 'SRV-2026-088',
                    'priority' => 'medium',
                    'icon' => 'bi-check-circle',
                ],
                'read_at' => null,
                'created_at' => Carbon::yesterday()->setTime(14, 30),
                'updated_at' => Carbon::yesterday()->setTime(14, 30),
            ],
            [
                'id' => (string) Str::uuid(),
                'type' => 'App\Notifications\DroneSurveyReady',
                'data' => [
                    'title' => 'Drone Topography Orthomosaic Ready',
                    'message' => 'High-resolution aerial LiDAR point cloud and 3D terrain elevation contour map ready for download.',
                    'category' => 'drone',
                    'action_url' => route('drone-survey.index'),
                    'entity_id' => 'DRN-2026-0031',
                    'priority' => 'medium',
                    'icon' => 'bi-send',
                ],
                'read_at' => Carbon::now()->subHours(6),
                'created_at' => Carbon::now()->subDays(1)->setTime(11, 15),
                'updated_at' => Carbon::now()->subHours(6),
            ],
            [
                'id' => (string) Str::uuid(),
                'type' => 'App\Notifications\EcComplianceDue',
                'data' => [
                    'title' => 'EC Compliance Half-Yearly Report Due',
                    'message' => 'Six-monthly environmental compliance return for Q2 FY26 requires statutory officer sign-off.',
                    'category' => 'environment',
                    'action_url' => route('ec-compliance.index'),
                    'entity_id' => 'EC-2026-0104',
                    'priority' => 'urgent',
                    'icon' => 'bi-leaf',
                ],
                'read_at' => Carbon::now()->subDays(1),
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'id' => (string) Str::uuid(),
                'type' => 'App\Notifications\PaymentReceived',
                'data' => [
                    'title' => 'Payment Receipt Generated',
                    'message' => 'Payment collection of ₹1,50,000 received for Quotation #QT-2026-0042 against Mining Plan consultancy.',
                    'category' => 'accounts',
                    'action_url' => route('accounts.receipts.index'),
                    'entity_id' => 'REC-2026-0077',
                    'priority' => 'medium',
                    'icon' => 'bi-credit-card-2-front',
                ],
                'read_at' => Carbon::now()->subDays(2),
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(2),
            ],
        ];

        foreach ($sampleNotifications as $notif) {
            $user->notifications()->create($notif);
        }
    }
}
