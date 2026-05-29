<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\MusicVideo;

use Illuminate\Http\Request;

class MusicVideoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $videos = MusicVideo::latest()->get();

        return view('admin.music-vidio', compact('videos'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.music-vidio-create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            'title' => 'required',

            'artist' => 'required',

            'video_file' => 'required|mimes:mp4,mov,avi,webm|max:512000',

            'thumbnail' => 'nullable|image',

            'description' => 'nullable',

        ]);

        /*
        |--------------------------------------------------------------------------
        | UPLOAD VIDEO
        |--------------------------------------------------------------------------
        */

        $videoPath = null;

        if ($request->hasFile('video_file')) {

            $video = $request->file('video_file');

            $videoName = time() . '_' . $video->getClientOriginalName();

            $video->move(
                public_path('uploads/videos'),
                $videoName
            );

            $videoPath = 'uploads/videos/' . $videoName;
        }

        /*
        |--------------------------------------------------------------------------
        | UPLOAD THUMBNAIL
        |--------------------------------------------------------------------------
        */

        $thumbnailPath = null;

        if ($request->hasFile('thumbnail')) {

            $thumbnail = $request->file('thumbnail');

            $thumbnailName = time() . '_' . $thumbnail->getClientOriginalName();

            $thumbnail->move(
                public_path('uploads/video-thumbnails'),
                $thumbnailName
            );

            $thumbnailPath =
                'uploads/video-thumbnails/' . $thumbnailName;
        }

        /*
        |--------------------------------------------------------------------------
        | SAVE
        |--------------------------------------------------------------------------
        */

        MusicVideo::create([

            'title' => $request->title,

            'artist' => $request->artist,

            'thumbnail' => $thumbnailPath,

            'video_file' => $videoPath,

            'description' => $request->description,

            'is_featured' => $request->is_featured ? true : false,

        ]);

        return redirect('/admin/mv')
            ->with('success', 'Music video uploaded');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(MusicVideo $mv)
    {
        $mv->delete();

        return redirect('/admin/mv');
    }
}