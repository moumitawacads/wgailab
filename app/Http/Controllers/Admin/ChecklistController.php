<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Checklist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChecklistController extends Controller
{
    public function index()
    {
        $checklists = Checklist::withCount('users')->orderBy('order')->get();
        return view('admin.checklists.index', compact('checklists'));
    }

    public function create()
    {
        $users = User::whereIn('role', ['se', 'student'])->get();
        return view('admin.checklists.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'link' => 'required|url',
            'target_type' => 'required|in:all,selected',
            'selected_users' => 'required_if:target_type,selected|array',
            'selected_users.*' => 'exists:users,id',
            'order' => 'nullable|integer'
        ]);

        DB::beginTransaction();
        try {
            $checklist = Checklist::create([
                'title' => $request->title,
                'description' => $request->description,
                'link' => $request->link,
                'target_type' => $request->target_type,
                'is_active' => true,
                'order' => $request->order ?? 0
            ]);

            if ($request->target_type == 'selected' && $request->has('selected_users')) {
                $users = User::whereIn('id', $request->selected_users)->get();
                $checklist->users()->attach($users->pluck('id')->mapWithKeys(function ($id) {
                    return [$id => ['is_completed' => false]];
                }));
            } else {
                $allParticipants = User::whereIn('role', ['se'])->get();
                $checklist->users()->attach($allParticipants->pluck('id')->mapWithKeys(function ($id) {
                    return [$id => ['is_completed' => false]];
                }));
            }

            DB::commit();
            return redirect()->route('admin.checklists.index')
                ->with('success', 'Checklist item created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create checklist: ' . $e->getMessage());
        }
    }

    public function edit(Checklist $checklist)
    {
        $users = User::whereIn('role', ['se', 'student'])->get();
        $selectedUsers = $checklist->users()->pluck('user_id')->toArray();
        return view('admin.checklists.edit', compact('checklist', 'users', 'selectedUsers'));
    }

    public function update(Request $request, Checklist $checklist)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'target_type' => 'required|in:all,selected',
            'selected_users' => 'required_if:target_type,selected|array',
            'selected_users.*' => 'exists:users,id',
            'order' => 'nullable|integer',
            // 'is_active' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $checklist->update([
                'title' => $request->title,
                'description' => $request->description,
                'link' => $request->link,
                'target_type' => $request->target_type,
                // 'is_active' => $request->has('is_active'),
                'order' => $request->order ?? 0
            ]);

            if ($request->target_type == 'selected') {
                $checklist->users()->sync($request->selected_users);
            } else {
                // $checklist->users()->detach();
                $allParticipants = User::whereIn('role', ['se'])->pluck('id')->toArray();
                $checklist->users()->sync($allParticipants);
            }

            DB::commit();
            return redirect()->route('admin.checklists.index')
                ->with('success', 'Checklist item updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update checklist: ' . $e->getMessage());
        }
    }

    public function destroy(Checklist $checklist)
    {
        $checklist->delete();
        return redirect()->route('admin.checklists.index')
            ->with('success', 'Checklist item deleted successfully!');
    }

    public function reorder(Request $request)
    {
        foreach ($request->orders as $order) {
            Checklist::where('id', $order['id'])->update(['order' => $order['position']]);
        }
        return response()->json(['success' => true]);
    }
}
