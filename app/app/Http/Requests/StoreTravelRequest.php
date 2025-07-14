<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTravelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'destino'     => 'required|string|max:255',
            'data_ida'    => 'required|date',
            'data_volta'  => 'required|date|after_or_equal:data_ida',
        ];
    }

    public function messages()
    {
        return [
            'destino.required' => 'O destino é obrigatório.',
            'data_ida.required' => 'A data de ida é obrigatória.',
            'data_volta.after_or_equal' => 'A data de volta deve ser igual ou posterior à data de ida.',
        ];
    }
}
