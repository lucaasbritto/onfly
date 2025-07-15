<?php

namespace App\Services;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TravelRequestStatusUpdated;
use Illuminate\Auth\Access\AuthorizationException;

class TravelRequestService
{

    public function getFilteredRequests(User $user, array $filters){
        $query = TravelRequest::with(['user', 'updatedByUser']);

        if (!$user->is_admin) {
            $query->where('user_id', $user->id);

            unset($filters['admin_id']);
            unset($filters['user_id']);
        }

        $query->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']));
        $query->when(!empty($filters['destino']), fn($q) => $q->where('destino', 'like', '%'.$filters['destino'].'%'));
        $query->when(!empty($filters['start_date']), fn($q) => $q->whereDate('data_ida', '>=', $filters['start_date']));
        $query->when(!empty($filters['end_date']), fn($q) => $q->whereDate('data_volta', '<=', $filters['end_date']));
        $query->when(!empty($filters['id']), fn($q) => $q->where('id', $filters['id']));
        $query->when(!empty($filters['user_id']), fn($q) => $q->where('user_id', $filters['user_id']));
        $query->when(!empty($filters['admin_id']), function ($q) use ($filters) {
            $q->whereHas('updatedByUser', fn($sub) => $sub->where('id', $filters['admin_id']));
        });

        return $query->orderBy('data_ida')->paginate($filters['per_page'] ?? 10);
    }


    public function createTravelRequest(User $user, array $data): TravelRequest{        
        return $user->travelRequests()->create([
            'destino'    => $data['destino'],
            'data_ida'   => $data['data_ida'],
            'data_volta' => $data['data_volta'],
            'status'     => 'solicitado',
        ]);
    }

    public function updateStatus(TravelRequest $travelRequest, string $newStatus, $user): TravelRequest{        
        if (!$user->is_admin) {
            throw new AuthorizationException('Usuário não autorizado a alterar status.');
        }

        if ($travelRequest->status === 'aprovado' && $newStatus === 'cancelado') {
            throw ValidationException::withMessages([
                'status' => ['Não é possível cancelar um pedido que já foi aprovado.']
            ]);
        }

        $travelRequest->update([
            'status' => $newStatus,
            'updated_by' => $user->id,
        ]);


        $travelRequest->load('user');
        Notification::send($travelRequest->user, new TravelRequestStatusUpdated($travelRequest));

        return $travelRequest;
    }

    public function getByIdOrFail(int $id, User $user): TravelRequest{
        $query = TravelRequest::with(['user', 'updatedByUser'])->where('id', $id);

        if (!$user->is_admin) {
            $query->where('user_id', $user->id);
        }

        $travelRequest = $query->first();

        if (!$travelRequest) {
            abort(403, 'Pedido não encontrado ou sem permissão');
        }

        return $travelRequest;
    }
}
