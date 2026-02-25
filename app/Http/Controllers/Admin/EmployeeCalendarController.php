<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Vacation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\EmployeeDayOverride;


class EmployeeCalendarController extends Controller
{
    public function show(Request $request, Employee $employee): View
    {
        // month format: YYYY-MM (example: 2026-02)
        $month = $request->string('month')->toString();
        $current = preg_match('/^\d{4}-\d{2}$/', $month)
            ? Carbon::createFromFormat('Y-m', $month)->startOfMonth()
            : now()->startOfMonth();


        $start = $current->copy()->startOfMonth();
        $end   = $current->copy()->endOfMonth();

        $overrides = EmployeeDayOverride::query()
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn($o) => $o->date->toDateString());


        // Holidays in that month (company days off)
        $holidays = Holiday::query()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn($h) => Carbon::parse($h->date)->toDateString()); // "YYYY-MM-DD"

        // Vacations overlapping the month (for this employee)
        $vacations = Vacation::query()
            ->where('employee_id', $employee->id)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($qq) use ($start, $end) {
                        $qq->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            })
            ->get();

        $hireDate = $employee->hire_date ? Carbon::parse($employee->hire_date)->startOfDay() : null;

        // Build a map date => vacation
        $vacMap = [];
        foreach ($vacations as $vac) {
            $vStart = Carbon::parse($vac->start_date)->max($start);
            $vEnd   = Carbon::parse($vac->end_date)->min($end);

            for ($d = $vStart->copy(); $d->lte($vEnd); $d->addDay()) {
                $vacMap[$d->toDateString()] = $vac; // last wins (ok for now)
            }
        }

        // Calendar grid starts on Monday (ISO)
        $gridStart = $start->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd   = $end->copy()->endOfWeek(Carbon::SUNDAY);

        $days = [];
        $days = [];

        for ($date = $gridStart->copy(); $date->lte($gridEnd); $date->addDay()) {
            $key = $date->toDateString();
            $isCurrentMonth = $date->month === $start->month;

            // 0) Before hire_date -> EMPTY
            if ($hireDate && $date->lt($hireDate)) {
                $days[] = [
                    'date' => $date->copy(),
                    'in_month' => $isCurrentMonth,
                    'type' => 'empty',
                    'label' => '',
                    'tooltip' => 'Not employed yet',
                ];
                continue;
            }

            // 1) Manual override (top priority)
            $override = $overrides->get($key);
            if ($override) {
                $days[] = [
                    'date' => $date->copy(),
                    'in_month' => $isCurrentMonth,
                    'type' => $override->status, // working/off/vacation
                    'label' => strtoupper($override->status),
                    'tooltip' => $override->reason ? ('Manual • ' . $override->reason) : 'Manual override',
                ];
                continue;
            }

            // 2) Vacation
            if (isset($vacMap[$key])) {
                $vac = $vacMap[$key];
                $days[] = [
                    'date' => $date->copy(),
                    'in_month' => $isCurrentMonth,
                    'type' => 'vacation',
                    'label' => strtoupper($vac->status),
                    'tooltip' => ($vac->type ?? 'vacation') . ' • ' . ($vac->reason ?? ''),
                ];
                continue;
            }

            // 3) Off: weekend or holiday
            $isWeekend = $date->isWeekend();
            if ($isWeekend || $holidays->has($key)) {
                $holiday = $holidays->get($key);
                $days[] = [
                    'date' => $date->copy(),
                    'in_month' => $isCurrentMonth,
                    'type' => 'off',
                    'label' => $holiday ? $holiday->name : 'repos',
                    'tooltip' => $holiday ? ($holiday->reason ?? $holiday->name) : 'Weekend',
                ];
                continue;
            }

            // 4) Work
            $days[] = [
                'date' => $date->copy(),
                'in_month' => $isCurrentMonth,
                'type' => 'working',
                'label' => 'Travail',
                'tooltip' => 'Working day',
            ];
        }

        $prevMonth = $current->copy()->subMonth()->format('Y-m');
        $nextMonth = $current->copy()->addMonth()->format('Y-m');

        return view('admin.employees.calendar', compact(
            'employee',
            'current',
            'prevMonth',
            'nextMonth',
            'days'
        ));
    }
}
