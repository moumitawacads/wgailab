<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResourceLibrary;

class ResourceLibraryController extends Controller
{

    public function index(Request $request)
    {
        $resources = ResourceLibrary::query();

        if ($request->filled('search')) {

            $resources->where(
                'media_title',
                'like',
                '%' . $request->search . '%'
            );
        }

        $resources = $resources
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.resource_library.list',
            compact('resources')
        );
    }



    public function create()
    {
        $title="Resource Library";
        return view(
            'admin.resource_library.form',
            compact('title')
        );
    }



    public function store(
        Request $request
    )
    {

        $request->validate([

            'media_link' =>
                'required|url|max:255',
            'media_title' =>
                'required|string',   

        ]);


        ResourceLibrary::create([

            'media_link' =>$request->media_link,
            'media_title' =>$request->media_title

        ]);


        return redirect()
            ->route(
                'admin.resource_library'
            )
            ->with(
                'success',
                'Resource created successfully.'
            );
    }



    public function edit(ResourceLibrary $resourceLibrary)
    {
        $title = "Edit";

        return view(
            'admin.resource_library.form',
            compact(
                'resourceLibrary',
                'title'
            )
        );
    }



   public function update(Request $request,ResourceLibrary $resourceLibrary)
    {
        $request->validate([

            'media_title' =>
                'required|string|max:255',

            'media_link' =>
                'required|url|max:255'

        ]);


        $resourceLibrary->update([

            'media_title' =>
                $request->media_title,

            'media_link' =>
                $request->media_link

        ]);


        return redirect()
            ->route(
                'admin.resource_library'
            )
            ->with(
                'success',
                'Resource updated successfully.'
            );
    }



    public function destroy(
        ResourceLibrary $resourceLibrary
    )
    {

        $resourceLibrary->delete();


        return back()->with(
            'success',
            'Resource deleted successfully.'
        );
    }

}