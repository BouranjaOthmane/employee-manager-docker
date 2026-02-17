<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee')?->id ?? $this->route('employee');

        return [
            'position_id' => ['nullable', 'integer', 'exists:positions,id'],

            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],

            'email' => [
                'nullable',
                'email',
                'max:190',
                Rule::unique('employees', 'email')->ignore($employeeId),
            ],

            'phone' => ['nullable', 'string', 'max:50'],

            'cin'  => ['nullable', 'string', 'max:50'],
            'cnss' => ['nullable', 'string', 'max:50'],

            'hire_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => $this->email ? strtolower(trim($this->email)) : null,
        ]);
    }
}
