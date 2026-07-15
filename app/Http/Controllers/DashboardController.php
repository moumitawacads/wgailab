<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\UsersClassesMapping;
use App\Models\Attendance;
use App\Models\Checklist;
use App\Models\Session;
use App\Models\WeeklyStipendReport;
use App\Models\User;
use App\Services\AttendanceReportService;
use App\Services\DomeworkReportService;

class DashboardController extends Controller
{
    public function loadadminDashboard()
    {
        $user = auth()->user();
        $students = User::where('role', 'se')->get()->count();
        $instructors = User::where('role', 'instructor')->get()->count();
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek   = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        $ongoingcount = Session::whereHas('schedules', function ($query) use ($startOfWeek, $endOfWeek) {
            $query->whereBetween('schedule_date', [$startOfWeek, $endOfWeek]);
        })->count();

        $startOfNextWeek = Carbon::now()
            ->addWeek()
            ->startOfWeek(Carbon::MONDAY);

        $endOfNextWeek = Carbon::now()
            ->addWeek()
            ->endOfWeek(Carbon::SUNDAY);

        $upcomingcount = Session::whereHas('schedules', function ($query) use ($startOfNextWeek, $endOfNextWeek) {
            $query->whereBetween('schedule_date', [$startOfNextWeek, $endOfNextWeek]);
        })->count();

        if ($user && $user->role == 'workforce_development') {
            $cards = [
                [
                    'title' => 'Street Entrepreneurs',
                    'value' => $students,
                    'icon'  => 'truck'
                ],
                [
                    'title' => 'Instructors',
                    'value' => $instructors,
                    'icon'  => 'users'
                ]
            ];
        } else {
            $cards = [
                [
                    'title' => 'Street Entrepreneurs',
                    'value' => $students,
                    'icon'  => 'truck'
                ],
                [
                    'title' => 'Instructors',
                    'value' => $instructors,
                    'icon'  => 'users'
                ],
                [
                    'title' => 'Current Week Class Count',
                    'value' => $ongoingcount,
                    'icon'  => 'star'
                ],
                [
                    'title' => 'Upcoming Week Class Count',
                    'value' => $upcomingcount,
                    'icon'  => 'star'
                ],
            ];
        }

        // Step 1: Get latest attendance date
        $lastDate = DB::table('attendances')->max('clock_in_time');

        $labels = [];
        $data = [];

        if ($lastDate) {

            // Step 2: Get last week (Mon → Sun)
            $endWeek = Carbon::parse($lastDate)->endOfWeek(Carbon::SUNDAY);

            // Step 3: Go back 5 more weeks (total 6 weeks)
            $startWeek = $endWeek->copy()->subWeeks(5)->startOfWeek(Carbon::MONDAY);

            // Step 4: Loop through each week
            $currentStart = $startWeek->copy();

            while ($currentStart->lte($endWeek)) {

                $currentEnd = $currentStart->copy()->endOfWeek(Carbon::SUNDAY);

                // Step 5: Count attendance in this week
                $count = DB::table('attendances')
                    ->whereBetween('clock_in_time', [$currentStart, $currentEnd])
                    ->count();

                // Step 6: Store label + data
                $labels[] = $currentStart->format('d M') . ' - ' . $currentEnd->format('d M');
                $data[] = $count;

                $currentStart->addWeek();
            }
        } else {
            // No data fallback
            $labels = ['No Data'];
            $data = [0];
        }

        $lastDate = WeeklyStipendReport::max('week_end');

        $stipendlabels = [];
        $stipenddata = [];

        if ($lastDate) {

            $endDate = Carbon::parse($lastDate);
            $startDate = $endDate->copy()->subMonths(3);

            $reports = WeeklyStipendReport::select(
                'week_start',
                'week_end',
                DB::raw('SUM(settled_stipend_amount) as total_stipend')
            )
                ->whereBetween('week_start', [$startDate, $endDate])
                ->groupBy('week_start', 'week_end')
                ->orderBy('week_start')
                ->get();

            foreach ($reports as $report) {
                $stipendlabels[] = Carbon::parse($report->week_start)->format('d M') . ' - ' .
                    Carbon::parse($report->week_end)->format('d M');

                $stipenddata[] = (float) $report->total_stipend;
            }
        } else {
            $stipendlabels = ['No Data'];
            $stipenddata = [0];
        }


        // attendance report
        $service = new AttendanceReportService();
        if (isset($request) && $request->has('period')) {
            $period = $request->get('period', 'this_week');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $dateRange = $service->getDateRange($period, $startDate, $endDate);
        } else {
            $dateRange = $service->getDateRange('this_week');
        }

        $stats = $service->getAttendanceStats($dateRange['start'], $dateRange['end']);


        $attendanceBoxes = [
            [
                'title' => '80%+ Attendance',
                'count' => count($stats['above_80']),
                'icon' => 'award',
                'color' => 'success',
                'category' => 'above_80',
                'users' => $stats['above_80']
            ],
            [
                'title' => '70-80% Attendance',
                'count' => count($stats['between_70_80']),
                'icon' => 'check-circle',
                'color' => 'info',
                'category' => 'between_70_80',
                'users' => $stats['between_70_80']
            ],
            [
                'title' => '60-70% Attendance',
                'count' => count($stats['between_60_70']),
                'icon' => 'alert-circle',
                'color' => 'warning',
                'category' => 'between_60_70',
                'users' => $stats['between_60_70']
            ],
            [
                'title' => 'Below 60% Attendance',
                'count' => count($stats['below_60']),
                'icon' => 'x-circle',
                'color' => 'danger',
                'category' => 'below_60',
                'users' => $stats['below_60']
            ],
        ];

        // domework report
        $domeworkService = new DomeworkReportService();
        $domeworkDateRange = $domeworkService->getDateRange('this_week');
        $domeworkStats = $domeworkService->getDomeworkStats($domeworkDateRange['start'], $domeworkDateRange['end']);

        $domeworkBoxes = [
            [
                'title' => '75%+ Homework Completion',
                'count' => count($domeworkStats['above_75']),
                'icon' => 'award',
                'color' => 'success',
                'category' => 'above_75',
                'users' => $domeworkStats['above_75']
            ],
            [
                'title' => '50%+ Homework Completion',
                'count' => count($domeworkStats['above_50']),
                'icon' => 'check-circle',
                'color' => 'info',
                'category' => 'above_50',
                'users' => $domeworkStats['above_50']
            ],
            [
                'title' => '25%+ Homework Completion',
                'count' => count($domeworkStats['above_25']),
                'icon' => 'alert-circle',
                'color' => 'warning',
                'category' => 'above_25',
                'users' => $domeworkStats['above_25']
            ],
            [
                'title' => 'Below 25% Homework Completion',
                'count' => count($domeworkStats['below_25']),
                'icon' => 'x-circle',
                'color' => 'danger',
                'category' => 'below_25',
                'users' => $domeworkStats['below_25']
            ],
        ];

        return view('admin.dashboard', compact(
            'cards',
            'labels',
            'data',
            'stipenddata',
            'stipendlabels',
            'startOfWeek',
            'endOfWeek',
            'startOfNextWeek',
            'endOfNextWeek',
            'attendanceBoxes',
            'dateRange',
            'domeworkBoxes',
            'domeworkDateRange'
        ));
    }



