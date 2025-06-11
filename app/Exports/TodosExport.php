<?php

namespace App\Exports;

use App\Models\Todo;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class TodosExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Todo::query();
        if (!empty($this->filters['title'])) {
            $query->where('title', 'like', '%' . $this->filters['title'] . '%');
        }

        if (!empty($this->filters['assignee'])) {
            $assignees = explode(',', $this->filters['assignee']);
            $query->whereIn('assignee', array_map('trim', $assignees));
        }

        if (!empty($this->filters['due_date_start']) && !empty($this->filters['due_date_end'])) {
            $query->whereBetween('due_date', [$this->filters['due_date_start'], $this->filters['due_date_end']]);
        } elseif (!empty($this->filters['due_date_start'])) {
            $query->where('due_date', '>=', $this->filters['due_date_start']);
        } elseif (!empty($this->filters['due_date_end'])) {
            $query->where('due_date', '<=', $this->filters['due_date_end']);
        }

        if (isset($this->filters['time_tracked_min']) && isset($this->filters['time_tracked_max'])) {
            $query->whereBetween('time_tracked', [$this->filters['time_tracked_min'], $this->filters['time_tracked_max']]);
        } elseif (isset($this->filters['time_tracked_min'])) {
            $query->where('time_tracked', '>=', $this->filters['time_tracked_min']);
        } elseif (isset($this->filters['time_tracked_max'])) {
            $query->where('time_tracked', '<=', $this->filters['time_tracked_max']);
        }

        if (!empty($this->filters['status'])) {
            $statuses = explode(',', $this->filters['status']);
            $query->whereIn('status', array_map('trim', $statuses));
        }

        if (!empty($this->filters['priority'])) {
            $priorities = explode(',', $this->filters['priority']);
            $query->whereIn('priority', array_map('trim', $priorities));
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Title',
            'Assignee',
            'Due Date',
            'Time Tracked (minutes)',
            'Status',
            'Priority',
        ];
    }

    /**
     * @var Todo $todo
     */
    public function map($todo): array
    {
        return [
            $todo->title,
            $todo->assignee,
            $todo->due_date->format('Y-m-d'),
            (int) $todo->time_tracked,
            ucfirst($todo->status),
            ucfirst($todo->priority),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F81BD']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastDataRow = $sheet->getHighestRow();
                $summaryRowIndex = $lastDataRow + 1;

                $baseQueryForTotals = Todo::query();
                if (!empty($this->filters['title'])) {
                    $baseQueryForTotals->where('title', 'like', '%' . $this->filters['title'] . '%');
                }
                if (!empty($this->filters['assignee'])) {
                    $assignees = explode(',', $this->filters['assignee']);
                    $baseQueryForTotals->whereIn('assignee', array_map('trim', $assignees));
                }
                if (!empty($this->filters['due_date_start']) && !empty($this->filters['due_date_end'])) {
                    $baseQueryForTotals->whereBetween('due_date', [$this->filters['due_date_start'], $this->filters['due_date_end']]);
                } elseif (!empty($this->filters['due_date_start'])) {
                    $baseQueryForTotals->where('due_date', '>=', $this->filters['due_date_start']);
                } elseif (!empty($this->filters['due_date_end'])) {
                    $baseQueryForTotals->where('due_date', '<=', $this->filters['due_date_end']);
                }
                if (isset($this->filters['time_tracked_min']) && isset($this->filters['time_tracked_max'])) {
                    $baseQueryForTotals->whereBetween('time_tracked', [$this->filters['time_tracked_min'], $this->filters['time_tracked_max']]);
                } elseif (isset($this->filters['time_tracked_min'])) {
                    $baseQueryForTotals->where('time_tracked', '>=', $this->filters['time_tracked_min']);
                } elseif (isset($this->filters['time_tracked_max'])) {
                    $baseQueryForTotals->where('time_tracked', '<=', $this->filters['time_tracked_max']);
                }
                if (!empty($this->filters['status'])) {
                    $statuses = explode(',', $this->filters['status']);
                    $baseQueryForTotals->whereIn('status', array_map('trim', $statuses));
                }
                if (!empty($this->filters['priority'])) {
                    $priorities = explode(',', $this->filters['priority']);
                    $baseQueryForTotals->whereIn('priority', array_map('trim', $priorities));
                }

                $totalTodos = $baseQueryForTotals->count();
                $totalTimeTracked = 0;
                foreach ($baseQueryForTotals->cursor() as $todo) {
                    $totalTimeTracked += (int) $todo->time_tracked;
                }

                $sheet->setCellValue('A' . $summaryRowIndex, 'Total Todos:');
                $sheet->setCellValue('B' . $summaryRowIndex, $totalTodos);
                $sheet->setCellValue('C' . $summaryRowIndex, 'Total Time Tracked:');
                $sheet->setCellValue('D' . $summaryRowIndex, $totalTimeTracked);

                $highestColumn = $sheet->getHighestColumn();

                $highestColumnAsLetter = Coordinate::stringFromColumnIndex(
                    Coordinate::columnIndexFromString($highestColumn)
                );

                $summaryRowRange = 'A' . $summaryRowIndex . ':' . $highestColumnAsLetter . $summaryRowIndex;
                $sheet->getStyle($summaryRowRange)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9D9D9']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
                ]);
            },
        ];
    }
}
