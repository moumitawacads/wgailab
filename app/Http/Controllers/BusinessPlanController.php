<?php

namespace App\Http\Controllers;

use App\Models\BusinessPlan;
use Illuminate\Http\Request;

class BusinessPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BusinessPlan::query();

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

        $business = $query
            ->orderBy('id', 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('admin.businessplan.list', compact('business'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Add';
        return view('admin.businessplan.form', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'media_url' => 'nullable|url',
        ]);

        $mediaType = null;
        if ($request->media_url) {
            $mediaType = $this->detectMediaType($request->media_url);
        }

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
            'media_url' => $request->media_url,
            'media_type' => $mediaType
        ];

        BusinessPlan::create($data);

        return redirect()->route('admin.businessplan')
            ->with('success', 'New entry created successfully!');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BusinessPlan $businessPlan)
    {
        $title = 'Edit';
        $business = $businessPlan;
        return view('admin.businessplan.form', compact('title', 'business'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BusinessPlan $businessPlan)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'media_url' => 'nullable|url',
        ]);

        if ($request->media_url) {
            $mediaType = $this->detectMediaType($request->media_url);
        } else {
            $mediaType = null;
        }

        $businessPlan->update([
            'title'       => $request->title,
            'description' => $request->description,
            'media_url' => $request->media_url,
            'media_type' => $mediaType
        ]);

        return redirect()->route('admin.businessplan')
            ->with('success', 'Business plan updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusinessPlan $businessPlan)
    {
        $businessPlan->delete();
        return redirect()->back()->with('success', 'Deleted successfully!');
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
