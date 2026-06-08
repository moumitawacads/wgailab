<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\UsersClassesMapping;
use App\Models\Attendance;
use App\Models\Session;
use App\Models\AssignedDomework;
use App\Models\AssignedBusinessPlan;
use App\Models\ResourceLibrary;
use Illuminate\Support\Facades\DB;


class SDashboardController extends Controller
{
     public function loadAttendance(Request $request){
        $query = DB::table('weekly_stipend_reports')
        ->join('users', 'users.id', '=', 'weekly_stipend_reports.user_id')

        ->leftJoin('compensation_requests', function ($join) {
            $join->on('compensation_requests.report_id', '=', 'weekly_stipend_reports.id')
                ->on('compensation_requests.user_id', '=', 'weekly_stipend_reports.user_id');
        })

        ->select(
            'weekly_stipend_reports.*',
            'users.name as user_name',
            'compensation_requests.status as compensation_status',
            'compensation_requests.id as compensation_id'
        )

        ->where('weekly_stipend_reports.user_id', auth()->id())
        ->orderBy('week_start', 'desc');

        $weeklyData = $query->paginate(10)->withQueryString();

        return view('student.attendance_log', compact('weeklyData'));
    }

    public function storeCompensation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'report_id' => 'required|exists:weekly_stipend_reports,id',
            'week_start' => 'required|date',
            'week_end' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // ✅ Prevent duplicate request
        $exists = DB::table('compensation_requests')
            ->where('user_id', $request->user_id)
            ->where('report_id', $request->report_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted a request for this week.'
            ]);
        }

        DB::table('compensation_requests')->insert([
            'user_id' => $request->user_id,
            'report_id' => $request->report_id,
            'notes' => $request->notes,
            'status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Compensation request submitted successfully'
        ]);
    }

    public function upcoming_schedules(){
        $userId = Auth::user()->id;
        $upcomingClasses = UsersClassesMapping::with([
            'mainclass','session',
            'attendances'=> function ($q) use ($userId) {
            $q->where('user_id', $userId);
            }
            ])
            ->where('user_id', $userId)
            ->whereHas('session', function ($q) {
                $q->where('status', '1');
            })
            ->whereDate('schedule_date', '>=', Carbon::today())
            ->orderBy('schedule_date', 'asc')
            ->orderBy('schedule_time', 'asc')
            ->get();
        return view('student.upcoming_schedules',compact('upcomingClasses'));
    }

    public function assigned_domework(){

        $userId = Auth::user()->id;

        $sessions = Session::where(function ($query) use ($userId) {
            $query->whereHas('assignedDomeworks', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->orWhereHas('assignedBusinessPlans', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });

        })
        ->with([
            'assignedDomeworks' => function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->with('domework');
            },
            'assignedBusinessPlans' => function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->with('businessPlan');
            }
        ])
        ->latest()->get();
                return view('student.assigned_domework',compact('sessions'));
    }

    public function start_session($session_id){
        $userId = Auth::id();
        $session_info=Session::find($session_id);
        $assigned_domework = AssignedDomework::where('session_id', $session_id)
        ->where('user_id', $userId)
        ->with('domework')
        ->get();
       

        /** Get all unique sessions for multiple business */
        // $assigned_businessplan = AssignedBusinessPlan::where('session_id', $session_id)
        //     ->where('user_id', $userId)
        //     ->with('businessPlan', 'relatedSessions.session')
        //     ->get()
        //     ->unique('businessplan_id');

        $assigned_businessplan = AssignedBusinessPlan::where('session_id', $session_id)
        ->where('user_id', $userId)
        ->with('businessPlan')
        ->get();

        return view('student.domework',compact('session_info','assigned_domework','assigned_businessplan'));
    }

    public function getresourcelibrary(Request $request){
         $resources = ResourceLibrary::query();

    if ($request->filled('search')) {

        $resources->where(
            'media_title',
            'like',
            '%'.$request->search.'%'
        );
    }

    $resources = $resources
        ->latest()
        ->paginate(12)
        ->withQueryString();

    return view(
        'student.resource_library',
        compact('resources')
    );

    }

}
