<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EmployeesAbsenceCalendarExport implements WithEvents
{
    public function __construct(
        protected Carbon $current,
        protected array $monthDays,
        protected array $rows,
        protected array $monthlyTotals
    ) {}

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                Carbon::setLocale('fr');
                setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');

                $sheet = $event->sheet->getDelegate();

                $employeeCol = 'A';
                $startDayColIndex = 2; // B
                $dayCount = count($this->monthDays);

                $lastDayColIndex = $startDayColIndex + $dayCount - 1;
                $lastDayCol = Coordinate::stringFromColumnIndex($lastDayColIndex);

                $totalColIndex = $lastDayColIndex + 1;
                $totalCol = Coordinate::stringFromColumnIndex($totalColIndex);

                // ===== TITLE =====
                $sheet->mergeCells("A1:{$totalCol}1");
                $sheet->setCellValue('A1', "Calendrier des absences des employés");
                $sheet->getStyle("A1:{$totalCol}1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 24,
                        'color' => ['rgb' => '6B2E0F'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(36);

                // ===== LEGEND =====
                $legendRow = 3;
                $sheet->setCellValue("A{$legendRow}", "Clé de motif d’absence");
                $sheet->setCellValue("C{$legendRow}", "CP");
                $sheet->setCellValue("D{$legendRow}", "Congé payé");
                $sheet->setCellValue("F{$legendRow}", "CNP");
                $sheet->setCellValue("G{$legendRow}", "Congé non payé");
                $sheet->setCellValue("I{$legendRow}", "JF");
                $sheet->setCellValue("J{$legendRow}", "Jour férié");
                $sheet->setCellValue("L{$legendRow}", "M");
                $sheet->setCellValue("M{$legendRow}", "Maladie");

                $sheet->getStyle("A{$legendRow}:M{$legendRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle("A{$legendRow}:B{$legendRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DDE3C7');

                $sheet->getStyle("C{$legendRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E67E6B');
                $sheet->getStyle("F{$legendRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1D18A');
                $sheet->getStyle("I{$legendRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DDE3C7');
                $sheet->getStyle("L{$legendRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFD966');

                // ===== MONTH / DATES / YEAR HEADER =====
                $headerTopRow = 5;

                $sheet->setCellValue("A{$headerTopRow}", ucfirst($this->current->translatedFormat('F')));
                $sheet->mergeCells("B{$headerTopRow}:{$lastDayCol}{$headerTopRow}");
                $sheet->setCellValue("B{$headerTopRow}", "Dates d’absence");
                $sheet->setCellValue("{$totalCol}{$headerTopRow}", $this->current->format('Y'));

                $sheet->getStyle("A{$headerTopRow}:{$totalCol}{$headerTopRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 18,
                        'color' => ['rgb' => '7A2D13'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'ECEDE2'],
                    ],
                ]);
                $sheet->getRowDimension($headerTopRow)->setRowHeight(44);

                // ===== WEEKDAY ROW =====
                $weekDayRow = 6;
                $numberRow = 7;

                $sheet->setCellValue("A{$numberRow}", "Nom de l’employé");
                $sheet->setCellValue("{$totalCol}{$numberRow}", "Total des jours");

                $sheet->getStyle("A{$numberRow}:{$totalCol}{$numberRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'EFEFE7'],
                    ],
                ]);

                foreach ($this->monthDays as $i => $day) {
                    $colIndex = $startDayColIndex + $i;
                    $col = Coordinate::stringFromColumnIndex($colIndex);

                    $sheet->setCellValue("{$col}{$weekDayRow}", $day['day_name']);
                    $sheet->setCellValue("{$col}{$numberRow}", $day['day_number']);

                    $headerBg = '5B180A';

                    $sheet->getStyle("{$col}{$weekDayRow}:{$col}{$numberRow}")->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 11,
                            'color' => ['rgb' => 'FFFFFF'],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $headerBg],
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'FFFFFF'],
                            ],
                        ],
                    ]);
                }

                // ===== DIMENSIONS =====
                $sheet->getColumnDimension('A')->setWidth(34);
                for ($i = 0; $i < $dayCount; $i++) {
                    $col = Coordinate::stringFromColumnIndex($startDayColIndex + $i);
                    $sheet->getColumnDimension($col)->setWidth(6);
                }
                $sheet->getColumnDimension($totalCol)->setWidth(16);

                // ===== EMPLOYEE ROWS =====
                $startRow = 8;
                $currentRow = $startRow;

                foreach ($this->rows as $index => $rowData) {
                    $employee = $rowData['employee'];
                    $days = $rowData['days'];
                    $totalAbsenceDays = $rowData['total_absence_days'];

                    $baseRowColor = $index % 2 === 0 ? 'F3F3F3' : 'E3E3E3';

                    $sheet->setCellValue("A{$currentRow}", $employee->full_name);
                    $sheet->setCellValue("{$totalCol}{$currentRow}", $totalAbsenceDays);

                    $sheet->getStyle("A{$currentRow}:{$totalCol}{$currentRow}")->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $baseRowColor],
                        ],
                        'borders' => [
                            'bottom' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'D7D7D7'],
                            ],
                        ],
                    ]);

                    $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                    foreach ($days as $i => $day) {
                        $colIndex = $startDayColIndex + $i;
                        $col = Coordinate::stringFromColumnIndex($colIndex);
                        $cell = "{$col}{$currentRow}";

                        $fill = $baseRowColor;
                        $font = '000000';
                        $text = $day['code'];

                        // weekends / jours fériés background
                        if ($this->monthDays[$i]['is_weekend'] || $this->monthDays[$i]['is_holiday']) {
                            $fill = 'C6D3A2';
                        }

                        switch ($day['type']) {
                            case 'paid':
                                $fill = 'E67E6B';
                                $font = '000000';
                                break;

                            case 'unpaid':
                                $fill = 'F1D18A';
                                $font = '000000';
                                break;

                            case 'holiday':
                                $fill = 'C6D3A2';
                                $font = '000000';
                                break;

                            case 'sick':
                                $fill = 'FFD966';
                                $font = '000000';
                                break;

                            case 'empty':
                                $text = '';
                                break;
                        }

                        $sheet->setCellValue($cell, $text);

                        $sheet->getStyle($cell)->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'size' => 11,
                                'color' => ['rgb' => $font],
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => $fill],
                            ],
                            'borders' => [
                                'left' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['rgb' => 'FFFFFF'],
                                ],
                                'right' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['rgb' => 'FFFFFF'],
                                ],
                            ],
                        ]);
                    }

                    $currentRow++;
                }

                // ===== TOTAL BOTTOM ROW =====
                $sheet->setCellValue("A{$currentRow}", ucfirst($this->current->translatedFormat('F')) . " Total");

                $monthTotal = 0;
                foreach ($this->monthDays as $i => $dayMeta) {
                    $colIndex = $startDayColIndex + $i;
                    $col = Coordinate::stringFromColumnIndex($colIndex);
                    $key = $dayMeta['date']->toDateString();
                    $count = $this->monthlyTotals[$key] ?? 0;

                    $sheet->setCellValue("{$col}{$currentRow}", $count > 0 ? $count : '');

                    $monthTotal += $count;
                }

                $sheet->setCellValue("{$totalCol}{$currentRow}", $monthTotal);

                $sheet->getStyle("A{$currentRow}:{$totalCol}{$currentRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'ECEDE2'],
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D7D7D7'],
                        ],
                    ],
                ]);

                $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // ===== FREEZE =====
                $sheet->freezePane('B8');
            },
        ];
    }
}