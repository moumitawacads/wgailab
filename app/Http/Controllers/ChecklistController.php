<?php


namespace App\Http\Controllers;

use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChecklistController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Checklist::where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->whereHas('users', function ($subq) use ($user) {
                    $subq->where('user_id', $user->id);
                })
                    ->orWhere('target_type', 'all');
            })
            ->orderBy('order');

        // Optional: Add filtering by status
        if ($request->has('status') && in_array($request->status, ['pending', 'completed'])) {
            $checklists = $query->get();

            // Filter manually based on completion status
            $checklists = $checklists->filter(function ($checklist) use ($user, $request) {
                $isCompleted = DB::table('checklist_user')
                    ->where('checklist_id', $checklist->id)
                    ->where('user_id', $user->id)
                    ->value('is_completed') ?? false;

                return $request->status == 'completed' ? $isCompleted : !$isCompleted;
            });

            $checklists = $checklists->paginate(20);
        } else {
            $checklists = $query->paginate(20);
        }

        return view('student.checklists', compact('checklists'));
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
}
