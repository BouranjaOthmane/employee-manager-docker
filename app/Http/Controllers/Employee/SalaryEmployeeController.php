<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use Illuminate\View\View;

class SalaryEmployeeController extends Controller
{
    public function index(): View
    {
        $employee = auth()->user()->employee;

        abort_unless($employee, 403, 'Employee profile not linked to this user.');

        $salaries = Salary::query()
            ->where('employee_id', $employee->id)
            ->orderByDesc('month')
            ->paginate(10);

        $totals = [
            'base' => Salary::where('employee_id', $employee->id)->sum('base_salary'),
            'bonus' => Salary::where('employee_id', $employee->id)->sum('bonus'),
            'deduction' => Salary::where('employee_id', $employee->id)->sum('deduction'),
            'net' => Salary::where('employee_id', $employee->id)->sum('net_salary'),
        ];

        return view('employee.salaries.index', compact(
            'employee',
            'salaries',
            'totals'
        ));
    }
}
