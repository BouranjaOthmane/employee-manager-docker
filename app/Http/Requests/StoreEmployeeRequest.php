<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        // later you can use policies/roles; for now allow authenticated users
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'position_id' => ['nullable', 'integer', 'exists:positions,id'],

            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],

            'email' => [
                'nullable',
                'email',
                'max:190',
                Rule::unique('employees', 'email'),
            ],

            'phone' => ['nullable', 'string', 'max:50'],

            'cin'  => ['nullable', 'string', 'max:50'],
            'cnss' => ['nullable', 'string', 'max:50'],

            'hire_date' => ['nullable', 'date'],

            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'position_id.exists' => 'Selected position does not exist.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // small cleanup (optional)
        $this->merge([
            'email' => $this->email ? strtolower(trim($this->email)) : null,
        ]);
    }
}
