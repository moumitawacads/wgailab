<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function getWeeklyAttendanceQuery($startOfWeek = null, $endOfWeek = null)
    {
        $query = DB::table('users_classes_mappings')
            ->join('users', 'users.id', '=', 'users_classes_mappings.user_id')
            ->leftJoin('attendances', function ($join) {
                $join->on('attendances.schedule_id', '=', 'users_classes_mappings.id')
                    ->on('attendances.user_id', '=', 'users_classes_mappings.user_id');
            });

        // ✅ Apply date range filter
        if ($startOfWeek && $endOfWeek) {
            $query->whereBetween('users_classes_mappings.schedule_date', [
                $startOfWeek,
                $endOfWeek
            ]);
        }

        return $query->select(
                'users.id as user_id',
                'users.name as user_name',
                'users.email as user_email',
                'users.phone as user_phone',
                DB::raw("MIN(schedule_date) as week_start"),
                DB::raw("MAX(schedule_date) as week_end"),

                DB::raw('COUNT(*) as total_classes'),

                DB::raw("
                    SUM(
                        CASE 
                            WHEN attendances.clock_in_time IS NOT NULL 
                            AND ABS(TIMESTAMPDIFF(MINUTE, 
                                CONCAT(users_classes_mappings.schedule_date, ' ', users_classes_mappings.schedule_time),
                                attendances.clock_in_time
                            )) <= 15
                            THEN 1
                            ELSE 0
                        END
                    ) as present_count
                ")
            )
            ->groupBy('users.id', 'users.name') // ✅ IMPORTANT CHANGE
            ->orderBy('week_start', 'desc');
    }
}