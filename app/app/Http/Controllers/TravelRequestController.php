<?php

namespace App\Http\Controllers;

use App\Models\TravelRequest;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTravelRequest;
use App\Http\Requests\UpdateTravelRequestStatusRequest;
use App\Notifications\TravelRequestStatusUpdated;

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
        $query->when($request->filled('id'), fn($q) => $q->where('id', $request->id));
        $query->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id));
        $query->when($request->filled('admin_id'), function ($q) use ($request) {
            $adminId = $request->admin_id;
            $q->whereHas('updatedByUser', fn($sub) => $sub->where('id', $adminId));
        });

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

       $requestModel->load(['user', 'updatedByUser']);
       $requestModel->user->notify(new TravelRequestStatusUpdated($requestModel));

        return response()->json([
            'message' => 'Status atualizado com sucesso.',
            'data'    => $requestModel
        ]);
    }
}
