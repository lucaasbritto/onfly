<?php

namespace App\Http\Controllers;

use App\Models\TravelRequest;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTravelRequest;
use App\Http\Requests\UpdateTravelRequestStatusRequest;

class TravelRequestController extends Controller{
    public function index(Request $request){

        $user = auth()->user();
        $query = TravelRequest::with(['user', 'updatedByUser']);

        if (!$user->is_admin) {
            $query->where('user_id', $user->id);
        }       

        $query->when($request->filled('status'), fn($q) => $q->where('status', $request->status));
        $query->when($request->filled('destino'), fn($q) => $q->where('destino', 'like', '%'.$request->destino.'%'));
        $query->when($request->filled('start_date'), fn($q) => $q->whereDate('data_ida', '>=', $request->start_date));
        $query->when($request->filled('end_date'), fn($q) => $q->whereDate('data_volta', '<=', $request->end_date));

        $perPage = $request->get('per_page', 10);
        $requests = $query->orderBy('data_ida')->paginate($perPage);

        return response()->json($requests);
    }

        
    public function store(StoreTravelRequest $request){

        $input = $request->validated();
        $user = auth()->user();

        $travel = $user->travelRequests()->create([
            'destino'     => $input['destino'],
            'data_ida'    => $input['data_ida'],
            'data_volta'  => $input['data_volta'],
            'status'      => 'solicitado',
        ]);

       return response()->json([
            'message' => 'Pedido de viagem criado com sucesso.',
            'data'    => $travel,
        ], 201);
    }

    public function updateStatus(UpdateTravelRequestStatusRequest $request, $id){

        $validated = $request->validated();

        $requestModel = TravelRequest::findOrFail($id);

        $requestModel->update([
            'status'     => $validated['status'],
            'updated_by' => auth()->id(),
        ]);

       $requestModel->load('user');

        return response()->json([
            'message' => 'Status atualizado com sucesso.',
            'data'    => $requestModel
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(TravelRequest $travelRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TravelRequest $travelRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TravelRequest $travelRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TravelRequest $travelRequest)
    {
        //
    }
}
