<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $positions = Position::orderBy('title')->get();

        $employees = Employee::query()
            ->with('position')
            ->when($request->q, function ($q) use ($request) {
                $search = $request->q;
                $q->where(function ($qq) use ($search) {
                    $qq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('cin', 'like', "%{$search}%")
                        ->orWhere('cnss', 'like', "%{$search}%");
                });
            })
            ->when($request->position_id, fn($q) => $q->where('position_id', $request->position_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        return view('admin.employees.index', compact('employees', 'positions'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $positions = Position::query()
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('admin.employees.create', compact('positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $employee = Employee::create($request->validated());

        return redirect()
            ->route('admin.employees.show', $employee)
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee): View
    {
        $employee->load([
            'position:id,title',
            'documents' => fn ($q) => $q->latest(),
            'vacations' => fn ($q) => $q->latest(),
            'salaries'  => fn ($q) => $q->orderByDesc('month'),
            'vacations' => fn ($q) => $q->latest()->with('approvedBy:id,name'),
        ]);

        return view('admin.employees.show', compact('employee'));
    }


    public function edit(Employee $employee): View
    {
        $positions = Position::query()->orderBy('title')->get(['id', 'title']);
        return view('admin.employees.edit', compact('employee', 'positions'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $employee->update($request->validated());

        return redirect()
            ->route('admin.employees.show', $employee)
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()
            ->route('admin.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
