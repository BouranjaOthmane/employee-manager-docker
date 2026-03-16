<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\Salary;
use App\Models\Vacation;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $employee = auth()->user()->employee;

        abort_unless($employee, 403, 'Employee profile not linked to this user.');

        $stats = [
            'vacations_pending' => Vacation::where('employee_id', $employee->id)->where('status', 'pending')->count(),
            'vacations_approved' => Vacation::where('employee_id', $employee->id)->where('status', 'approved')->count(),
            'salary_records' => Salary::where('employee_id', $employee->id)->count(),
        ];

        $latestVacations = Vacation::where('employee_id', $employee->id)
            ->latest()
            ->limit(5)
            ->get();

        $latestSalaries = Salary::where('employee_id', $employee->id)
            ->latest('month')
            ->limit(5)
            ->get();

        $upcomingHolidays = Holiday::whereDate('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->limit(5)
            ->get();

        return view('employee.dashboard', compact(
            'employee',
            'stats',
            'latestVacations',
            'latestSalaries',
            'upcomingHolidays'
        ));
    }
}