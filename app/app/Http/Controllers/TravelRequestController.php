<?php

namespace App\Http\Controllers;

use App\Models\TravelRequest;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTravelRequest;
use App\Http\Requests\UpdateTravelRequestStatusRequest;
use App\Notifications\TravelRequestStatusUpdated;
use App\Services\TravelRequestService;

class TravelRequestController extends Controller{


    protected TravelRequestService $service;

    public function __construct(TravelRequestService $service)
    {
        $this->service = $service;
    }


    public function index(Request $request){

        $user = auth()->user();
        $filters = array_filter($request->all());
        $requests = $this->service->getFilteredRequests($user, $filters);

        return response()->json($requests);
    }

        
    public function store(StoreTravelRequest $request){

        $user = auth()->user();
        $travelRequest = $this->service->createTravelRequest($user, $request->validated());

        return response()->json([
            'message' => 'Pedido de viagem criado com sucesso.',
            'data' => $travelRequest,
        ], 201);
    }


    public function show($id){
        $user = auth()->user();
        $travelRequest = $this->service->getByIdOrFail($id, $user);

        return response()->json($travelRequest);
    }


    public function updateStatus(UpdateTravelRequestStatusRequest $request, $id){
        $user = auth()->user();
        $travelRequest = TravelRequest::findOrFail($id);

        $travelRequest = $this->service->updateStatus($travelRequest, $request->status, $user);

        return response()->json([
            'message' => 'Status atualizado com sucesso.',
            'data' => $travelRequest,
        ]);
    }
}
