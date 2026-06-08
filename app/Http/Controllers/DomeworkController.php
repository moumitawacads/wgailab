<?php

namespace App\Http\Controllers;

use App\Models\Domework;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\AssignedDomework;
use App\Models\AssignedBusinessPlan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Models\Session;

class DomeworkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Domework::query();

        // search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($innerQuery) use ($request) {
                $innerQuery->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Date From filter
        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        // Date To filter
        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $domeworks = $query
            ->orderBy('id', 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('admin.domework.list', compact('domeworks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Add';
        return view('admin.domework.form', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            // 'description'   => 'string',
            'question'      => 'required|string',
            'media_url' => 'nullable|url',
        ]);

        $mediaType = null;
        if ($request->media_url) {
            $mediaType = $this->detectMediaType($request->media_url);
        }

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
            'question'    => $request->question,
            'media_url' => $request->media_url,
            'media_type' => $mediaType
        ];

        Domework::create($data);
        return redirect()->route('admin.domework')
            ->with('success', 'New domework created successfully!');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Domework $domework, $id)
    {
        $domework = Domework::findOrFail($id);
        $title = 'Edit';
        return view('admin.domework.form', compact('title', 'domework'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Domework $domework)
    {

        $request->validate([
            'title'         => 'required|string|max:255',
            // 'description'   => 'string',
            'question'      => 'required|string',
            'media_url' => 'nullable|url',
        ]);

        if ($request->media_url) {
            $mediaType = $this->detectMediaType($request->media_url);
        } else {
            $mediaType = null;
        }

        $domework->update([
            'title'       => $request->title,
            'description' => $request->description,
            'question'    => $request->question,
            'media_url' => $request->media_url,
            'media_type' => $mediaType
        ]);

        return redirect()->route('admin.domework')
            ->with('success', 'Domework updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Domework $domework)
    {
        $domework->delete();
        return redirect()->back()->with('success', 'Domework deleted successfully!');
    }

    public function saveWorksheet(Request $request)
    {
        $rules = [];

        // Domework validation
        if ($request->has('domeworks')) {

            foreach ($request->domeworks as $key => $value) {

                $rules["domeworks.$key.answer"] = 'required';
            }
        }

        // Businessplan validation
        if ($request->has('businessplans')) {

            foreach ($request->businessplans as $key => $value) {

                $rules["businessplans.$key.answer"] = 'required';
            }
        }

        $request->validate($rules, [
            '*.answer.required' => 'This answer field is required.'
        ]);
        // Save Domeworks
        if ($request->has('domeworks')) {

            foreach ($request->domeworks as $item) {

                AssignedDomework::where('id', $item['id'])
                    ->update([
                        'domework_answer' => $item['answer'],
                        'status' => '1'
                    ]);
            }
        }

        // Save Business Plans
        if ($request->has('businessplans')) {

            foreach ($request->businessplans as $item) {

                AssignedBusinessPlan::where('id', $item['id'])
                    ->update([
                        'businessplan_answer' => $item['answer'],
                        'status' => '1'
                    ]);
            }
        }

        return redirect()->route('se.assigned_domework')
            ->with('success', 'Worksheet saved successfully');
    }

    public function downloadWorksheetPdf($session_id)
    {
        $userId = Auth::id();

        $session_info = Session::findOrFail($session_id);

        $assigned_domework = AssignedDomework::where('session_id', $session_id)
            ->where('user_id', $userId)
            ->with('domework')
            ->get();

        $assigned_businessplan = AssignedBusinessPlan::where('session_id', $session_id)
            ->where('user_id', $userId)
            ->with('businessPlan')
            ->get()
            ->unique('businessplan_id');

        $pdf = Pdf::loadView(
            'student.worksheet_pdf',
            compact(
                'session_info',
                'assigned_domework',
                'assigned_businessplan'
            )
        );

        return $pdf->download('worksheet.pdf');
    }

    public function downloadStudentWorksheetPdf($session_id, $userId)
    {

        $session_info = Session::findOrFail($session_id);

        $assigned_domework = AssignedDomework::where('session_id', $session_id)
            ->where('user_id', $userId)
            ->with('domework')
            ->get();

        $assigned_businessplan = AssignedBusinessPlan::where('session_id', $session_id)
            ->where('user_id', $userId)
            ->with('businessPlan')
            ->get()
            ->unique('businessplan_id');

        $pdf = Pdf::loadView(
            'student.worksheet_pdf',
            compact(
                'session_info',
                'assigned_domework',
                'assigned_businessplan'
            )
        );

        return $pdf->download('worksheet.pdf');
    }

    public function domeWorkAnswerSheet(Request $request)
    {
        $query = AssignedDomework::with([
            'user',
            'session',
            'domework'
        ]);

        // Filter by Session
        if ($request->filled('session_id')) {

            $query->where('session_id', $request->session_id);
        }

        // Filter by User
        if ($request->filled('user_id')) {

            $query->where('user_id', $request->user_id);
        }

        $assignedDomeworks = $query
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Dropdown Data
        $sessions = Session::orderBy('session_name')->get();

        $users = User::where('role', 'se')
            ->orderBy('name')
            ->get();

        return view(
            'admin.domework.answersheet',
            compact(
                'assignedDomeworks',
                'sessions',
                'users'
            )
        );
    }

    private function detectMediaType($url)
    {
        $extension = pathinfo($url, PATHINFO_EXTENSION);
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];

        if (in_array(strtolower($extension), $imageExtensions)) {
            return 'image';
        } elseif (
            strpos($url, 'youtube.com') !== false ||
            strpos($url, 'youtu.be') !== false ||
            strpos($url, 'vimeo.com') !== false ||
            strtolower($extension) == 'mp4'
        ) {
            return 'video';
        }

        return null;
    }
}
