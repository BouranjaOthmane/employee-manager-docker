<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;

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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
