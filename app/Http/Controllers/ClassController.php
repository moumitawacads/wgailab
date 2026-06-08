<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Classes;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Session;
use App\Models\WeeklyStipendReport;
use App\Models\UsersClassesMapping;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Exports\WeeklyAttendanceExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\Mail;
use App\Mail\CompensationStatusMail;

use App\Models\Notification;
use App\Models\Domework;
use App\Models\BusinessPlan;
use App\Models\SessionDomeworkBusinessPlan;
use App\Models\AssignedDomework;
use App\Models\AssignedBusinessPlan;
use App\Services\ZoomMeetingService;
use Illuminate\Support\Facades\Log;

class ClassController extends Controller
{
    public function list(Request $request)
    {
        $query = Classes::where('is_deleted', '0');

        // search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($innerQuery) use ($request) {
                $innerQuery->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', (int)$request->status);
        }

        // Date From filter
        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        // Date To filter
        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $classes = $query
            ->orderBy('id', 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('admin.classes.list', compact('classes'));
    }

    public function add()
    {
        $title = 'Add New';
        $users = User::all();
        return view('admin.classes.form', compact('title', 'users'));
    }

    public function edit(Request $request, $id)
    {
        $title = 'Edit';
        $class = Classes::findOrFail($id);
        return view('admin.classes.form', compact('title', 'class'));
    }

    public function create(Request $request)
    {

        $request->validate([
            'name'         => 'required|string|max:255',
            'image'         => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'description'   => 'required|string',
            'status'        => 'required|string',
        ]);
        $data = [
            'name'       => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('classes', 'public');
            $data['image'] = $path;
        }

        Classes::create($data);
        return redirect()->route('admin.classes')
            ->with('success', 'New class created successfully!');
    }


    public function update(Request $request, $id)
    {

        $class = Classes::findOrFail($id);
        $request->validate([
            'name'         => 'required|string|max:255',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            //    'description'   => 'required|string',
            'status'        => 'required|string',
        ]);
        $data = [
            'name'       => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ];

        if ($request->hasFile('image')) {
            if ($class->image) {
                \Storage::disk('public')->delete($class->image);
            }
            $data['image'] = $request->file('image')->store('classes', 'public');
        }

        $class->update($data);
        return redirect()->route('admin.classes')
            ->with('success', 'Class edited successfully!');
    }

    public function destroy($id)
    {
        $success = Classes::findOrFail($id);
        $data = ['is_deleted' => '1'];
        $success->update($data);
        return redirect()->back()->with('success', 'Class deleted successfully!');
    }

    public function manage_schedule()
    {
        $classes = Classes::where('is_deleted', '0')->get();
        $users = User::where('status', '1')->whereIn('role', ['se'])->get();
        $instructor = User::where('status', '1')->whereIn('role', ['instructor'])->get();
        //return view('admin.classes.schedule',compact('users','classes','instructor'));
        $title = "Add New ";
        return view('admin.session.form', compact('users', 'classes', 'instructor', 'title'));
    }

    public function cancel_session(Request $request, $id)
    {

        $request->validate([
            'status' => 'required|in:1,2',
        ]);

        $schedule_id = UsersClassesMapping::where('session_id', $id)->value('id');
        $hasAttendance = Attendance::where('schedule_id', $schedule_id)->exists();

        if ($hasAttendance) {
            return redirect()->back()
                ->with('error', 'Cannot change session status. Attendance already recorded.');
        }

        $session = Session::findOrFail($id);
        $session->update(['status' => $request->status]);

        $mapping = UsersClassesMapping::where('session_id', $session->id)->first();

        $date = $mapping->schedule_date ?? '';
        $time = $mapping->schedule_time ?? '';

        $message = "Session '{$session->session_name}' scheduled on {$date} at {$time} has been cancelled.";
        $participants = $session->participant_ids
            ? explode(',', $session->participant_ids)
            : [];
        $instructors = $session->instructor_ids
            ? explode(',', $session->instructor_ids)
            : [];
        $allUsers = array_unique(array_merge($participants, $instructors));
        foreach ($allUsers as $userId) {
            Notification::create([
                'title' => 'Session Cancelled',
                'message' => $message,
                'user_id' => $userId,
                'icon'    => 'alert-circle',
                'type'    => 'danger',
                'is_read' => 0,
            ]);
        }
        $role = auth()->user()->role == 'superadmin' ? 'admin' : auth()->user()->role;
        return redirect()->route($role . '.schedule_log')
            ->with('success', 'Session cancelled successfully!');
    }

