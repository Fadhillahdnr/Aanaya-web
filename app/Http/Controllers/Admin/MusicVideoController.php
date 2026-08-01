<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MusicVideo;
use App\Services\MediaService;
use Illuminate\Http\Request;

class MusicVideoController extends Controller
{
    public function index()
    {
        return view('admin.music-vidio', ['videos' => MusicVideo::latest()->get()]);
    }

    public function create()
    {
        return view('admin.music-vidio-create');
    }

    public function store(Request $request, MediaService $mediaService)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'uploaded_media.video_file' => 'required|string|exists:media,id',
            'uploaded_media.thumbnail' => 'nullable|string|exists:media,id',
            'description' => 'nullable|string',
        ]);

        $videoMedia = $mediaService->fromRequest($request, 'video_file', true);
        $thumbnail = $mediaService->fromRequest($request, 'thumbnail');
        $video = MusicVideo::create([
            'title' => $request->title,
            'artist' => $request->artist,
            'thumbnail' => $thumbnail?->secure_url,
            'thumbnail_public_id' => $thumbnail?->public_id,
            'video_file' => $videoMedia->secure_url,
            'video_public_id' => $videoMedia->public_id,
            'description' => $request->description,
            'is_featured' => $request->boolean('is_featured'),
        ]);

        $mediaService->claim($videoMedia, $video, 'video_file');
        if ($thumbnail) $mediaService->claim($thumbnail, $video, 'thumbnail', 1);

        return redirect('/admin/mv')->with('success', 'Music video uploaded');
    }

    public function edit(MusicVideo $mv)
    {
        return view('admin.music-vidio-edit', ['video' => $mv]);
    }

    public function update(Request $request, MusicVideo $mv, MediaService $mediaService)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'uploaded_media.video_file' => 'nullable|string|exists:media,id',
            'uploaded_media.thumbnail' => 'nullable|string|exists:media,id',
            'description' => 'nullable|string',
        ]);
        $videoMedia = $mediaService->fromRequest($request, 'video_file');
        $thumbnail = $mediaService->fromRequest($request, 'thumbnail');
        $oldVideo = $mv->video_public_id;
        $oldThumbnail = $mv->thumbnail_public_id;

        $mv->update([
            'title' => $request->title,
            'artist' => $request->artist,
            'description' => $request->description,
            'is_featured' => $request->boolean('is_featured'),
            'thumbnail' => $thumbnail?->secure_url ?? $mv->thumbnail,
            'thumbnail_public_id' => $thumbnail?->public_id ?? $mv->thumbnail_public_id,
            'video_file' => $videoMedia?->secure_url ?? $mv->video_file,
            'video_public_id' => $videoMedia?->public_id ?? $mv->video_public_id,
        ]);

        if ($thumbnail) {
            $mediaService->claim($thumbnail, $mv, 'thumbnail', 1);
            $mediaService->queueDelete($oldThumbnail);
        }
        if ($videoMedia) {
            $mediaService->claim($videoMedia, $mv, 'video_file');
            $mediaService->queueDelete($oldVideo, 'video');
        }

        return redirect('/admin/mv')->with('success', 'Music video updated successfully ✨');
    }

    public function destroy(MusicVideo $mv, MediaService $mediaService)
    {
        $mediaService->queueDelete($mv->video_public_id, 'video');
        $mediaService->queueDelete($mv->thumbnail_public_id);
        $mv->delete();

        return redirect('/admin/mv')->with('success', 'Music video deleted');
    }
}
