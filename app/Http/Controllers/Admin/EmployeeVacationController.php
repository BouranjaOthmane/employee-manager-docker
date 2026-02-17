<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVacationRequest;
use App\Models\Employee;
use App\Models\Vacation;
use Illuminate\Http\RedirectResponse;

class EmployeeVacationController extends Controller
{
    public function store(StoreVacationRequest $request, Employee $employee): RedirectResponse
    {
        Vacation::create([
            'employee_id' => $employee->id,
            'start_date'  => $request->validated()['start_date'],
            'end_date'    => $request->validated()['end_date'],
            'type'        => $request->validated()['type'],
            'reason'      => $request->validated()['reason'] ?? null,
            'status'      => 'pending',
        ]);

        return redirect()
            ->to(route('admin.employees.show', $employee) . '?tab=vacations')
            ->with('success', 'Vacation request created successfully.');
    }
}
