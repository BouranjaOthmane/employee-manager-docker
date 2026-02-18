<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalaryController extends Controller
{
    public function index(Request $request): View
    {
        $month = $request->string('month')->toString();
        $employeeId = $request->integer('employee_id') ?: null;

        $monthStart = null;
        $monthEnd = null;

        if (preg_match('/^\d{4}-\d{2}$/', $month)) {
            $monthStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $monthEnd   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        }

        // List query (with relations, order, pagination)
        $baseQuery = Salary::query()
            ->with(['employee:id,first_name,last_name'])
            ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
            ->when($monthStart && $monthEnd, fn($q) => $q->whereBetween('month', [
                $monthStart->toDateString(),
                $monthEnd->toDateString()
            ]))
            ->orderByDesc('month')
            ->orderByDesc('id');

        // ✅ Totals query (NO with(), NO orderBy, NO selecting *)
        $totals = Salary::query()
            ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
            ->when($monthStart && $monthEnd, fn($q) => $q->whereBetween('month', [
                $monthStart->toDateString(),
                $monthEnd->toDateString()
            ]))
            ->selectRaw('
            COALESCE(SUM(base_salary),0) as base_total,
            COALESCE(SUM(bonus),0) as bonus_total,
            COALESCE(SUM(deduction),0) as deduction_total,
            COALESCE(SUM(net_salary),0) as net_total,
            COUNT(*) as records
        ')
            ->first();

        $employees = \App\Models\Employee::query()
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);


        $salaries = $baseQuery->paginate(15)->withQueryString();

        return view('admin.salaries.index', compact('salaries', 'employees', 'totals'));
    }
}
