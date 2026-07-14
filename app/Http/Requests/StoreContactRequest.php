<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'nome' => 'required',
            'telefone' => 'nullable',
            'email' => 'nullable|email'
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if (!$this->telefone && !$this->email) {

                $validator->errors()->add(
                    'telefone',
                    'Informe telefone ou email.'
                );
            }
        });
    }
}
