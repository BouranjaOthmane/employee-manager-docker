<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVacationRequest;
use App\Models\Vacation;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class VacationEmployeeController extends Controller
{
    public function index(): View
    {
        $employee = auth()->user()->employee;

        abort_unless($employee, 403);

        $vacations = Vacation::where('employee_id', $employee->id)
            ->latest()
            ->paginate(10);

        return view('employee.vacations.index', compact('employee', 'vacations'));
    }

    public function store(StoreVacationRequest $request): RedirectResponse
    {
        $employee = auth()->user()->employee;

        abort_unless($employee, 403);

        Vacation::create([
            'employee_id' => $employee->id,
            'start_date' => $request->validated()['start_date'],
            'end_date' => $request->validated()['end_date'],
            'type' => $request->validated()['type'],
            'reason' => $request->validated()['reason'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('employee.vacations.index')
            ->with('success', 'Vacation request sent successfully.');
    }
}