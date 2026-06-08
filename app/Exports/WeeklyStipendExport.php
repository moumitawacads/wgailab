<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WeeklyStipendExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = [];
        $total = 0;

        foreach ($this->data as $row) {

            // ✅ SAFE USER NAME
            $userName = optional($row->user)->name ?? 'N/A';

            // ✅ SAFE STIPEND
            $stipend = $row->settled_stipend_amount ?? 0;

            $total += $stipend;

            $rows[] = [
                $userName,
                $row->total_classes ?? 0,
                $row->present_count ?? 0,
                round($row->attendance_percentage ?? 0, 2) . '%',
                $stipend
            ];
        }

        // ✅ TOTAL ROW
        $rows[] = [
            'TOTAL',
            '',
            '',
            '',
            $total
        ];

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'User Name',
            'Total Classes',
            'Present Count',
            'Attendance %',
            'Stipend Amount'
        ];
    }
}