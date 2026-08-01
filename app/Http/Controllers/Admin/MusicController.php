<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Music;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MusicController extends Controller
{
    public function index()
    {
        return view('admin.music', ['musics' => Music::latest()->get()]);
    }

    public function create()
    {
        return view('admin.music-create');
    }

    public function store(Request $request, MediaService $mediaService)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'uploaded_media.cover_image' => 'required|string|exists:media,id',
            'uploaded_media.audio_file' => 'required|string|exists:media,id',
            'spotify_link' => 'nullable|url',
            'youtube_link' => 'nullable|url',
            'description' => 'nullable|string',
            'release_date' => 'nullable|date',
        ]);

        $cover = $mediaService->fromRequest($request, 'cover_image', true);
        $audio = $mediaService->fromRequest($request, 'audio_file', true);

        $music = Music::create([
            'title' => $request->title,
            'artist' => $request->artist,
            'slug' => Str::slug($request->title.'-'.time()),
            'cover_image' => $cover->secure_url,
            'cover_public_id' => $cover->public_id,
            'audio_file' => $audio->secure_url,
            'audio_public_id' => $audio->public_id,
            'spotify_link' => $request->spotify_link,
            'youtube_link' => $request->youtube_link,
            'description' => $request->description,
            'release_date' => $request->release_date,
        ]);

        $mediaService->claim($cover, $music, 'cover_image');
        $mediaService->claim($audio, $music, 'audio_file', 1);

        return redirect()->route('admin.music')->with('success', 'Music uploaded successfully ✨');
    }

    public function edit($id)
    {
        return view('admin.music-edit', ['music' => Music::findOrFail($id)]);
    }

    public function update(Request $request, $id, MediaService $mediaService)
    {
        $music = Music::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'uploaded_media.cover_image' => 'nullable|string|exists:media,id',
            'uploaded_media.audio_file' => 'nullable|string|exists:media,id',
            'spotify_link' => 'nullable|url',
            'youtube_link' => 'nullable|url',
            'release_date' => 'nullable|date',
        ]);

        $cover = $mediaService->fromRequest($request, 'cover_image');
        $audio = $mediaService->fromRequest($request, 'audio_file');
        $oldCover = $music->cover_public_id;
        $oldAudio = $music->audio_public_id;

        $music->update([
            'title' => $request->title,
            'artist' => $request->artist,
            'slug' => $music->title === $request->title ? $music->slug : Str::slug($request->title.'-'.time()),
            'cover_image' => $cover?->secure_url ?? $music->cover_image,
            'cover_public_id' => $cover?->public_id ?? $music->cover_public_id,
            'audio_file' => $audio?->secure_url ?? $music->audio_file,
            'audio_public_id' => $audio?->public_id ?? $music->audio_public_id,
            'spotify_link' => $request->spotify_link,
            'youtube_link' => $request->youtube_link,
            'description' => $request->description,
            'release_date' => $request->release_date,
        ]);

        if ($cover) {
            $mediaService->claim($cover, $music, 'cover_image');
            $mediaService->queueDelete($oldCover);
        }
        if ($audio) {
            $mediaService->claim($audio, $music, 'audio_file', 1);
            $mediaService->queueDelete($oldAudio, 'video');
        }

        return redirect()->route('admin.music')->with('success', 'Music updated ✨');
    }

    public function destroy($id, MediaService $mediaService)
    {
        $music = Music::findOrFail($id);
        $mediaService->queueDelete($music->cover_public_id);
        $mediaService->queueDelete($music->audio_public_id, 'video');
        $music->delete();

        return redirect()->route('admin.music')->with('success', 'Music deleted');
    }

    public function userIndex()
    {
        return view('user.music', ['musics' => Music::latest()->get()]);
    }
}
