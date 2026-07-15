<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\AttendanceReportService;
use Carbon\Carbon;

class AttendanceReportController extends Controller
{
    protected $service;

    public function __construct(AttendanceReportService $service)
    {
        $this->service = $service;
    }

    public function details(Request $request)
    {
        $category = $request->get('category', 'all');
        $period = $request->get('period', 'this_week');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Handle custom date range properly
        if ($period === 'custom' && $startDate && $endDate) {
            $dateRange = $this->service->getDateRange('custom', $startDate, $endDate);
        } else {
            $dateRange = $this->service->getDateRange($period);
        }

        $users = $this->service->getDetailedReport(
            $dateRange['start'],
            $dateRange['end'],
            $category
        );

        // Paginate results
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $paginatedUsers = new \Illuminate\Pagination\LengthAwarePaginator(
            $users->slice(($currentPage - 1) * $perPage, $perPage)->values(),
            $users->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $categoryLabels = [
            'above_80' => '80%+ Attendance',
            'between_70_80' => '70-80% Attendance',
            'between_60_70' => '60-70% Attendance',
            'below_60' => 'Below 60% Attendance',
            'all' => 'All Participants'
        ];

        return view('admin.reports.attendance-details', [
            'users' => $paginatedUsers,
            'category' => $category,
            'categoryLabel' => $categoryLabels[$category] ?? 'All Participants',
            'startDate' => $dateRange['start_label'],
            'endDate' => $dateRange['end_label'],
            'period' => $period // Pass period to view for maintaining state
        ]);
    }

    public function userDetails(Request $request, $userId)
    {
        $period = $request->get('period', 'this_week');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $category = $request->get('category', 'all');

        // Handle custom date range properly
        if ($period === 'custom' && $startDate && $endDate) {
            $dateRange = $this->service->getDateRange('custom', $startDate, $endDate);
        } else {
            $dateRange = $this->service->getDateRange($period);
        }

        $user = User::findOrFail($userId);

        // Use the alternative method that processes statuses in PHP
        $sessions = $this->service->getUserAttendanceDetailsAlternative(
            $userId,
            $dateRange['start'],
            $dateRange['end']
        );

        // Calculate attendance percentage based on started sessions only
        $startedSessions = $sessions->filter(function ($session) {
            return $session->session_status === 'started';
        });

        $totalStarted = $startedSessions->count();
        $presentCount = $startedSessions->filter(function ($session) {
            return $session->attendance_status === 'present';
        })->count();
        $absentCount = $startedSessions->filter(function ($session) {
            return $session->attendance_status === 'absent';
        })->count();

        $attendancePercentage = $totalStarted > 0 ? round(($presentCount / $totalStarted) * 100, 2) : 0;

        return view('admin.reports.attendance-user-details', [
            'user' => $user,
            'sessions' => $sessions,
            'totalStarted' => $totalStarted,
            'presentCount' => $presentCount,
            'absentCount' => $absentCount,
            'attendancePercentage' => $attendancePercentage,
            'category' => $category,
            'startDate' => $dateRange['start_label'],
            'endDate' => $dateRange['end_label'],
            'period' => $period // Pass period to view for maintaining state
        ]);
    }
}
