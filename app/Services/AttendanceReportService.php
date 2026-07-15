<?php
// app/Services/AttendanceReportService.php

namespace App\Services;

use App\Models\User;
use App\Models\UsersClassesMapping;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceReportService
{
    /**
     * Get attendance statistics for a given date range
     * Now only considers sessions that have already started/passed
     */
    public function getAttendanceStats($startDate, $endDate)
    {
        $now = Carbon::now();

        // Get all scheduled sessions for users in the date range
        // But only consider sessions that have already started
        $userAttendance = UsersClassesMapping::select(
            'users.id',
            'users.name',
            'users.email',
            'users.phone',
            'users.role',
            DB::raw('COUNT(users_classes_mappings.id) as total_classes'),
            DB::raw('SUM(CASE 
                WHEN attendances.clock_in_time IS NOT NULL 
                AND ABS(TIMESTAMPDIFF(MINUTE, 
                    CONCAT(users_classes_mappings.schedule_date, " ", users_classes_mappings.schedule_time),
                    attendances.clock_in_time
                )) <= 15 
                THEN 1 
                ELSE 0 
            END) as present_count')
        )
            ->join('users', 'users.id', '=', 'users_classes_mappings.user_id')
            ->leftJoin('attendances', function ($join) {
                $join->on('attendances.schedule_id', '=', 'users_classes_mappings.id')
                    ->on('attendances.user_id', '=', 'users_classes_mappings.user_id');
            })
            ->whereBetween('users_classes_mappings.schedule_date', [$startDate, $endDate])
            ->where('users.role', 'se')
            // KEY CHANGE: Only include sessions that have already started/passed
            ->where(function ($query) use ($now) {
                $query->where('users_classes_mappings.schedule_date', '<', $now->toDateString())
                    ->orWhere(function ($subQuery) use ($now) {
                        $subQuery->where('users_classes_mappings.schedule_date', '=', $now->toDateString())
                            ->where('users_classes_mappings.schedule_time', '<=', $now->toTimeString());
                    });
            })
            ->groupBy('users.id', 'users.name', 'users.email', 'users.phone', 'users.role')
            ->get();

        // Calculate percentages and categorize
        $stats = [
            'above_80' => [],
            'between_70_80' => [],
            'between_60_70' => [],
            'below_60' => [],
        ];

        foreach ($userAttendance as $user) {
            $total = $user->total_classes;
            $present = $user->present_count;

            if ($total == 0) {
                $percentage = 0;
            } else {
                $percentage = ($present / $total) * 100;
            }

            $user->attendance_percentage = round($percentage, 2);

            if ($percentage >= 80) {
                $stats['above_80'][] = $user;
            } elseif ($percentage >= 70 && $percentage < 80) {
                $stats['between_70_80'][] = $user;
            } elseif ($percentage >= 60 && $percentage < 70) {
                $stats['between_60_70'][] = $user;
            } else {
                $stats['below_60'][] = $user;
            }
        }

        return $stats;
    }

    /**
     * Get paginated users for detailed report page with proper attendance calculation
     * FIXED: Using raw where conditions instead of selectRaw with bindings
     */
    public function getDetailedReport($startDate, $endDate, $category = null)
    {
        $now = Carbon::now();
        $nowDateTime = $now->toDateTimeString();

        // First, get all users with their total sessions (only started ones)
        $userAttendance = UsersClassesMapping::select(
            'users.id',
            'users.name',
            'users.email',
            'users.phone',
            'users.role',
            DB::raw('COUNT(users_classes_mappings.id) as total_classes'),
            DB::raw('SUM(CASE 
                WHEN attendances.clock_in_time IS NOT NULL 
                AND ABS(TIMESTAMPDIFF(MINUTE, 
                    CONCAT(users_classes_mappings.schedule_date, " ", users_classes_mappings.schedule_time),
                    attendances.clock_in_time
                )) <= 15 
                THEN 1 
                ELSE 0 
            END) as present_count'),
            DB::raw("SUM(CASE 
                WHEN attendances.clock_in_time IS NULL 
                AND CONCAT(users_classes_mappings.schedule_date, ' ', users_classes_mappings.schedule_time) <= '{$nowDateTime}'
                THEN 1 
                ELSE 0 
            END) as absent_count"),
            DB::raw("SUM(CASE 
                WHEN CONCAT(users_classes_mappings.schedule_date, ' ', users_classes_mappings.schedule_time) > '{$nowDateTime}'
                THEN 1 
                ELSE 0 
            END) as upcoming_count")
        )
            ->join('users', 'users.id', '=', 'users_classes_mappings.user_id')
            ->leftJoin('attendances', function ($join) {
                $join->on('attendances.schedule_id', '=', 'users_classes_mappings.id')
                    ->on('attendances.user_id', '=', 'users_classes_mappings.user_id');
            })
            ->whereBetween('users_classes_mappings.schedule_date', [$startDate, $endDate])
            ->where('users.role', 'se')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.phone', 'users.role')
            ->get();

        // Calculate percentages and filter by category
        $filtered = [];
        foreach ($userAttendance as $user) {
            // Only count started sessions for attendance percentage
            $totalStarted = $user->total_classes - $user->upcoming_count;
            $present = $user->present_count;

            $percentage = $totalStarted > 0 ? ($present / $totalStarted) * 100 : 0;
            $user->attendance_percentage = round($percentage, 2);
            $user->total_started = $totalStarted;
            $user->absent_count = $totalStarted - $present;

            $categoryMatch = false;
            switch ($category) {
                case 'above_80':
                    $categoryMatch = $percentage >= 80;
                    break;
                case 'between_70_80':
                    $categoryMatch = $percentage >= 70 && $percentage < 80;
                    break;
                case 'between_60_70':
                    $categoryMatch = $percentage >= 60 && $percentage < 70;
                    break;
                case 'below_60':
                    $categoryMatch = $percentage < 60;
                    break;
                default:
                    $categoryMatch = true;
            }

            if ($categoryMatch) {
                $filtered[] = $user;
            }
        }

        return collect($filtered);
    }

    /**
     * Get user attendance details with session status - FIXED VERSION
     */
    public function getUserAttendanceDetails($userId, $startDate, $endDate)
    {
        $now = Carbon::now();
        $nowDateTime = $now->toDateTimeString();

        // Using DB::raw with CONCAT and parameter binding properly
        return UsersClassesMapping::select(
            'users_classes_mappings.*',
            'users.name as user_name',
            'users.email as user_email',
            'users.phone as user_phone',
            'classes.name as class_name',
            'sessions.session_name',
            'attendances.clock_in_time',
            DB::raw('TIMESTAMPDIFF(MINUTE, 
                CONCAT(users_classes_mappings.schedule_date, " ", users_classes_mappings.schedule_time),
                attendances.clock_in_time
            ) as minutes_diff'),
            DB::raw("CASE 
                WHEN CONCAT(users_classes_mappings.schedule_date, ' ', users_classes_mappings.schedule_time) <= '{$nowDateTime}' 
                THEN 'started' 
                ELSE 'upcoming' 
            END as session_status"),
            DB::raw("CASE 
                WHEN attendances.clock_in_time IS NOT NULL 
                AND ABS(TIMESTAMPDIFF(MINUTE, 
                    CONCAT(users_classes_mappings.schedule_date, ' ', users_classes_mappings.schedule_time),
                    attendances.clock_in_time
                )) <= 15 
                THEN 'present' 
                WHEN attendances.clock_in_time IS NULL 
                AND CONCAT(users_classes_mappings.schedule_date, ' ', users_classes_mappings.schedule_time) <= '{$nowDateTime}' 
                THEN 'absent' 
                ELSE 'upcoming' 
            END as attendance_status")
        )
            ->join('users', 'users.id', '=', 'users_classes_mappings.user_id')
            ->leftJoin('classes', 'classes.id', '=', 'users_classes_mappings.class_id')
            ->leftJoin('sessions', 'sessions.id', '=', 'users_classes_mappings.session_id')
            ->leftJoin('attendances', function ($join) {
                $join->on('attendances.schedule_id', '=', 'users_classes_mappings.id')
                    ->on('attendances.user_id', '=', 'users_classes_mappings.user_id');
            })
            ->where('users_classes_mappings.user_id', $userId)
            ->whereBetween('users_classes_mappings.schedule_date', [$startDate, $endDate])
            ->orderBy('users_classes_mappings.schedule_date', 'desc')
            ->orderBy('users_classes_mappings.schedule_time', 'desc')
            ->get();
    }

    /**
     * Alternative method using subquery approach - more reliable
     */
    public function getUserAttendanceDetailsAlternative($userId, $startDate, $endDate)
    {
        $now = Carbon::now();

        // First get all mappings with raw status calculations using a different approach
        $mappings = UsersClassesMapping::select(
            'users_classes_mappings.*',
            'users.name as user_name',
            'users.email as user_email',
            'users.phone as user_phone',
            'classes.name as class_name',
            'sessions.session_name',
            'attendances.clock_in_time',
            DB::raw('TIMESTAMPDIFF(MINUTE, 
                CONCAT(users_classes_mappings.schedule_date, " ", users_classes_mappings.schedule_time),
                attendances.clock_in_time
            ) as minutes_diff')
        )
            ->join('users', 'users.id', '=', 'users_classes_mappings.user_id')
            ->leftJoin('classes', 'classes.id', '=', 'users_classes_mappings.class_id')
            ->leftJoin('sessions', 'sessions.id', '=', 'users_classes_mappings.session_id')
            ->leftJoin('attendances', function ($join) {
                $join->on('attendances.schedule_id', '=', 'users_classes_mappings.id')
                    ->on('attendances.user_id', '=', 'users_classes_mappings.user_id');
            })
            ->where('users_classes_mappings.user_id', $userId)
            ->whereBetween('users_classes_mappings.schedule_date', [$startDate, $endDate])
            ->orderBy('users_classes_mappings.schedule_date', 'desc')
            ->orderBy('users_classes_mappings.schedule_time', 'desc')
            ->get();

        // Calculate statuses in PHP instead of SQL
        foreach ($mappings as $mapping) {
            $sessionDateTime = Carbon::parse($mapping->schedule_date . ' ' . $mapping->schedule_time);
            $isStarted = $sessionDateTime->lte($now);

            $mapping->session_status = $isStarted ? 'started' : 'upcoming';

            if ($mapping->clock_in_time && $isStarted) {
                $clockInTime = Carbon::parse($mapping->clock_in_time);
                $minutesDiff = abs($clockInTime->diffInMinutes($sessionDateTime));

                if ($minutesDiff <= 15) {
                    $mapping->attendance_status = 'present';
                    $mapping->minutes_diff = $clockInTime->diffInMinutes($sessionDateTime);
                } else {
                    $mapping->attendance_status = 'absent';
                }
            } elseif (!$mapping->clock_in_time && $isStarted) {
                $mapping->attendance_status = 'absent';
            } else {
                $mapping->attendance_status = 'upcoming';
            }
        }

        return $mappings;
    }

    /**
     * Get date range based on period type - FIXED
     */
    public function getDateRange($period, $startDate = null, $endDate = null)
    {
        // If custom period and dates are provided, use them directly
        if ($period === 'custom' && $startDate && $endDate) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            return [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
                'start_label' => $start->format('d M Y'),
                'end_label' => $end->format('d M Y')
            ];
        }

        // For non-custom periods, calculate based on the period type
        switch ($period) {
            case 'this_week':
                $start = Carbon::now()->startOfWeek(Carbon::MONDAY);
                $end = Carbon::now()->endOfWeek(Carbon::SUNDAY);
                break;
            case 'last_week':
                $start = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY);
                $end = Carbon::now()->subWeek()->endOfWeek(Carbon::SUNDAY);
                break;
            case 'two_weeks':
                $start = Carbon::now()->subWeeks(2)->startOfWeek(Carbon::MONDAY);
                $end = Carbon::now()->endOfWeek(Carbon::SUNDAY);
                break;
            case 'six_weeks':
                $start = Carbon::now()->subWeeks(6)->startOfWeek(Carbon::MONDAY);
                $end = Carbon::now()->endOfWeek(Carbon::SUNDAY);
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                break;
            case 'last_month':
                $start = Carbon::now()->subMonth()->startOfMonth();
                $end = Carbon::now()->subMonth()->endOfMonth();
                break;
            default:
                $start = Carbon::now()->startOfWeek(Carbon::MONDAY);
                $end = Carbon::now()->endOfWeek(Carbon::SUNDAY);
                break;
        }

        return [
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
            'start_label' => $start->format('d M Y'),
            'end_label' => $end->format('d M Y')
        ];
    }

    protected function getBindings()
    {
        return [];
    }
}
