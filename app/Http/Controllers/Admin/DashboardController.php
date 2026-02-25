<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Position;
use App\Models\Salary;
use App\Models\Vacation;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd   = now()->endOfMonth()->toDateString();

        $stats = [
            'employees' => Employee::count(),
            'positions' => Position::count(),
            'vacations_pending' => Vacation::where('status', 'pending')->count(),
            'payroll_net_this_month' => number_format(
                (float) Salary::whereBetween('month', [$monthStart, $monthEnd])->sum('net_salary'),
                2
            ),
        ];

        $pendingVacations = Vacation::query()
            ->with(['employee:id,first_name,last_name'])
            ->where('status', 'pending')
            ->orderBy('start_date')
            ->limit(8)
            ->get();

        $upcomingHolidays = Holiday::query()
            ->whereDate('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->limit(6)
            ->get();

        $latestSalaries = Salary::query()
            ->with(['employee:id,first_name,last_name'])
            ->orderByDesc('month')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('dashboard', compact(
            'stats',
            'pendingVacations',
            'upcomingHolidays',
            'latestSalaries'
        ));
    }
}