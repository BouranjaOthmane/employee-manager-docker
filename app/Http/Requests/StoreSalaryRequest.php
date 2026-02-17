<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'month'       => ['required', 'date'], // expect YYYY-MM-01
            'base_salary' => ['required', 'numeric', 'min:0'],
            'bonus'       => ['nullable', 'numeric', 'min:0'],
            'deduction'   => ['nullable', 'numeric', 'min:0'],
            'note'        => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $month = $this->month;

        if ($month && preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = $month . '-01';
        }

        $this->merge([
            'month' => $month,
            'bonus' => $this->bonus ?? 0,
            'deduction' => $this->deduction ?? 0,
        ]);
    }
}
