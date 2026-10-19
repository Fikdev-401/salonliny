<?php

namespace App\Http\Controllers;

use App\Models\LandingVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingVideoController extends Controller
{
    public function index()
    {
        $videos = LandingVideo::orderBy('urutan')->orderBy('id', 'desc')->get();
        return view('landing-admin.videos.index', compact('videos'));
    }

    public function create()
    {
        return view('landing-admin.videos.form', ['video' => new LandingVideo()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe' => 'required|in:youtube,upload',
            'youtube_url' => 'nullable|required_if:tipe,youtube|string|max:255',
            'video_file' => 'nullable|required_if:tipe,upload|file|mimes:mp4,webm,ogg|max:51200',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'urutan' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        // Extract YouTube ID
        $validated['youtube_id'] = null;
        if ($validated['tipe'] === 'youtube' && !empty($validated['youtube_url'])) {
            $validated['youtube_id'] = LandingVideo::extractYoutubeId($validated['youtube_url']);
            if (!$validated['youtube_id']) {
                return back()->withInput()->with('error', 'URL YouTube tidak valid.');
            }
        }
        unset($validated['youtube_url']);

        // Upload video file
        if ($request->hasFile('video_file')) {
            $file = $request->file('video_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('landing/videos', $filename, 'public');
            $validated['video_file'] = 'landing/videos/' . $filename;
        }

        // Upload thumbnail
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = 'thumb_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('landing/videos', $filename, 'public');
            $validated['thumbnail'] = 'landing/videos/' . $filename;
        }

        $validated['urutan'] = $validated['urutan'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active', true);

        LandingVideo::create($validated);

        return redirect()->route('landing-admin.videos.index')
            ->with('success', 'Video berhasil ditambahkan.');
    }

    public function edit(LandingVideo $video)
    {
        return view('landing-admin.videos.form', compact('video'));
    }

    public function update(Request $request, LandingVideo $video)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe' => 'required|in:youtube,upload',
            'youtube_url' => 'nullable|required_if:tipe,youtube|string|max:255',
            'video_file' => 'nullable|file|mimes:mp4,webm,ogg|max:51200',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'urutan' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validated['tipe'] === 'youtube') {
            if (!empty($validated['youtube_url'])) {
                $validated['youtube_id'] = LandingVideo::extractYoutubeId($validated['youtube_url']);
                if (!$validated['youtube_id']) {
                    return back()->withInput()->with('error', 'URL YouTube tidak valid.');
                }
            }
        } else {
            // Switched to upload: clear youtube_id
            $validated['youtube_id'] = null;
        }
        unset($validated['youtube_url']);

        // Replace video file
        if ($request->hasFile('video_file')) {
            $this->deleteStorageFile($video->video_file);
            $file = $request->file('video_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('landing/videos', $filename, 'public');
            $validated['video_file'] = 'landing/videos/' . $filename;
        }

        // Replace thumbnail
        if ($request->hasFile('thumbnail')) {
            // Don't auto-delete YouTube auto-thumb
            if ($video->thumbnail && $request->hasFile('thumbnail')) {
                $this->deleteStorageFile($video->thumbnail);
            }
            $file = $request->file('thumbnail');
            $filename = 'thumb_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('landing/videos', $filename, 'public');
            $validated['thumbnail'] = 'landing/videos/' . $filename;
        }

        $validated['is_active'] = $request->boolean('is_active', false);

        $video->update($validated);

        return redirect()->route('landing-admin.videos.index')
            ->with('success', 'Video berhasil diperbarui.');
    }

    public function destroy(LandingVideo $video)
    {
        $this->deleteStorageFile($video->video_file);
        $this->deleteStorageFile($video->thumbnail);
        $video->delete();

        return redirect()->route('landing-admin.videos.index')
            ->with('success', 'Video berhasil dihapus.');
    }

    private function deleteStorageFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
