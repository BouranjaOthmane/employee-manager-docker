<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Vacation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VacationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $type   = $request->string('type')->toString();
        $employeeId = $request->integer('employee_id') ?: null;

        $dateFrom = $request->string('from')->toString(); // YYYY-MM-DD
        $dateTo   = $request->string('to')->toString();   // YYYY-MM-DD

        $employees = Employee::query()
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        $vacations = Vacation::query()
            ->with([
                'employee:id,first_name,last_name',
                'approvedBy:id,name',
            ])
            ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
            ->when(in_array($status, ['pending','approved','rejected'], true), fn($q) => $q->where('status', $status))
            ->when(in_array($type, ['paid','unpaid','sick','other'], true), fn($q) => $q->where('type', $type))
            ->when($dateFrom !== '', fn($q) => $q->whereDate('start_date', '>=', $dateFrom))
            ->when($dateTo !== '', fn($q) => $q->whereDate('end_date', '<=', $dateTo))
            ->orderByDesc('start_date')
            ->paginate(15)
            ->withQueryString();

        return view('admin.vacations.index', compact('vacations', 'employees'));
    }
}
