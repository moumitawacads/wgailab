<?php


namespace App\Http\Controllers;

use App\Models\AssignedDomework;
use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChecklistController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Collect all valid user Checklists
        $checklistsRaw = Checklist::where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->whereHas('users', function ($subq) use ($user) {
                    $subq->where('user_id', $user->id);
                })->orWhere('target_type', 'all');
            })
            ->orderBy('order')
            ->get();

        $mappedChecklists = $checklistsRaw->map(function ($item) use ($user) {
            $userChecklist = DB::table('checklist_user')
                ->where('checklist_id', $item->id)
                ->where('user_id', $user->id)
                ->first();

            return [
                'id'          => $item->id,
                'type'        => 'checklist',
                'title'       => $item->title,
                'description' => $item->description,
                'link'        => $item->link,
                'date'        => $item->created_at,
                'completed_at' => $userChecklist ? $userChecklist->completed_at : null,
                'is_completed' => $userChecklist && $userChecklist->is_completed ? true : false,
                'complete_url' => route('checklist.complete', $item->id),
                'incomplete_url' => route('checklist.incomplete', $item->id),
            ];
        });

        // 2. Collect all active Domework items
        $domeworksRaw = AssignedDomework::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        $mappedDomeworks = $domeworksRaw->map(function ($item) {
            return [
                'id'          => $item->session->id,
                'type'        => 'domework',
                'title'       => $item->session->session_name,
                'description' => $item->session->session_objectives,
                'link'        => route('se.session.start', $item->session->id), // Fallback link
                'date'        => $item->created_at, // Sort baseline
                'completed_at' => $item->status == "1" ? $item->updated_at : null,
                'is_completed' => $item->status == "1" ? true : false,
                'complete_url' => route('assign.domework.complete', $item->id),
                'incomplete_url' => '#'
            ];
        });

        // Merge arrays into unified Collection and sort date-wise
        $mergedTasks = $mappedChecklists->concat($mappedDomeworks)->sortByDesc('date');

        // Optional filter checking by specific tab statuses
        if ($request->has('status') && in_array($request->status, ['pending', 'completed'])) {
            $mergedTasks = $mergedTasks->filter(function ($task) use ($request) {
                return $request->status == 'completed' ? $task['is_completed'] : !$task['is_completed'];
            });
        }

        // Custom manual LengthAwarePagination simulation for merged collections
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $currentItems = $mergedTasks->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $allTasksPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $mergedTasks->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('student.checklists', ['checklists' => $allTasksPaginated]);
    }

    public function complete(Request $request, Checklist $checklist)
    {
        $user = Auth::user();

        // Check if user is assigned to this checklist
        if ($checklist->target_type == 'selected') {
            $isAssigned = $checklist->users()->where('user_id', $user->id)->exists();
            if (!$isAssigned) {
                return response()->json(['success' => false, 'message' => 'Not authorized'], 403);
            }
        }

        DB::table('checklist_user')->updateOrInsert(
            ['checklist_id' => $checklist->id, 'user_id' => $user->id],
            ['is_completed' => true, 'completed_at' => now(), 'updated_at' => now()]
        );

        return response()->json(['success' => true, 'message' => 'Task completed!']);
    }

    public function incomplete(Request $request, Checklist $checklist)
    {
        $user = Auth::user();

        DB::table('checklist_user')
            ->where('checklist_id', $checklist->id)
            ->where('user_id', $user->id)
            ->update(['is_completed' => false, 'completed_at' => null, 'updated_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Task marked as incomplete']);
    }

    public function assignedDomeworkComplete(Request $request, $assigned_domework_id)
    {
        $user = Auth::user();

        $assignedDomework = AssignedDomework::where('id', $assigned_domework_id)->first();

        if (empty($assignedDomework->domework_answer) || is_null($assignedDomework->domework_answer)) {
            return response()->json(['success' => false, 'message' => "Task can't be marked as complete without an answer!"]);
        }

        $assignedDomework->status = "1";
        $assignedDomework->save();

        return response()->json(['success' => true, 'message' => 'Task completed!']);
    }
}
