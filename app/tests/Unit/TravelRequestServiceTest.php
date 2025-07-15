<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\TravelRequest;
use App\Services\TravelRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TravelRequestServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;
    protected $user;
    protected $admin;

    protected function setUp(): void{
        parent::setUp();
        $this->service = new TravelRequestService();
        $this->user = User::factory()->create(['is_admin' => false]);
        $this->admin = User::factory()->create(['is_admin' => true]);
    }


    public function test_create_travel_request(){
        $data = [
            'destino' => 'Rio de Janeiro',
            'data_ida' => '2025-08-10',
            'data_volta' => '2025-08-15',
        ];

        $travelRequest = $this->service->createTravelRequest($this->user, $data);

        $this->assertDatabaseHas('travel_requests', [
            'id' => $travelRequest->id,
            'user_id' => $this->user->id,
            'destino' => 'Rio de Janeiro',
            'status' => 'solicitado',
        ]);
    }


    public function test_admin_can_update_status(){
        $travelRequest = TravelRequest::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'solicitado',
        ]);

        $updated = $this->service->updateStatus($travelRequest, 'aprovado', $this->admin);

        $this->assertEquals('aprovado', $updated->status);
        $this->assertEquals($this->admin->id, $updated->updated_by);
    }


    public function test_non_admin_cannot_update_status(){
        $this->expectException(AuthorizationException::class);

        $travelRequest = TravelRequest::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'solicitado',
        ]);

        $this->service->updateStatus($travelRequest, 'aprovado', $this->user);
    }


    public function test_cannot_cancel_approved_request(){
        $this->expectException(ValidationException::class);

        $travelRequest = TravelRequest::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'aprovado',
        ]);

        $this->service->updateStatus($travelRequest, 'cancelado', $this->admin);
    }


    public function test_get_filtered_requests_filters_correctly(){        
        TravelRequest::factory()->create(['status' => 'solicitado', 'destino' => 'Rio', 'user_id' => $this->user->id]);
        TravelRequest::factory()->create(['status' => 'aprovado', 'destino' => 'São Paulo', 'user_id' => $this->user->id]);
        TravelRequest::factory()->create(['status' => 'cancelado', 'destino' => 'Rio', 'user_id' => $this->admin->id]);
        
        $result = $this->service->getFilteredRequests($this->admin, ['status' => 'solicitado']);
        $this->assertCount(1, $result);
        
        $result = $this->service->getFilteredRequests($this->admin, ['destino' => 'Rio']);
        $this->assertCount(2, $result);
        
        $resultUser = $this->service->getFilteredRequests($this->user, []);
        foreach ($resultUser as $request) {
            $this->assertEquals($this->user->id, $request->user_id);
        }
    }


    public function test_get_by_id_or_fail_returns_request_if_user_authorized(){
        $travelRequest = TravelRequest::factory()->create(['user_id' => $this->user->id]);

        $result = $this->service->getByIdOrFail($travelRequest->id, $this->user);
        $this->assertEquals($travelRequest->id, $result->id);
    }
    

    public function test_get_by_id_or_fail_aborts_if_user_not_authorized(){
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $travelRequest = TravelRequest::factory()->create(['user_id' => $this->admin->id]);

        $this->service->getByIdOrFail($travelRequest->id, $this->user);
    }
}
