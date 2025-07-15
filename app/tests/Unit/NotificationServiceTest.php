<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\TravelRequest;
use App\Notifications\TravelRequestStatusUpdated;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationService $service;
    protected User $user;

    protected function setUp(): void{
        parent::setUp();
        $this->service = new NotificationService();
        $this->user = User::factory()->create();
    }


    public function test_get_all_notifications(){
        $travelRequest = TravelRequest::factory()->create(['user_id' => $this->user->id]);

        $this->user->notify(new TravelRequestStatusUpdated($travelRequest));

        $notifications = $this->service->getAll($this->user);

        $this->assertCount(1, $notifications);
    }


    public function test_mark_all_as_read(){
        $travelRequest = TravelRequest::factory()->create(['user_id' => $this->user->id]);
        $this->user->notify(new TravelRequestStatusUpdated($travelRequest));

        $this->assertCount(1, $this->user->unreadNotifications);

        $this->service->markAllAsRead($this->user);

        $this->assertCount(0, $this->user->fresh()->unreadNotifications);
    }


    public function test_mark_single_notification_as_read(){
        $travelRequest = TravelRequest::factory()->create(['user_id' => $this->user->id]);
        $this->user->notify(new TravelRequestStatusUpdated($travelRequest));

        $notification = $this->user->unreadNotifications->first();

        $this->service->markAsRead($this->user, $notification->id);

        $this->assertNotNull($notification->fresh()->read_at);
    }
}
