<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class MediaController extends Controller
{
    public function index(Request $request): Response
    {
        $media = $request->user()
            ->media()
            ->select(['id', 'filename', 'path', 'mime_type', 'size', 'created_at'])
            ->latest()
            ->paginate(24)
            ->withQueryString();

        return Inertia::render('Media/Index', [
            'media' => $media,
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['file', 'max:10240'],
        ]);

        foreach ($request->file('files') as $file) {
            $request->user()->media()->create([
                'filename' => $file->getClientOriginalName(),
                'path' => $file->store('media', 'public'),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'disk' => 'public',
            ]);
        }

        return redirect()->route('media.index')
            ->with('status', 'success')
            ->with('message', 'Files uploaded successfully.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(Request $request, Media $medium): RedirectResponse
    {
        abort_if($request->user()->isNot($medium->user), 403);

        Storage::disk($medium->disk)->delete($medium->path);
        $medium->delete();

        return redirect()->route('media.index')
            ->with('status', 'success')
            ->with('message', 'File deleted successfully.');
    }
}
