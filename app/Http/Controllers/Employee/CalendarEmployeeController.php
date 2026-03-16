<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeDayOverride;
use App\Models\Holiday;
use App\Models\Vacation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarEmployeeController extends Controller
{
    public function show(Request $request): View
    {
        $employee = auth()->user()->employee;

        abort_unless($employee, 403, 'Employee profile not linked to this user.');

        // month format: YYYY-MM
        $month = $request->string('month')->toString();

        $current = preg_match('/^\d{4}-\d{2}$/', $month)
            ? Carbon::createFromFormat('Y-m', $month)->startOfMonth()
            : now()->startOfMonth();

        $start = $current->copy()->startOfMonth();
        $end   = $current->copy()->endOfMonth();

        $hireDate = $employee->hire_date ? Carbon::parse($employee->hire_date)->startOfDay() : null;

        $overrides = EmployeeDayOverride::query()
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn ($o) => $o->date->toDateString());

        $holidays = Holiday::query()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn ($h) => Carbon::parse($h->date)->toDateString());

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

        $vacMap = [];
        foreach ($vacations as $vac) {
            $vStart = Carbon::parse($vac->start_date)->max($start);
            $vEnd   = Carbon::parse($vac->end_date)->min($end);

            for ($d = $vStart->copy(); $d->lte($vEnd); $d->addDay()) {
                $vacMap[$d->toDateString()] = $vac;
            }
        }

        $gridStart = $start->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd   = $end->copy()->endOfWeek(Carbon::SUNDAY);

        $days = [];

        for ($date = $gridStart->copy(); $date->lte($gridEnd); $date->addDay()) {
            $key = $date->toDateString();
            $isCurrentMonth = $date->month === $start->month;

            // Before hire date => empty
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

            // Manual override
            $override = $overrides->get($key);
            if ($override) {
                $days[] = [
                    'date' => $date->copy(),
                    'in_month' => $isCurrentMonth,
                    'type' => $override->status,
                    'label' => strtoupper($override->status),
                    'tooltip' => $override->reason ? ('Manual • '.$override->reason) : 'Manual override',
                ];
                continue;
            }

            // Vacation
            if (isset($vacMap[$key])) {
                $vac = $vacMap[$key];
                $days[] = [
                    'date' => $date->copy(),
                    'in_month' => $isCurrentMonth,
                    'type' => 'vacation',
                    'label' => strtoupper($vac->status),
                    'tooltip' => ($vac->type ?? 'vacation').' • '.($vac->reason ?? ''),
                ];
                continue;
            }

            // Weekend / holiday
            $isWeekend = $date->isWeekend();
            if ($isWeekend || $holidays->has($key)) {
                $holiday = $holidays->get($key);
                $days[] = [
                    'date' => $date->copy(),
                    'in_month' => $isCurrentMonth,
                    'type' => 'off',
                    'label' => $holiday ? $holiday->name : 'OFF',
                    'tooltip' => $holiday ? ($holiday->reason ?? $holiday->name) : 'Weekend',
                ];
                continue;
            }

            // Working day
            $days[] = [
                'date' => $date->copy(),
                'in_month' => $isCurrentMonth,
                'type' => 'working',
                'label' => 'WORK',
                'tooltip' => 'Working day',
            ];
        }

        $prevMonth = $current->copy()->subMonth()->format('Y-m');
        $nextMonth = $current->copy()->addMonth()->format('Y-m');

        return view('employee.calendar.show', compact(
            'employee',
            'current',
            'prevMonth',
            'nextMonth',
            'days'
        ));
    }
}