    public function loadseDashboard()
    {
        $userId = Auth::user()->id;
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek   = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        $ongoingcount = UsersClassesMapping::where('user_id', $userId)
            ->whereBetween('schedule_date', [$startOfWeek, $endOfWeek])
            ->count();

        $startOfNextWeek = Carbon::now()
            ->addWeek()
            ->startOfWeek(Carbon::MONDAY);

        $endOfNextWeek = Carbon::now()
            ->addWeek()
            ->endOfWeek(Carbon::SUNDAY);
        $upcomingcount = UsersClassesMapping::where('user_id', $userId)
            ->whereBetween('schedule_date', [$startOfNextWeek, $endOfNextWeek])
            ->count();

        $upcomingSession = UsersClassesMapping::where('user_id', $userId)
            ->whereBetween('schedule_date', [Carbon::now()->format('Y-m-d'), Carbon::now()->addWeek()->format('Y-m-d')])
            ->where('schedule_time', '>=', Carbon::now()->format('H:i:s'))
            ->with('session')
            ->orderBy('schedule_date', 'asc')
            ->orderBy('schedule_time', 'asc')
            ->first();

        $cards = [
            [
                'title' => 'Current Week Class Count',
                'value' => $ongoingcount,
                'icon'  => 'star'
            ],
            [
                'title' => 'Upcoming Week Class Count',
                'value' => $upcomingcount,
                'icon'  => 'star'
            ],
            [
                'title' => 'Upcoming Session',
                'value' => $upcomingSession,
                'icon'  => 'star'
            ],
        ];

        // Step 1: Get latest attendance date
        $lastDate = DB::table('attendances')->max('clock_in_time');

        $labels = [];
        $data = [];

        if ($lastDate) {

            // Step 2: Get last week (Mon → Sun)
            $endWeek = Carbon::parse($lastDate)->endOfWeek(Carbon::SUNDAY);

            // Step 3: Go back 5 more weeks (total 6 weeks)
            $startWeek = $endWeek->copy()->subWeeks(5)->startOfWeek(Carbon::MONDAY);

            // Step 4: Loop through each week
            $currentStart = $startWeek->copy();

            while ($currentStart->lte($endWeek)) {

                $currentEnd = $currentStart->copy()->endOfWeek(Carbon::SUNDAY);

                // Step 5: Count attendance in this week
                $count = DB::table('attendances')
                    ->where('user_id', $userId)
                    ->whereBetween('clock_in_time', [$currentStart, $currentEnd])
                    ->count();

                // Step 6: Store label + data
                $labels[] = $currentStart->format('d M') . ' - ' . $currentEnd->format('d M');
                $data[] = $count;

                $currentStart->addWeek();
            }
        } else {
            // No data fallback
            $labels = ['No Data'];
            $data = [0];
        }

        $lastDate = WeeklyStipendReport::max('week_end');

        $stipendlabels = [];
        $stipenddata = [];

        if ($lastDate) {

            $endDate = Carbon::parse($lastDate);
            $startDate = $endDate->copy()->subMonths(3);

            $reports = WeeklyStipendReport::select(
                'week_start',
                'week_end',
                DB::raw('SUM(settled_stipend_amount) as total_stipend')
            )
                ->where('user_id', $userId)
                ->whereBetween('week_start', [$startDate, $endDate])
                ->groupBy('week_start', 'week_end')
                ->orderBy('week_start')
                ->get();

            foreach ($reports as $report) {
                $stipendlabels[] = Carbon::parse($report->week_start)->format('d M') . ' - ' .
                    Carbon::parse($report->week_end)->format('d M');

                $stipenddata[] = (float) $report->total_stipend;
            }
        } else {
            $stipendlabels = ['No Data'];
            $stipenddata = [0];
        }

        $user = auth()->user();
        $checklists = Checklist::where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->where('target_type', 'all')
                    ->orWhereHas('users', function ($subq) use ($user) {
                        $subq->where('user_id', $user->id);
                    });
            })
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->filter(function ($checklist) use ($user) {
                // Check if the checklist is completed by this user
                $isCompleted = DB::table('checklist_user')
                    ->where('checklist_id', $checklist->id)
                    ->where('user_id', $user->id)
                    ->value('is_completed') ?? false;

                // Only keep checklists that are NOT completed
                return !$isCompleted;
            })
            ->take(5); // Limit to 5 incomplete items

        return view('student.dashboard', compact('cards', 'labels', 'data', 'stipenddata', 'stipendlabels', 'startOfWeek', 'checklists', 'startOfWeek', 'endOfWeek', 'startOfNextWeek', 'endOfNextWeek'));
    }

    public function loadinstructorDashboard()
    {
        $instructorId = Auth::user()->id;
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $endOfWeek   = Carbon::now()->endOfWeek(Carbon::SUNDAY)->toDateString();

        $count = DB::table('sessions')
            ->join('users_classes_mappings', 'users_classes_mappings.session_id', '=', 'sessions.id')
            ->whereBetween('users_classes_mappings.schedule_date', [$startOfWeek, $endOfWeek])
            ->whereRaw('FIND_IN_SET(?, sessions.instructor_ids)', [$instructorId])
            ->distinct('sessions.id') // avoid duplicates
            ->count('sessions.id');

        $startOfNextWeek = Carbon::now()
            ->addWeek()
            ->startOfWeek(Carbon::MONDAY);

        $endOfNextWeek = Carbon::now()
            ->addWeek()
            ->endOfWeek(Carbon::SUNDAY);
        $next_week_count = DB::table('sessions')
            ->join('users_classes_mappings', 'users_classes_mappings.session_id', '=', 'sessions.id')
            ->whereBetween('users_classes_mappings.schedule_date', [$startOfNextWeek, $endOfNextWeek])
            ->whereRaw('FIND_IN_SET(?, sessions.instructor_ids)', [$instructorId])
            ->distinct('sessions.id') // avoid duplicates
            ->count('sessions.id');

        $cards = [
            [
                'title' => 'Current Week Class Count',
                'value' => $count,
                'icon'  => 'star'
            ],
            [
                'title' => 'Upcoming Week Class Count',
                'value' => $next_week_count,
                'icon'  => 'star'
            ],
        ];
        return view('instructor.instructordashboard', compact('cards', 'startOfWeek', 'endOfWeek', 'startOfNextWeek', 'endOfNextWeek'));
    }

    public function getAttendanceStats(Request $request)
    {
        $service = new AttendanceReportService();

        $period = $request->get('period', 'this_week');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // For custom period, we need to pass the dates directly
        if ($period === 'custom' && $startDate && $endDate) {
            $dateRange = $service->getDateRange('custom', $startDate, $endDate);
        } else {
            $dateRange = $service->getDateRange($period);
        }

        $stats = $service->getAttendanceStats($dateRange['start'], $dateRange['end']);

        $attendanceBoxes = [
            [
                'title' => '80%+ Attendance',
                'count' => count($stats['above_80']),
                'icon' => 'award',
                'color' => 'success',
                'category' => 'above_80',
                'users' => $stats['above_80']
            ],
            [
                'title' => '70-80% Attendance',
                'count' => count($stats['between_70_80']),
                'icon' => 'check-circle',
                'color' => 'info',
                'category' => 'between_70_80',
                'users' => $stats['between_70_80']
            ],
            [
                'title' => '60-70% Attendance',
                'count' => count($stats['between_60_70']),
                'icon' => 'alert-circle',
                'color' => 'warning',
                'category' => 'between_60_70',
                'users' => $stats['between_60_70']
            ],
            [
                'title' => 'Below 60% Attendance',
                'count' => count($stats['below_60']),
                'icon' => 'x-circle',
                'color' => 'danger',
                'category' => 'below_60',
                'users' => $stats['below_60']
            ],
        ];

        // Pass the period parameter to the response
        return response()->json([
            'boxes' => $attendanceBoxes,
            'date_range' => $dateRange,
            'period' => $period
        ]);
    }

    public function getDomeworkStats(Request $request)
    {
        $service = new DomeworkReportService();

        $period = $request->get('period', 'this_week');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // For custom period, we need to pass the dates directly
        if ($period === 'custom' && $startDate && $endDate) {
            $dateRange = $service->getDateRange('custom', $startDate, $endDate);
        } else {
            $dateRange = $service->getDateRange($period);
        }

        $stats = $service->getDomeworkStats($dateRange['start'], $dateRange['end']);

        $domeworkBoxes = [
            [
                'title' => '75%+ Homework Completion',
                'count' => count($stats['above_75']),
                'icon' => 'award',
                'color' => 'success',
                'category' => 'above_75',
                'users' => $stats['above_75']
            ],
            [
                'title' => '50%+ Homework Completion',
                'count' => count($stats['above_50']),
                'icon' => 'check-circle',
                'color' => 'info',
                'category' => 'above_50',
                'users' => $stats['above_50']
            ],
            [
                'title' => '25%+ Homework Completion',
                'count' => count($stats['above_25']),
                'icon' => 'alert-circle',
                'color' => 'warning',
                'category' => 'above_25',
                'users' => $stats['above_25']
            ],
            [
                'title' => 'Below 25% Homework Completion',
                'count' => count($stats['below_25']),
                'icon' => 'x-circle',
                'color' => 'danger',
                'category' => 'below_25',
                'users' => $stats['below_25']
            ],
        ];

        return response()->json([
            'boxes' => $domeworkBoxes,
            'date_range' => $dateRange,
            'period' => $period
        ]);
    }

    public function getDomeworkDetails(Request $request)
    {
        $service = new DomeworkReportService();

        $category = $request->get('category', 'above_75');
        $period = $request->get('period', 'this_week');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Get date range
        if ($period === 'custom' && $startDate && $endDate) {
            $dateRange = $service->getDateRange('custom', $startDate, $endDate);
        } else {
            $dateRange = $service->getDateRange($period);
        }

        // Get all users and their completion rates
        $allUsers = User::where('role', 'se')->get();
        $usersWithRates = [];

        foreach ($allUsers as $user) {
            $details = $service->getUserDomeworkDetails($user->id, $dateRange['start'], $dateRange['end']);

            if ($details['total_assigned'] > 0) {
                $user->completion_rate = $details['completion_rate'];
                $user->total_assigned = $details['total_assigned'];
                $user->completed = $details['completed'];
                $user->pending = $details['pending'];
                $usersWithRates[] = $user;
            }
        }

        // Filter users based on category
        $filteredUsers = array_filter($usersWithRates, function ($user) use ($category) {
            switch ($category) {
                case 'above_75':
                    return $user->completion_rate >= 75;
                case 'above_50':
                    return $user->completion_rate >= 50 && $user->completion_rate < 75;
                case 'above_25':
                    return $user->completion_rate >= 25 && $user->completion_rate < 50;
                case 'below_25':
                    return $user->completion_rate < 25;
                default:
                    return false;
            }
        });

        // Convert to array format for the view
        $userData = [];
        foreach ($filteredUsers as $user) {
            $userData[] = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_phone' => $user->phone,
                'user_city' => $user->city,
                'total_assigned' => $user->total_assigned,
                'completed' => $user->completed,
                'pending' => $user->pending,
                'completion_rate' => $user->completion_rate,
            ];
        }

        // Sort by completion rate descending
        usort($userData, function ($a, $b) {
            return $b['completion_rate'] <=> $a['completion_rate'];
        });

        return view('admin.reports.domework_details', [
            'users' => $userData,
            'category' => $category,
            'dateRange' => $dateRange,
            'period' => $period
        ]);
    }

    public function showUserDomeworkDetails(Request $request)
    {
        $userId = $request->get('user_id');
        $period = $request->get('period', 'this_week');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $service = new DomeworkReportService();

        // Get date range
        if ($period === 'custom' && $startDate && $endDate) {
            $dateRange = $service->getDateRange('custom', $startDate, $endDate);
        } else {
            $dateRange = $service->getDateRange($period);
        }

        // Get user details
        $user = User::find($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Get user domework details
        $details = $service->getUserDomeworkDetails($userId, $dateRange['start'], $dateRange['end']);

        return view('admin.reports.user_domework_details', [
            'user' => $user,
            'details' => $details,
            'dateRange' => $dateRange,
            'period' => $period
        ]);
    }
}
