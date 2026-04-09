<?php

namespace App\Exports;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeCalendarExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function __construct(
        protected Employee $employee,
        protected Carbon $current,
        protected array $days
    ) {}

    public function headings(): array
    {
        return [
            'Date',
            'Day',
            'Month',
            'Employee',
            'Status',
            'Label',
            'Tooltip',
            'In Current Month',
        ];
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->days as $day) {
            $rows[] = [
                $day['date']->format('Y-m-d'),
                $day['date']->translatedFormat('l'),
                $this->current->format('Y-m'),
                $this->employee->full_name,
                $day['type'],
                $day['label'],
                $day['tooltip'],
                $day['in_month'] ? 'Yes' : 'No',
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }
}