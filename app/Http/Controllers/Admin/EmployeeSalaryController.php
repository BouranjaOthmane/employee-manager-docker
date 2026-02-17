<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSalaryRequest;
use App\Models\Employee;
use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;

class EmployeeSalaryController extends Controller
{
    public function store(StoreSalaryRequest $request, Employee $employee): RedirectResponse
    {
        $data = $request->validated();

        $base = (float) $data['base_salary'];
        $bonus = (float) ($data['bonus'] ?? 0);
        $deduction = (float) ($data['deduction'] ?? 0);

        Salary::create([
            'employee_id' => $employee->id,
            'month'       => Carbon::parse($data['month'])->startOfMonth(),
            'base_salary' => $base,
            'bonus'       => $bonus,
            'deduction'   => $deduction,
            'net_salary'  => $base + $bonus - $deduction,
            'note'        => $data['note'] ?? null,
        ]);

        return redirect()
            ->to(route('admin.employees.show', $employee) . '?tab=salaries')
            ->with('success', 'Salary record added successfully.');
    }
}
