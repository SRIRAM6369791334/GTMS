<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->user = User::where('email', 'admin@gtms.com')->first() ?? User::first();
    }

    public function test_guest_is_redirected_from_notifications_page(): void
    {
        $response = $this->get(route('notifications.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_notifications_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('notifications.index'));
        $response->assertStatus(200);
        $response->assertSee('Notifications Center');
        $response->assertSee('Total Notifications');
        $response->assertSee('Unread Alerts');
    }

    public function test_user_can_mark_notification_as_read_via_ajax(): void
    {
        $notif = $this->user->unreadNotifications()->first();
        if (!$notif) {
            app(\App\Http\Controllers\NotificationController::class)->seedDefaultNotifications($this->user, true);
            $notif = $this->user->unreadNotifications()->first();
        }

        $response = $this->actingAs($this->user)
            ->postJson(route('notifications.mark-as-read', $notif->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Notification marked as read.',
        ]);

        $this->assertNotNull($notif->fresh()->read_at);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('notifications.mark-all-read'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'unread_count' => 0,
        ]);

        $this->assertEquals(0, $this->user->unreadNotifications()->count());
    }

    public function test_user_can_filter_notifications_by_category(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('notifications.index', ['category' => 'lease']));

        $response->assertStatus(200);
        $response->assertSee('Lease Application');
    }

    public function test_user_can_delete_notification(): void
    {
        $notif = $this->user->notifications()->first();
        if (!$notif) {
            app(\App\Http\Controllers\NotificationController::class)->seedDefaultNotifications($this->user, true);
            $notif = $this->user->notifications()->first();
        }
        $notifId = $notif->id;

        $response = $this->actingAs($this->user)
            ->deleteJson(route('notifications.destroy', $notifId));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('notifications', [
            'id' => $notifId,
        ]);
    }

    public function test_header_view_contains_view_all_notifications_link(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('View All Notifications');
        $response->assertSee(route('notifications.index'));
        $response->assertDontSee('View All Applications');
    }
}