    public function delete_session($id)
    {
        $session = Session::findOrFail($id);

        // Get all mappings for this session
        $scheduleIds = UsersClassesMapping::where('session_id', $id)
            ->pluck('id');

        // Check if attendance exists
        $hasAttendance = Attendance::whereIn('schedule_id', $scheduleIds)
            ->exists();

        if ($hasAttendance) {

            return redirect()->back()
                ->with('error', 'Cannot delete session. Attendance already recorded.');
        }

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Delete Related Records
            |--------------------------------------------------------------------------
            */

            // Delete Zoom meeting if it exists
            if ($session->zoom_meeting_id) {
                $zoomService = new ZoomMeetingService();
                $zoomService->deleteMeeting($session->zoom_meeting_id);
            }

            // Notifications (optional)
            Notification::whereIn(
                'user_id',
                array_merge(
                    explode(',', $session->participant_ids ?? ''),
                    explode(',', $session->instructor_ids ?? '')
                )
            )->where(function ($q) use ($session) {

                $q->where('title', 'New Session Assigned')
                    ->orWhere('title', 'Session Updated')
                    ->orWhere('title', 'Session Cancelled');
            })->delete();

            // Assigned Domeworks
            AssignedDomework::where('session_id', $id)->delete();

            // Assigned Business Plans
            AssignedBusinessPlan::where('session_id', $id)->delete();

            // Session Domework Business Plan links
            SessionDomeworkBusinessPlan::where('session_id', $id)->delete();

            // Users class mappings
            UsersClassesMapping::where('session_id', $id)->delete();

            // Finally delete session
            $session->delete();

            DB::commit();

            $role = auth()->user()->role == 'superadmin'
                ? 'admin'
                : auth()->user()->role;

            return redirect()
                ->route($role . '.schedule_log')
                ->with('success', 'Session deleted successfully.');
        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Something went wrong while deleting session.');
        }
    }

    public function view_session($id)
    {
        $session = Session::with([
            'schedules.mainclass',
            'schedules.user',
            'schedules.creator'
        ])->findOrFail($id);
        return view('admin.session.view', compact('session'));
    }

    public function edit_session($id)
    {
        $session = Session::with([
            'schedules.mainclass',
            'schedules.user',
            'schedules.creator'
        ])->findOrFail($id);
        $classes = Classes::where('is_deleted', '0')->get();
        $users = User::where('status', '1')->whereIn('role', ['se'])->get();
        $instructor = User::where('status', '1')->whereIn('role', ['instructor'])->get();
        $userIds = $session->schedules->pluck('user_id')->toArray();
        $title = "Edit";
        return view('admin.session.form', compact('session', 'title', 'userIds', 'users', 'classes', 'instructor'));
    }


    public function create_session(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'session_name' => 'required|string|max:255',
            'session_order' => 'nullable|string|max:150',
            'session_objectives' => 'nullable|string',
            // 'zoom_link' => 'nullable|url',
            'class_id' => 'required|exists:classes,id',
            'user_id' => 'required|array',
            'user_id.*' => 'exists:users,id',
            'instructor_id' => 'required|array',
            'instructor_id.*' => 'exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
            'session_duration' => 'nullable|numeric',
            'status' => 'required|in:1,2',
        ]);

        DB::beginTransaction();

        try {


            $session = Session::create([
                'session_name' => $request->session_name,
                'session_objectives' => $request->session_objectives,
                'session_order' => $request->session_order,
                // 'zoom_link' => $request->zoom_link,
                'instructor_ids' => implode(',', $request->instructor_id),
                'session_duration' => $request->session_duration,
                'participant_ids' => implode(',', $request->user_id),
                'status' => $request->status,
            ]);

            $dateFormatted = Carbon::parse($request->date)->format('jS F, Y');
            $timeFormatted = Carbon::createFromFormat('H:i A', $request->time)->format('g:i A');
            $message = "A new session '{$request->session_name}' has been scheduled on {$dateFormatted} at {$timeFormatted}.";

            foreach ($request->user_id as $userId) {

                UsersClassesMapping::create([
                    'session_id' => $session->id,
                    'user_id' => $userId,
                    'class_id' => $request->class_id,
                    'instructor_id' => implode(',', $request->instructor_id), // if still CSV
                    'schedule_date' => $request->date,
                    'schedule_time' => Carbon::parse($request->time)->format('H:i:s'), //$request->time,
                    'created_by' => Auth::id(),
                    'status' => 1,
                ]);
                Notification::create([
                    'title' => 'New Session Assigned',
                    'message' => $message,
                    'user_id' => $userId,
                    'is_read' => 0,
                ]);
            }
            foreach ($request->instructor_id as $instructorId) {
                Notification::create([
                    'title' => 'New Session Assigned',
                    'message' => $message,
                    'user_id' => $instructorId,
                    'is_read' => 0,
                ]);
            }

            // Create Zoom meeting and register all users
            $zoomService = new ZoomMeetingService();

            // Step 1: Create the main meeting
            $meeting = $zoomService->createMeeting($session);

            // Update session with Zoom meeting details
            $session->update([
                'zoom_meeting_id' => $meeting['id'],
                'zoom_meeting_password' => $meeting['password'],
                'zoom_meeting_url' => $meeting['join_url'],
                'zoom_registration_url' => $meeting['registration_url'],
                'zoom_start_url' => $meeting['start_url'],
            ]);

            // Step 2: Register each user and get unique join URLs
            $failedRegistrations = [];
            $successCount = 0;

            foreach ($request->user_id as $userId) {
                try {
                    $user = User::find($userId);
                    $registrant = $zoomService->addRegistrant($meeting['id'], $user);

                    if ($registrant) {
                        UsersClassesMapping::where('session_id', $session->id)
                            ->where('user_id', $userId)
                            ->update([
                                'zoom_join_url' => $registrant['join_url'],
                                'registrant_id' => $registrant['id']
                            ]);
                        $successCount++;
                    }

                    // Small delay to avoid rate limits (0.5 second between registrations)
                    usleep(500000);
                } catch (\Exception $e) {
                    $failedRegistrations[] = [
                        'user_id' => $userId,
                        'error' => $e->getMessage()
                    ];
                    Log::error("Failed to register user {$userId}: " . $e->getMessage());
                }
            }

            DB::commit();


            $role = auth()->user()->role == 'superadmin' ? 'admin' : auth()->user()->role;
            return redirect()->route($role . '.schedule_log')
                ->with('success', "New session created successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create session: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create session: ' . $e->getMessage());
        }
    }

    public function update_session(Request $request, $id)
    {
        $request->validate([
            'session_name' => 'required|string|max:255',
            'session_order' => 'nullable|string|max:150',
            'session_objectives' => 'nullable|string',
            // 'zoom_link' => 'nullable|url',
            'class_id' => 'required|exists:classes,id',
            'user_id' => 'required|array',
            'user_id.*' => 'exists:users,id',
            'instructor_id' => 'required|array',
            'instructor_id.*' => 'exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
            'session_duration' => 'nullable|numeric',
            'status' => 'required|in:1,2',
        ]);

        $session = Session::findOrFail($id);

        $oldMappings = UsersClassesMapping::where('session_id', $id)->get();
        $oldUserIds = $oldMappings->pluck('user_id')->toArray();
        $oldInstructorIds = $oldMappings->whereNotNull('instructor_start_url')->pluck('user_id')->toArray();

        // Get one mapping for attendance check
        $firstMapping = $oldMappings->first();


        if ($firstMapping) {
            $hasAttendance = Attendance::where('schedule_id', $firstMapping->id)->exists();

            $restrictedChange =
                $firstMapping->schedule_date != $request->date ||
                $firstMapping->schedule_time != $request->time ||
                $session->status != $request->status;

            if ($restrictedChange && $hasAttendance) {
                return redirect()->route('admin.schedule_log')
                    ->with('error', 'Cannot update schedule/time/status. Attendance already recorded.');
            }
        }

        $oldDate = $firstMapping ? $firstMapping->schedule_date : null;
        $oldTime = $firstMapping ? $firstMapping->schedule_time : null;
        $oldName = $session->session_name;

        $session->update([
            'session_name' => $request->session_name,
            'session_order' => $request->session_order,
            'session_objectives' => $request->session_objectives,
            // 'zoom_link' => $request->zoom_link,
            'instructor_ids' => implode(',', $request->instructor_id),
            'participant_ids' => implode(',', $request->user_id),
            'session_duration' => $request->session_duration,
            'status' => $request->status,
        ]);

        $newUserIds = $request->user_id;
        $newInstructorIds = $request->instructor_id;

        $toAdd = array_diff($newUserIds, $oldUserIds);
        $toRemove = array_diff($oldUserIds, $newUserIds);

        $toAddInstructors = array_diff($newInstructorIds, $oldInstructorIds);
        $toRemoveInstructors = array_diff($oldInstructorIds, $newInstructorIds);

        $zoomService = new ZoomMeetingService();

        // Update existing meeting if details changed
        $meetingChanged = ($oldDate != $request->date) ||
            ($oldTime != $request->time) ||
            ($oldName != $request->session_name);

        if ($meetingChanged && $session->zoom_meeting_id) {
            $zoomService->updateMeeting($session);
        }

        // if zoom meeting id is null for updating so it will generate meeting link
        if (is_null($session->zoom_meeting_id)) {
            // Step 1: Create the main meeting
            $meeting = $zoomService->createMeeting($session);

            // Update session with Zoom meeting details
            $session->update([
                'zoom_meeting_id' => $meeting['id'],
                'zoom_meeting_password' => $meeting['password'],
                'zoom_meeting_url' => $meeting['join_url'],
                'zoom_registration_url' => $meeting['registration_url'],
                'zoom_start_url' => $meeting['start_url'],
            ]);

            // Step 2: Register each user and get unique join URLs
            $failedRegistrations = [];
            $successCount = 0;

            foreach ($request->user_id as $userId) {
                try {
                    $user = User::find($userId);
                    $registrant = $zoomService->addRegistrant($meeting['id'], $user);

                    if ($registrant) {
                        UsersClassesMapping::where('session_id', $session->id)
                            ->where('user_id', $userId)
                            ->update([
                                'zoom_join_url' => $registrant['join_url'],
                                'registrant_id' => $registrant['id']
                            ]);
                        $successCount++;
                    }

                    // Small delay to avoid rate limits (0.5 second between registrations)
                    usleep(500000);
                } catch (\Exception $e) {
                    $failedRegistrations[] = [
                        'user_id' => $userId,
                        'error' => $e->getMessage()
                    ];
                    Log::error("Failed to register user {$userId}: " . $e->getMessage());
                }
            }
        }

        // Cancel registrations for removed users
        foreach ($toRemove as $userId) {
            $mapping = UsersClassesMapping::where('session_id', $id)
                ->where('user_id', $userId)
                ->first();

            if ($mapping && $mapping->registrant_id && $session->zoom_meeting_id) {
                // Verify registrant_id is not the same as meeting_id
                if ($mapping->registrant_id != $session->zoom_meeting_id) {
                    $zoomService->cancelRegistration($session->zoom_meeting_id, $mapping->registrant_id);
                } else {
                    Log::warning("Invalid registrant_id for user {$userId}: Registrant ID equals meeting ID");
                }
            }
        }

        // ========== UPDATE USERS_CLASSES_MAPPINGS TABLE ==========

        // Remove deleted users
        if (!empty($toRemove)) {
            UsersClassesMapping::where('session_id', $id)
                ->whereIn('user_id', $toRemove)
                ->delete();
        }

        // Add only new users
        foreach ($toAdd as $userId) {
            UsersClassesMapping::create([
                'session_id' => $session->id,
                'user_id' => $userId,
                'class_id' => $request->class_id,
                'instructor_id' => implode(',', $request->instructor_id),
                'schedule_date' => $request->date,
                'schedule_time' => Carbon::parse($request->time)->format('H:i:s'), //$request->time,
                'created_by' => Auth::id(),
                'status' => 1,
            ]);

            // Register new user for Zoom meeting
            if ($session->zoom_meeting_id) {
                try {
                    $user = User::find($userId);
                    $registrant = $zoomService->addRegistrant($session->zoom_meeting_id, $user);

                    if ($registrant) {
                        UsersClassesMapping::where('session_id', $session->id)
                            ->where('user_id', $userId)
                            ->update([
                                'zoom_join_url' => $registrant['join_url'],
                                'registrant_id' => $registrant['id']
                            ]);
                    }

                    usleep(500000); // Delay for rate limiting

                } catch (\Exception $e) {
                    Log::error("Failed to register new user {$userId}: " . $e->getMessage());
                }
            }
        }

        if (!empty($newUserIds)) {
            // Update existing users
            UsersClassesMapping::where('session_id', $id)
                ->whereIn('user_id', $newUserIds)
                ->update([
                    'class_id' => $request->class_id,
                    'instructor_id' => implode(',', $request->instructor_id),
                    'schedule_date' => $request->date,
                    'schedule_time' => Carbon::parse($request->time)->format('H:i:s'), //$request->time,
                ]);
        }

        $dateFormatted = Carbon::parse($request->date)->format('jS F, Y');
        $timeFormatted = Carbon::parse($request->time)->format('g:i A');

        $message = "Session '{$request->session_name}' has been updated.";

        $allUsers = array_unique(array_merge($newUserIds, $request->instructor_id));

        $this->assignDomeworkAndBusinessPlan($id);

        foreach ($allUsers as $userId) {
            Notification::create([
                'title' => 'Session Updated',
                'message' => $message,
                'user_id' => $userId,
                'is_read' => 0,
            ]);
        }

        $role = auth()->user()->role == 'superadmin' ? 'admin' : auth()->user()->role;
        return redirect()->route($role . '.schedule_log')
            ->with('success', 'Session updated successfully!');
    }

    public function schedule_log(Request $request)
    {

        $loggedin_role = Auth::user()->role;
        $instructorId = Auth::user()->id;
        $query = Session::with([
            'schedules.mainclass',
            'schedules.user',
            'schedules.creator'
        ])->withExists('sessionLinks');

        $query->has('schedules');

        // search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($innerQuery) use ($request) {
                $innerQuery->where('session_name', 'like', '%' . $request->search . '%')
                    ->orWhere('session_objectives', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by: Class ID
        if ($request->filled('class_id')) {
            $query->whereHas('schedules', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        if ($loggedin_role == 'instructor') {
            $query->where(function ($q) use ($instructorId) {

                // ✅ Case 1: Instructor exists in session (CSV)
                $q->whereRaw('FIND_IN_SET(?, instructor_ids)', [$instructorId])

                    // ✅ OR Case 2: Instructor is creator in schedules table
                    ->orWhereHas('schedules', function ($sub) use ($instructorId) {
                        $sub->where('created_by', $instructorId);
                    });
            });
        }

        // Filter by: Instructor ID
        else {
            if ($request->filled('instructor_id')) {
                $query->whereRaw('FIND_IN_SET(?, instructor_ids)', [$request->instructor_id]);
            }
        }
        // Filter by: User (participant)
        if ($request->filled('user_id')) {
            $query->whereHas('schedules', function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }

        // Filter: Date From
        if ($request->filled('from_date')) {
            $query->whereHas('schedules', function ($q) use ($request) {
                $q->whereDate('schedule_date', '>=', $request->from_date);
            });
        }

        // 🔍 Filter: Date To
        if ($request->filled('to_date')) {
            $query->whereHas('schedules', function ($q) use ($request) {
                $q->whereDate('schedule_date', '<=', $request->to_date);
            });
        }

        // ✅ Fetch sessions
        $sessions = $query
            ->orderBy('id', 'desc')
            ->paginate(25)
            ->withQueryString();

        // ✅ Dropdown data
        $classes = Classes::where('is_deleted', '0')->get();

        $users = User::where('status', '1')
            ->whereIn('role', ['se'])
            ->get();

        $instructor = User::where('status', '1')
            ->whereIn('role', ['instructor'])
            ->get();

        return view('admin.session.schedule_log', compact(
            'sessions',
            'classes',
            'users',
            'instructor'
        ));
        //return view('admin.classes.schedule_log',compact('schedules','classes','users','instructor'));
    }

    public function clockIn(Request $request)
    {
        $request->validate(['schedule_id' => 'required|exists:users_classes_mappings,id']);
        $schedule_id = $request->schedule_id;
        $schedule = UsersClassesMapping::findOrFail($schedule_id);
        $sessionId = $schedule->session_id;
        //dd($sessionId);
        $domeworkInfo = SessionDomeworkBusinessPlan::where('session_id', $sessionId)->get();
        //dd($domeworkInfo);
        $selectedDomework = $domeworkInfo->first()->domework_id ?? null;
        $selectedBusinessPlans = $domeworkInfo->pluck('businessplan_id')->toArray();
        //dd($selectedBusinessPlans);
        $now = Carbon::now();

        $scheduleDateTime = Carbon::parse($schedule->schedule_date . ' ' . $schedule->schedule_time);

        $startWindow = $scheduleDateTime->copy()->subMinutes(15);
        $endWindow = $scheduleDateTime;

        if (!$now->isSameDay($scheduleDateTime)) {
            return response()->json([
                'status' => false,
                'message' => 'You can only clock in on scheduled day!'
            ]);
        }

        if ($now->lt($startWindow)) {
            return response()->json([
                'status' => false,
                'now' => $now,
                'message' => 'Clock-in allowed only 15 minutes before class time!'
            ]);
        }

        $exists = Attendance::where('schedule_id', $schedule->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'Already clocked in!'
            ]);
        }

        Attendance::create([
            'schedule_id'   => $schedule->id,
            'user_id'       => Auth::id(),
            'clock_in_time' => $now
        ]);

        // if (!is_null($selectedDomework)) {
        //     $domeworkData = [
        //         'user_id' => Auth::id(),
        //         'session_id' => $sessionId,
        //         'domework_id' => $selectedDomework,
        //         'domework_answer' => '',
        //         'status' => '0',
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ];
        //     //  Bulk insert (faster)
        //     if (!empty($domeworkData)) {
        //         AssignedDomework::insert($domeworkData);
        //     }
        //     //dd($selectedBusinessPlans);    
        //     foreach ($selectedBusinessPlans as $item) {
        //         $businessPlanData[] = [
        //             'user_id' => Auth::id(),
        //             'session_id' => $sessionId,
        //             'businessplan_id' => $item,
        //             'businessplan_answer' => '',
        //             'status' => '0',
        //             'created_at' => now(),
        //             'updated_at' => now(),
        //         ];
        //     }

        //     if (!empty($businessPlanData)) {
        //         AssignedBusinessPlan::insert($businessPlanData);
        //     }

        //     Notification::create([
        //         'title' => 'Domework Assignment',
        //         'message' => 'New domework has been assigned.',
        //         'user_id' => Auth::id(),
        //         'type' => 'info',
        //         'icon' => 'bell',
        //         'is_read' => 0,
        //     ]);
        // }

        return response()->json([
            'status' => true,
            'message' => 'Clock-in successful!'
        ]);
    }

    public function attendance_record(Request $request)
    {
        $query = DB::table('weekly_stipend_reports')
            ->join('users', 'users.id', '=', 'weekly_stipend_reports.user_id')
            ->select(
                'weekly_stipend_reports.*',
                'users.name as user_name'
            )
            ->orderBy('week_start', 'desc');

        // ✅ Week filter
        if ($request->filled('week_date')) {
            $date = Carbon::parse($request->week_date);

            $startOfWeek = $date->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
            $endOfWeek = $date->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();

            $query->where('week_start', $startOfWeek)
                ->where('week_end', $endOfWeek);
        }

        $weeklyData = $query->paginate(10)->withQueryString();

        return view('admin.classes.attendance_log', compact('weeklyData'));
    }

    public function getScheduleDetails(Request $request)
    {
        $schedules = UsersClassesMapping::with([
            'mainclass',
            'attendances' => function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            }
        ])
            ->where('user_id', $request->user_id)
            ->whereBetween('schedule_date', [$request->start, $request->end])
            ->get();

        return response()->json($schedules);
    }

    public function exportWeeklyReport(Request $request)
    {
        $query = DB::table('weekly_stipend_reports')
            ->join('users', 'users.id', '=', 'weekly_stipend_reports.user_id')
            ->select(
                'weekly_stipend_reports.*',
                'users.name as user_name'
            )
            ->orderBy('week_start', 'desc');

        // ✅ Apply same week filter
        if ($request->filled('week_date')) {
            $date = \Carbon\Carbon::parse($request->week_date);

            $startOfWeek = $date->copy()->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString();
            $endOfWeek = $date->copy()->endOfWeek(\Carbon\Carbon::SUNDAY)->toDateString();

            $query->where('week_start', $startOfWeek)
                ->where('week_end', $endOfWeek);
        }

        $weeklyData = $query->get();

        // ✅ Attach schedules (same logic as getScheduleDetails)
        foreach ($weeklyData as $row) {

            $row->schedules = UsersClassesMapping::with([
                'mainclass',
                'attendances' => function ($q) use ($row) {
                    $q->where('user_id', $row->user_id);
                }
            ])
                ->where('user_id', $row->user_id)
                ->whereBetween('schedule_date', [$row->week_start, $row->week_end])
                ->get();
        }

        return Excel::download(new WeeklyAttendanceExport($weeklyData), 'weekly-attendance.xlsx');
    }


    public function compensation_report()
    {
        $query = DB::table('compensation_requests')
            ->join('weekly_stipend_reports', 'weekly_stipend_reports.id', '=', 'compensation_requests.report_id')
            ->join('users', 'users.id', '=', 'compensation_requests.user_id')

            ->select(
                'compensation_requests.*',
                'weekly_stipend_reports.id as week_id',
                'weekly_stipend_reports.week_start',
                'weekly_stipend_reports.week_end',
                'weekly_stipend_reports.total_classes',
                'weekly_stipend_reports.present_count',
                'weekly_stipend_reports.stipend_amount',
                'weekly_stipend_reports.settled_stipend_amount',
                'weekly_stipend_reports.stipend_payment_status',
                'users.name as user_name'
            )
            ->orderBy('weekly_stipend_reports.week_start', 'desc');

        $weeklyData = $query->paginate(10)->withQueryString();

        return view('admin.compensation_log', compact('weeklyData'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:compensation_requests,id',
            'status' => 'required|in:1,2',
            'week_id' => 'required',
        ]);

        $requestData = DB::table('compensation_requests')
            ->where('id', $request->id)
            ->first();

        DB::table('compensation_requests')
            ->where('id', $request->id)
            ->update([
                'status' => $request->status,
                'updated_at' => now()
            ]);

        if ($request->status == 1) {
            $altered_stipend = admin_settings('stipend_amount') ?? 0;
            $stipendR = WeeklyStipendReport::findOrFail($request->week_id);
            $stipendR->update([
                'adjusted_stipend_amount' => $altered_stipend,
                'settled_stipend_amount' => $altered_stipend,
            ]);
        }

        // Fetch user & report
        $user = User::find($requestData->user_id);
        $report = WeeklyStipendReport::find($requestData->report_id);
        $type = 'success';
        $icon = 'check-circle';
        $msg_status = $request->status == 1 ? 'Approved' : 'Rejected';
        if ($request->status == 2) {
            $type = 'danger';
            $icon = 'x-circle';
        }
        Notification::create([
            'title' => 'Compensation Request Update',
            'message' => 'Your Compensation request for ' . $report->week_start . ' - ' . $report->week_end . ' has been ' . $msg_status,
            'user_id' => $requestData->user_id,
            'type' => $type,
            'icon' => $icon,
            'is_read' => 0,
        ]);


        // Prepare email data
        $mailData = [
            'name' => $user->name,
            'status' => $request->status == 1 ? 'Approved' : 'Rejected',
            'week_start' => $report->week_start,
            'week_end' => $report->week_end,
            'total_classes' => $report->total_classes,
            'present_count' => $report->present_count,
            'notes' => $requestData->notes ?? null
        ];

        // Send Mail
        Mail::to($user->email)->send(new CompensationStatusMail($mailData));

        return response()->json([
            'success' => true,
            'message' => 'Status updated and email sent to the student successfully.'
        ]);
    }

    public function manage_domework($id)
    {
        $session = Session::findOrFail($id);

        $domeworks = Domework::get();
        $businessplans = BusinessPlan::get();
        $existing = SessionDomeworkBusinessPlan::where('session_id', $session->id)->get();
        $selectedDomework = $existing->first()->domework_id ?? null;
        $selectedBusinessPlans = $existing->pluck('businessplan_id')->toArray();
        $role = auth()->user()->role == 'superadmin' ? 'admin' : auth()->user()->role;



        return view('admin.session.manage_domework', compact('session', 'domeworks', 'businessplans', 'selectedDomework', 'selectedBusinessPlans'));
    }

    protected function assignDomeworkAndBusinessPlan($sessionId)
    {
        $session = Session::findOrFail($sessionId);

        /*
        |--------------------------------------------------------------------------
        | Current session participants
        |--------------------------------------------------------------------------
        */

        $participantIds = collect(
            explode(',', $session->participant_ids)
        )
            ->filter()
            ->map(fn($id) => (int)$id)
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | Remove assignments of removed participants
        |--------------------------------------------------------------------------
        */

        AssignedDomework::where(
            'session_id',
            $sessionId
        )
            ->whereNotIn(
                'user_id',
                $participantIds
            )
            ->delete();


        AssignedBusinessPlan::where(
            'session_id',
            $sessionId
        )
            ->whereNotIn(
                'user_id',
                $participantIds
            )
            ->delete();


        /*
        |--------------------------------------------------------------------------
        | Fetch assignment config
        |--------------------------------------------------------------------------
        */

        $domeworkInfo =
            SessionDomeworkBusinessPlan::where(
                'session_id',
                $sessionId
            )->get();


        $selectedDomework =
            $domeworkInfo->first()->domework_id ?? null;


        $selectedBusinessPlans =
            $domeworkInfo
            ->pluck('businessplan_id')
            ->filter()
            ->unique()
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | Sync each participant
        |--------------------------------------------------------------------------
        */

        foreach ($participantIds as $userId) {

            /*
            |--------------------------------------------------------------------------
            | Domework Sync
            |--------------------------------------------------------------------------
            */

            $existingDomework =
                AssignedDomework::where([
                    'user_id' => $userId,
                    'session_id' => $sessionId
                ])->first();


            if ($selectedDomework) {

                if (!$existingDomework) {

                    AssignedDomework::create([
                        'user_id' => $userId,
                        'session_id' => $sessionId,
                        'domework_id' => $selectedDomework,
                        'domework_answer' => '',
                        'status' => '0'
                    ]);
                } elseif (
                    $existingDomework->domework_id !=
                    $selectedDomework
                ) {

                    $existingDomework->update([
                        'domework_id' => $selectedDomework,
                        'domework_answer' => '',
                        'status' => '0'
                    ]);
                }
            } else {

                AssignedDomework::where([
                    'user_id' => $userId,
                    'session_id' => $sessionId
                ])->delete();
            }


            /*
            |--------------------------------------------------------------------------
            | Business Plan Sync
            |--------------------------------------------------------------------------
            */

            $existingPlans =
                AssignedBusinessPlan::where([
                    'user_id' => $userId,
                    'session_id' => $sessionId
                ])
                ->pluck('businessplan_id')
                ->toArray();


            $plansToInsert =
                array_diff(
                    $selectedBusinessPlans,
                    $existingPlans
                );


            $plansToDelete =
                array_diff(
                    $existingPlans,
                    $selectedBusinessPlans
                );


            if (!empty($plansToDelete)) {

                AssignedBusinessPlan::where([
                    'user_id' => $userId,
                    'session_id' => $sessionId
                ])
                    ->whereIn(
                        'businessplan_id',
                        $plansToDelete
                    )
                    ->delete();
            }


            if (!empty($plansToInsert)) {

                $insertData = [];

                foreach ($plansToInsert as $plan) {

                    $insertData[] = [
                        'user_id' => $userId,
                        'session_id' => $sessionId,
                        'businessplan_id' => $plan,
                        'businessplan_answer' => '',
                        'status' => '0',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                AssignedBusinessPlan::insert(
                    $insertData
                );
            }


            Notification::create([
                'title' => 'Domework Assignment',
                'message' => 'Assignments updated.',
                'user_id' => $userId,
                'type' => 'info',
                'icon' => 'bell',
                'is_read' => '0'
            ]);
        }
    }

    public function update_domework_assignment(Request $request, $id)
    {

        $session = Session::findOrFail($id);

        $schedule_id = UsersClassesMapping::where('session_id', $id)->value('id');
        $hasAttendance = Attendance::where('schedule_id', $schedule_id)->exists();

        if ($hasAttendance) {
            return redirect()->route('admin.schedule_log')
                ->with('error', 'Cannot change session status. Attendance already recorded.');
        }

        $request->validate([
            'session_id'     => 'required|exists:sessions,id',
            'domeworks'      => 'required|exists:domeworks,id',
            'businessplans'  => 'required|array|min:1',
            'businessplans.*' => 'exists:business_plans,id',
        ]);

        $sessionId = $request->session_id;
        $domeworkId = $request->domeworks;
        $businessplans = $request->businessplans;

        // 🔥 Delete old records
        SessionDomeworkBusinessPlan::where('session_id', $sessionId)->delete();

        // 🔁 Insert new
        $data = [];

        foreach ($businessplans as $businessplanId) {
            $data[] = [
                'session_id'      => $sessionId,
                'domework_id'     => $domeworkId,
                'businessplan_id' => $businessplanId,
                'created_at'      => now(),
                'updated_at'      => now(),
            ];
        }

        SessionDomeworkBusinessPlan::insert($data);
        $this->assignDomeworkAndBusinessPlan($sessionId);
        $role = auth()->user()->role == 'superadmin' ? 'admin' : auth()->user()->role;
        return redirect()->route($role . '.schedule_log')->with('success', 'Assignment updated successfully!');
    }


    public function getInstructorDomeworks(Request $request)
    {
        $userId = auth()->user()->id;

        $query = Session::whereRaw('FIND_IN_SET(?, instructor_ids)', [$userId])->whereHas('domeworkAssignments', function ($q) {
            $q->whereNotNull('domework_id')
                ->whereNotNull('businessplan_id');
        });

        // search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where('session_name', 'like', '%' . $request->search . '%');
        }

        // Date From filter
        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereHas('schedules', function ($q) use ($request) {
                $q->whereDate('schedule_date', '>=', $request->from_date);
            });
        }

        // Date To filter
        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereHas('schedules', function ($q) use ($request) {
                $q->whereDate('schedule_date', '<=', $request->to_date);
            });
        }

        $sessions = $query->orderBy('created_at', 'desc')->get();

        return view('instructor.domeworks', compact('sessions'));
    }

    public function viewDomework($session_id)
    {
        $userId = Auth::id();

        $session_info = Session::find($session_id);

        $assigned_domework = SessionDomeworkBusinessPlan::where('session_id', $session_id)->first();

        $assigned_businessplans = SessionDomeworkBusinessPlan::where('session_id', $session_id)->get();

        return view('instructor.view_domework', compact('session_info', 'assigned_domework', 'assigned_businessplans'));
    }

    public function updatePaymentStatus(Request $request, $week_id)
    {
        $request->validate([
            'payment_status'  => 'required',
        ]);

        $weeklyStipendReport = WeeklyStipendReport::find($week_id);
        $weeklyStipendReport->stipend_payment_status = $request->payment_status ? 1 : 0;
        $weeklyStipendReport->save();

        return redirect()->back()->with(['success' => 'Payment status updated successfully!']);
    }
}
