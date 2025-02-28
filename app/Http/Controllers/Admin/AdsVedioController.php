<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AdsVedio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdsVedioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $admin = $user;

        // Fetch videos for the determined admin
        $videos = AdsVedio::where('admin_id', $admin->id)->get();

        return view('admin.videos.index', compact('videos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'video_ads' => 'required|mimes:mp4,mkv,avi|max:51200', // Validate video format and size
        ]);

        if ($request->hasFile('video_ads')) {
            $file = $request->file('video_ads');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('assets/img/video_ads'), $filename);

            AdsVedio::create([
                'video_ads' => $filename,
                'admin_id' => auth()->id(), // Assuming admin is authenticated
            ]);
        }

        return redirect()->back()->with('success', 'Video uploaded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
