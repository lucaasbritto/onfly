<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CustomNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_list_notifications(){
        CustomNotification::factory()->count(3)->create([
            'notifiable_id' => $this->user->id,
            'notifiable_type' => User::class,
        ]);

        $this->actingAs($this->user, 'api');

        $response = $this->getJson('/api/notifications');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }


    public function test_user_can_mark_all_notifications_as_read(){
        CustomNotification::factory()->count(2)->create([
            'notifiable_id' => $this->user->id,
            'notifiable_type' => User::class,
            'read_at' => null,
        ]);

        $this->actingAs($this->user, 'api');

        $response = $this->patchJson('/api/notifications/read');

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Todas as notificações foram marcadas como lidas']);

        $this->assertDatabaseMissing('notifications', [
            'notifiable_id' => $this->user->id,
            'read_at' => null,
        ]);
    }


    public function test_user_can_mark_single_notification_as_read(){
        $notification = CustomNotification::factory()->create([
            'notifiable_id' => $this->user->id,
            'notifiable_type' => User::class,
            'read_at' => null,
        ]);

        $this->actingAs($this->user, 'api');

        $response = $this->patchJson("/api/notifications/{$notification->id}/read");

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Notificação marcada como lida.']);

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'read_at' => now(),            
        ]);
    }


    public function test_unauthenticated_user_cannot_access_notifications(){
        $response = $this->getJson('/api/notifications');
        $response->assertStatus(401);
    }


    public function test_user_cannot_access_others_notifications(){
        $otherUser = User::factory()->create();
        $notification = CustomNotification::factory()->create([
            'notifiable_id' => $otherUser->id,
            'notifiable_type' => User::class,
            'read_at' => null,
        ]);

        $this->actingAs($this->user, 'api');

        $response = $this->patchJson("/api/notifications/{$notification->id}/read");

        $response->assertStatus(404);
    }
}
