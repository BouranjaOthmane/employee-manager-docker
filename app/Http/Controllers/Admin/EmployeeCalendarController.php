<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EmployeeCalendarStyledExport;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDayOverride;
use App\Models\Holiday;
use App\Models\Vacation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EmployeeCalendarController extends Controller
{
    public function show(Request $request, Employee $employee): View
    {
        [$current, $prevMonth, $nextMonth, $days] = $this->buildCalendarData($request, $employee);

        return view('admin.employees.calendar', compact(
            'employee',
            'current',
            'prevMonth',
            'nextMonth',
            'days'
        ));
    }

    public function export(Request $request, Employee $employee): BinaryFileResponse
    {
        [$current, $prevMonth, $nextMonth, $days] = $this->buildCalendarData($request, $employee);

        $filename = 'planning_' . $employee->id . '_' . $current->format('Y_m') . '.xlsx';

        return Excel::download(
            new EmployeeCalendarStyledExport($employee, $current, $days),
            $filename
        );
    }

    private function buildCalendarData(Request $request, Employee $employee): array
    {
        Carbon::setLocale('fr');
        setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');

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

        $holidays = Holiday::query()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn($h) => Carbon::parse($h->date)->toDateString());

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

        // Build map congés
        $vacMap = [];
        foreach ($vacations as $vac) {
            $vStart = Carbon::parse($vac->start_date)->max($start);
            $vEnd   = Carbon::parse($vac->end_date)->min($end);

            for ($d = $vStart->copy(); $d->lte($vEnd); $d->addDay()) {
                $vacMap[$d->toDateString()] = $vac;
            }
        }

        // Grille calendrier
        $gridStart = $start->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd   = $end->copy()->endOfWeek(Carbon::SUNDAY);

        $days = [];

        for ($date = $gridStart->copy(); $date->lte($gridEnd); $date->addDay()) {
            $key = $date->toDateString();
            $isCurrentMonth = $date->month === $start->month;

            // Avant embauche
            if ($hireDate && $date->lt($hireDate)) {
                $days[] = [
                    'date' => $date->copy(),
                    'in_month' => $isCurrentMonth,
                    'type' => 'empty',
                    'label' => '',
                    'tooltip' => 'Pas encore embauché',
                ];
                continue;
            }

            // Override manuel
            $override = $overrides->get($key);
            if ($override) {
                $days[] = [
                    'date' => $date->copy(),
                    'in_month' => $isCurrentMonth,
                    'type' => $override->status,
                    'label' => match ($override->status) {
                        'working' => 'Travail',
                        'off' => 'Repos',
                        'holiday' => 'Jours férié',
                        'paid' => 'Congé Payé',
                        'sick' => 'Maladie',
                        'unpaid' => 'Congé non payé',
                        default => strtoupper($override->status),
                    },
                    'tooltip' => $override->reason ? ('Manuel • ' . $override->reason) : 'Override manuel',
                ];
                continue;
            }

            // Congés
            if (isset($vacMap[$key])) {
                $vac = $vacMap[$key];

                $vacationType = match ($vac->type) {
                    'paid' => 'paid',
                    'sick' => 'sick',
                    'unpaid' => 'unpaid',
                    default => 'paid',
                };

                $vacationLabel = match ($vac->type) {
                    'paid' => 'Congé Payé',
                    'sick' => 'Maladie',
                    'unpaid' => 'Congé non payé',
                    default => 'Congé Payé',
                };

                $days[] = [
                    'date' => $date->copy(),
                    'in_month' => $isCurrentMonth,
                    'type' => $vacationType,
                    'label' => $vacationLabel,
                    'tooltip' => $vacationLabel . (($vac->reason ?? null) ? (' • ' . $vac->reason) : ''),
                ];
                continue;
            }

            // Jour férié
            if ($holidays->has($key)) {
                $holiday = $holidays->get($key);

                $days[] = [
                    'date' => $date->copy(),
                    'in_month' => $isCurrentMonth,
                    'type' => 'holiday',
                    'label' => 'Jours férié',
                    'tooltip' => $holiday ? ($holiday->reason ?? $holiday->name) : 'Jours férié',
                ];
                continue;
            }

            // Weekend
            if ($date->isWeekend()) {
                $days[] = [
                    'date' => $date->copy(),
                    'in_month' => $isCurrentMonth,
                    'type' => 'off',
                    'label' => 'Repos',
                    'tooltip' => 'Weekend',
                ];
                continue;
            }

            // Travail
            $days[] = [
                'date' => $date->copy(),
                'in_month' => $isCurrentMonth,
                'type' => 'working',
                'label' => 'Travail',
                'tooltip' => 'Jour travaillé',
            ];
        }

        $prevMonth = $current->copy()->subMonth()->format('Y-m');
        $nextMonth = $current->copy()->addMonth()->format('Y-m');

        return [$current, $prevMonth, $nextMonth, $days];
    }
}