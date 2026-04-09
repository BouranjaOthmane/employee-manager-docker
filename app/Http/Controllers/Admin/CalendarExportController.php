<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EmployeesAbsenceCalendarExport;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDayOverride;
use App\Models\Holiday;
use App\Models\Vacation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Maatwebsite\Excel\Facades\Excel;

class CalendarExportController extends Controller
{
    public function exportAll(Request $request): BinaryFileResponse
    {
        Carbon::setLocale('fr');
        setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');

        $month = $request->string('month')->toString();

        $current = preg_match('/^\d{4}-\d{2}$/', $month)
            ? Carbon::createFromFormat('Y-m', $month)->startOfMonth()
            : now()->startOfMonth();

        $start = $current->copy()->startOfMonth();
        $end   = $current->copy()->endOfMonth();

        $employees = Employee::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $holidays = Holiday::query()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn ($h) => Carbon::parse($h->date)->toDateString());

        $allRows = [];
        $monthlyTotals = [];
        $monthDays = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $key = $date->toDateString();

            $monthDays[] = [
                'date' => $date->copy(),
                'day_number' => (int) $date->format('j'),
                'day_name' => match ($date->dayOfWeekIso) {
                    1 => 'lun',
                    2 => 'mar',
                    3 => 'mer',
                    4 => 'jeu',
                    5 => 'ven',
                    6 => 'sam',
                    7 => 'dim',
                },
                'is_weekend' => $date->isWeekend(),
                'is_holiday' => $holidays->has($key),
            ];

            $monthlyTotals[$key] = 0;
        }

        foreach ($employees as $employee) {
            $rowDays = $this->buildEmployeeMonthDays($employee, $start, $end, $holidays);

            $totalAbsenceDays = 0;

            foreach ($rowDays as $day) {
                if (in_array($day['type'], ['paid', 'unpaid', 'sick'], true)) {
                    $totalAbsenceDays++;
                    $monthlyTotals[$day['date']->toDateString()]++;
                }
            }

            $allRows[] = [
                'employee' => $employee,
                'days' => $rowDays,
                'total_absence_days' => $totalAbsenceDays,
            ];
        }

        $filename = 'calendrier_absences_' . $current->format('Y_m') . '.xlsx';

        return Excel::download(
            new EmployeesAbsenceCalendarExport(
                current: $current,
                monthDays: $monthDays,
                rows: $allRows,
                monthlyTotals: $monthlyTotals
            ),
            $filename
        );
    }

    private function buildEmployeeMonthDays(Employee $employee, Carbon $start, Carbon $end, $holidays): array
    {
        $overrides = EmployeeDayOverride::query()
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn ($o) => $o->date->toDateString());

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

        $hireDate = $employee->hire_date
            ? Carbon::parse($employee->hire_date)->startOfDay()
            : null;

        $vacMap = [];
        foreach ($vacations as $vac) {
            $vStart = Carbon::parse($vac->start_date)->max($start);
            $vEnd   = Carbon::parse($vac->end_date)->min($end);

            for ($d = $vStart->copy(); $d->lte($vEnd); $d->addDay()) {
                $vacMap[$d->toDateString()] = $vac;
            }
        }

        $days = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $key = $date->toDateString();

            if ($hireDate && $date->lt($hireDate)) {
                $days[] = [
                    'date' => $date->copy(),
                    'type' => 'empty',
                    'code' => '',
                    'label' => '',
                ];
                continue;
            }

            $override = $overrides->get($key);
            if ($override) {
                $mapped = match ($override->status) {
                    'working' => ['type' => 'working', 'code' => '', 'label' => 'Travail'],
                    'off' => ['type' => 'off', 'code' => '', 'label' => 'Repos'],
                    'holiday' => ['type' => 'holiday', 'code' => 'JF', 'label' => 'Jours férié'],
                    'paid' => ['type' => 'paid', 'code' => 'CP', 'label' => 'Congé payé'],
                    'sick' => ['type' => 'sick', 'code' => 'M', 'label' => 'Maladie'],
                    'unpaid' => ['type' => 'unpaid', 'code' => 'CNP', 'label' => 'Congé non payé'],
                    default => ['type' => 'working', 'code' => '', 'label' => 'Travail'],
                };

                $days[] = array_merge(['date' => $date->copy()], $mapped);
                continue;
            }

            if (isset($vacMap[$key])) {
                $vac = $vacMap[$key];

                $mapped = match ($vac->type) {
                    'paid' => ['type' => 'paid', 'code' => 'CP', 'label' => 'Congé payé'],
                    'sick' => ['type' => 'sick', 'code' => 'M', 'label' => 'Maladie'],
                    'unpaid' => ['type' => 'unpaid', 'code' => 'CNP', 'label' => 'Congé non payé'],
                    default => ['type' => 'paid', 'code' => 'CP', 'label' => 'Congé payé'],
                };

                $days[] = array_merge(['date' => $date->copy()], $mapped);
                continue;
            }

            if ($holidays->has($key)) {
                $days[] = [
                    'date' => $date->copy(),
                    'type' => 'holiday',
                    'code' => 'JF',
                    'label' => 'Jours férié',
                ];
                continue;
            }

            if ($date->isWeekend()) {
                $days[] = [
                    'date' => $date->copy(),
                    'type' => 'off',
                    'code' => '',
                    'label' => 'Repos',
                ];
                continue;
            }

            $days[] = [
                'date' => $date->copy(),
                'type' => 'working',
                'code' => '',
                'label' => 'Travail',
            ];
        }

        return $days;
    }
}