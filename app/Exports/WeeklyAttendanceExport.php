<?php
namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WeeklyAttendanceExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = [];

        foreach ($this->data as $item) {

            // ✅ Summary Row
            $rows[] = [
                'Week' => $item->week_start . ' - ' . $item->week_end,
                'User' => $item->user_name,
                'Type' => 'Summary',
                'Class' => '',
                'Date' => '',
                'Time' => '',
                'Clock In' => '',
                'Status' => '',
                'Total Classes' => $item->total_classes,
                'Present Count' => $item->present_count,
                'Stipend' => $item->settled_stipend_amount,
            ];

            // ✅ Detail Rows
            foreach ($item->schedules as $schedule) {

                $attendance = $schedule->attendances->first();

                $status = 'Absent';

                if ($attendance && $attendance->clock_in_time) {
                    $scheduleDateTime = \Carbon\Carbon::parse($schedule->schedule_date.' '.$schedule->schedule_time);
                    $clockInTime = \Carbon\Carbon::parse($attendance->clock_in_time);
                    $diffMinutes = $clockInTime->diffInMinutes($scheduleDateTime, false);

                    if (abs($diffMinutes) <= 15) {
                        $status = 'Present';
                    } elseif (abs($diffMinutes) > 15 && abs($diffMinutes) <= 30) {
                        $status = 'Late';
                    } elseif(abs($diffMinutes) > 30) {
                        $status = 'Absent';
                    }
                    else{
                        $status = 'Present';
                    }
                }

                $rows[] = [
                    'Week' => '',
                    'User' => '',
                    'Type' => 'Detail',
                    'Class' => $schedule->mainclass->name ?? '',
                    'Date' => $schedule->schedule_date,
                    'Time' => $schedule->schedule_time,
                    'Clock In' => $attendance->clock_in_time ?? 'Not Clocked In',
                    'Status' => $status,
                    'Total Classes' => '',
                    'Present Count' => '',
                    'Stipend' => '',
                ];
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Week',
            'User',
            'Type',
            'Class',
            'Date',
            'Time',
            'Clock In',
            'Status',
            'Total Classes',
            'Present Count',
            'Stipend'
        ];
    }
}