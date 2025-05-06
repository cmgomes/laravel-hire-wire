<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'account_type_id' => ['required', 'integer', 'exists:account_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'account_type_id.required' => 'O tipo de conta é obrigatório.',
            'account_type_id.integer' => 'O tipo de conta deve ser um número inteiro.',
            'account_type_id.exists' => 'O tipo de conta selecionado não existe.',
            'title.required' => 'O título da conta é obrigatório.',
            'title.string' => 'O título da conta deve ser um texto.',
            'title.max' => 'O título da conta não pode ter mais de 255 caracteres.',
            'description.string' => 'A descrição da conta deve ser um texto.',
        ];
    }
}
