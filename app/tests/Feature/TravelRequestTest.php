<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\TravelRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TravelRequestStatusUpdated;

class TravelRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $admin;

    public function setUp(): void{
        parent::setUp();

        $this->user = User::factory()->create();
        $this->admin = User::factory()->create([
            'is_admin' => true,
            'email' => 'admin@test.com'
        ]);
    }

    public function test_user_can_create_travel_request(){
        $this->actingAs($this->user, 'api');

        $payload = [
            'destino' => 'São Paulo',
            'data_ida' => '2025-08-01',
            'data_volta' => '2025-08-10',
        ];

        $response = $this->postJson('/api/requests', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('travel_requests', [
            'user_id' => $this->user->id,
            'destino' => 'São Paulo',
            'status' => 'solicitado',
        ]);
    }


    public function test_create_validation_fails_with_wrong_dates(){
        $this->actingAs($this->user, 'api');

        $payload = [
            'destino' => 'Rio de Janeiro',
            'data_ida' => '2025-08-10',
            'data_volta' => '2025-08-01',
        ];

        $response = $this->postJson('/api/requests', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['data_volta']);
    }


    public function test_user_cannot_update_status(){
        $this->actingAs($this->user, 'api');

        $travelRequest = TravelRequest::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'solicitado',
        ]);

        $response = $this->patchJson("/api/requests/{$travelRequest->id}", [
            'status' => 'aprovado'
        ]);

        $response->assertStatus(403);
    }


    public function test_admin_can_update_status_and_notify_user(){
        Notification::fake();

        $this->actingAs($this->admin, 'api');

        $travelRequest = TravelRequest::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'solicitado',
        ]);

        $response = $this->patchJson("/api/requests/{$travelRequest->id}", [
            'status' => 'aprovado'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('travel_requests', [
            'id' => $travelRequest->id,
            'status' => 'aprovado',
            'updated_by' => $this->admin->id,
        ]);

        Notification::assertSentTo(
            [$this->user],
            TravelRequestStatusUpdated::class
        );
    }


    public function test_cannot_cancel_after_approved(){
        $this->actingAs($this->admin, 'api');

        $travelRequest = TravelRequest::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'aprovado',
        ]);

        $response = $this->patchJson("/api/requests/{$travelRequest->id}", [
            'status' => 'cancelado'
        ]);
       
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('status');
    }


    public function test_show_travel_request(){
        $this->actingAs($this->user, 'api');

        $travelRequest = TravelRequest::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/requests/{$travelRequest->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                    'id' => $travelRequest->id,
                    'destino' => $travelRequest->destino,
                 ]);
    }


    public function test_user_cannot_view_others_requests(){
        $this->actingAs($this->user, 'api');

        $otherUser = User::factory()->create();
        $travelRequest = TravelRequest::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->getJson("/api/requests/{$travelRequest->id}");

        $response->assertStatus(403);
    }


    public function test_index_filters_work(){
        $this->actingAs($this->admin, 'api');

        TravelRequest::factory()->create([
            'user_id' => $this->user->id,
            'destino' => 'São Paulo',
            'status' => 'aprovado',
            'data_ida' => '2025-08-01',
            'data_volta' => '2025-08-10',
        ]);

        TravelRequest::factory()->create([
            'user_id' => $this->user->id,
            'destino' => 'Rio de Janeiro',
            'status' => 'solicitado',
            'data_ida' => '2025-09-01',
            'data_volta' => '2025-09-10',
        ]);

        $response = $this->getJson('/api/requests?destino=São Paulo');
        $response->assertStatus(200);
        $response->assertJsonFragment(['destino' => 'São Paulo']);
        $response->assertJsonMissing(['destino' => 'Rio de Janeiro']);

        $response = $this->getJson('/api/requests?status=solicitado');
        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'solicitado']);
        $response->assertJsonMissing(['status' => 'aprovado']);

        $response = $this->getJson('/api/requests?start_date=2025-09-01');
        $response->assertStatus(200);
        $response->assertJsonFragment(['destino' => 'Rio de Janeiro']);
        $response->assertJsonMissing(['destino' => 'São Paulo']);
    }


    public function test_unauthenticated_access(){
        $response = $this->getJson('/api/requests');
        $response->assertStatus(401);

        $response = $this->postJson('/api/requests', []);
        $response->assertStatus(401);

        $response = $this->patchJson('/api/requests/1', []);
        $response->assertStatus(401);
    }


    public function test_create_validation_fails_with_missing_fields(){
        $this->actingAs($this->user, 'api');

        $response = $this->postJson('/api/requests', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['destino', 'data_ida', 'data_volta']);
    }


    public function test_admin_cannot_update_with_invalid_status(){
        $this->actingAs($this->admin, 'api');

        $travelRequest = TravelRequest::factory()->create(['user_id' => $this->user->id]);

        $response = $this->patchJson("/api/requests/{$travelRequest->id}", [
            'status' => 'invalid_status'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('status');
    }
}
