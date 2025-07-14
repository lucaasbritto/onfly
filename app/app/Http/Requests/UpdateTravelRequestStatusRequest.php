<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\TravelRequest;

class UpdateTravelRequestStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:aprovado,cancelado',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $requestModel = TravelRequest::find($this->route('id'));

            if (!$requestModel) {
                $validator->errors()->add('id', 'Pedido não encontrado.');
                return;
            }

            if ($requestModel->status === 'aprovado' && $this->input('status') === 'cancelado') {
                $validator->errors()->add('status', 'Não é possível cancelar um pedido que já foi aprovado.');
            }
        });
    }
}
