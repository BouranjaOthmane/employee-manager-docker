<?php

namespace App\Exports;

use App\Models\Employee;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EmployeeCalendarStyledExport implements WithEvents
{
    public function __construct(
        protected Employee $employee,
        protected Carbon $current,
        protected array $days
    ) {}

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                Carbon::setLocale('fr');
                setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');

                $sheet = $event->sheet->getDelegate();

                // Garder uniquement les jours du mois courant
                $monthDays = array_values(array_filter($this->days, function ($day) {
                    return $day['in_month'] === true;
                }));

                $dayCount = count($monthDays);
                $lastColIndex = $dayCount + 1; // A = employé
                $lastCol = Coordinate::stringFromColumnIndex($lastColIndex);

                // ===== TITRE DU MOIS =====
                $monthTitle = mb_strtoupper($this->current->translatedFormat('F Y'));

                $sheet->mergeCells("B1:{$lastCol}1");
                $sheet->setCellValue('B1', $monthTitle);

                $sheet->getStyle("B1:{$lastCol}1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 18,
                        'color' => ['rgb' => '8B5A2B'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F6EEE7'],
                    ],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(34);

                // ===== COLONNE EMPLOYÉ =====
                $sheet->mergeCells("A2:A3");
                $sheet->setCellValue('A2', "Nom de l'employé");

                $sheet->getStyle("A2:A3")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 13,
                        'color' => ['rgb' => '8B5A2B'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'EADCCF'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D0D0D0'],
                        ],
                    ],
                ]);

                // ===== JOURS SEMAINE + NUMÉROS =====
                $colIndex = 2; // B
                foreach ($monthDays as $day) {
                    $col = Coordinate::stringFromColumnIndex($colIndex);

                    $weekday = match ($day['date']->dayOfWeekIso) {
                        1 => 'lun.',
                        2 => 'mar.',
                        3 => 'mer.',
                        4 => 'jeu.',
                        5 => 'ven.',
                        6 => 'sam.',
                        7 => 'dim.',
                    };

                    $dayNumber = $day['date']->format('j');

                    $sheet->setCellValue("{$col}2", $weekday);
                    $sheet->setCellValue("{$col}3", $dayNumber);

                    $headerBg = $day['date']->isWeekend() ? 'C9C9C9' : '8B4513';

                    $sheet->getStyle("{$col}2:{$col}3")->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
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

                    $sheet->getColumnDimension($col)->setWidth(6);
                    $colIndex++;
                }

                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(3)->setRowHeight(22);

                // ===== LIGNE EMPLOYÉ =====
                $row = 4;
                $sheet->setCellValue("A{$row}", $this->employee->full_name);

                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font' => [
                        'size' => 12,
                        'color' => ['rgb' => '333333'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F2F2F2'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D9D9D9'],
                        ],
                    ],
                ]);

                $sheet->getColumnDimension('A')->setWidth(32);
                $sheet->getRowDimension($row)->setRowHeight(32);

                // ===== CELLULES STATUT =====
                $colIndex = 2;
                foreach ($monthDays as $day) {
                    $col = Coordinate::stringFromColumnIndex($colIndex);
                    $cell = "{$col}{$row}";

                    $text = '';
                    $fill = 'FFFFFF';
                    $font = '333333';

                    switch ($day['type']) {
                        case 'working':
                            $text = 'TR';
                            $fill = '6E8F3A'; // vert
                            $font = 'FFFFFF';
                            break;

                        case 'off':
                            $text = 'RP';
                            $fill = 'E53935'; // rouge
                            $font = 'FFFFFF';
                            break;

                        case 'holiday':
                            $text = 'JF';
                            $fill = '2F5DA8'; // bleu
                            $font = 'FFFFFF';
                            break;

                        case 'paid':
                            $text = 'CP';
                            $fill = 'FFD54F'; // jaune
                            $font = '000000';
                            break;

                        case 'sick':
                            $text = 'ML';
                            $fill = 'F39C12'; // orange
                            $font = 'FFFFFF';
                            break;

                        case 'unpaid':
                            $text = 'CNP';
                            $fill = '8E44AD'; // violet
                            $font = 'FFFFFF';
                            break;

                        case 'empty':
                            $text = '';
                            $fill = 'EFEFEF'; // gris
                            $font = '333333';
                            break;

                        default:
                            $text = '';
                            $fill = 'FFFFFF';
                            $font = '333333';
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
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'FFFFFF'],
                            ],
                        ],
                    ]);

                    $colIndex++;
                }

                // ===== BORDURE GLOBALE =====
                $sheet->getStyle("A1:{$lastCol}{$row}")->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // ===== LÉGENDE =====
                $legendRow = 6;

                $sheet->setCellValue("A{$legendRow}", 'Légende');

                $sheet->setCellValue("B{$legendRow}", 'TR');
                $sheet->setCellValue("C{$legendRow}", 'Travail');

                $sheet->setCellValue("B" . ($legendRow + 1), 'RP');
                $sheet->setCellValue("C" . ($legendRow + 1), 'Repos');

                $sheet->setCellValue("B" . ($legendRow + 2), 'JF');
                $sheet->setCellValue("C" . ($legendRow + 2), 'Jours férié');

                $sheet->setCellValue("B" . ($legendRow + 3), 'CP');
                $sheet->setCellValue("C" . ($legendRow + 3), 'Congé Payé');

                $sheet->setCellValue("B" . ($legendRow + 4), 'ML');
                $sheet->setCellValue("C" . ($legendRow + 4), 'Maladie');

                $sheet->setCellValue("B" . ($legendRow + 5), 'CNP');
                $sheet->setCellValue("C" . ($legendRow + 5), 'Congé non payé');

                $sheet->getStyle("A{$legendRow}:C" . ($legendRow + 5))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D9D9D9'],
                        ],
                    ],
                ]);

                // Couleurs légende
                $sheet->getStyle("B{$legendRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('6E8F3A');
                $sheet->getStyle("B" . ($legendRow + 1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E53935');
                $sheet->getStyle("B" . ($legendRow + 2))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('2F5DA8');
                $sheet->getStyle("B" . ($legendRow + 3))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFD54F');
                $sheet->getStyle("B" . ($legendRow + 4))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F39C12');
                $sheet->getStyle("B" . ($legendRow + 5))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('8E44AD');

                // Texte blanc/noir
                $sheet->getStyle("B{$legendRow}:B" . ($legendRow + 2))->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $sheet->getStyle("B" . ($legendRow + 4) . ":B" . ($legendRow + 5))->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $sheet->getStyle("B" . ($legendRow + 3))->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '000000'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $sheet->freezePane('B4');
            },
        ];
    }
}