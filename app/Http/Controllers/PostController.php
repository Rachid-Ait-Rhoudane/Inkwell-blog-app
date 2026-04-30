<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(Request $request): Response
    {
        $posts = $request->user()
            ->posts()
            ->select(['id', 'title', 'slug', 'excerpt', 'status', 'views', 'published_at', 'created_at'])
            ->addSelect([
                'first_image_path' => Media::select('path')
                    ->join('media_post', 'media.id', '=', 'media_post.media_id')
                    ->whereColumn('media_post.post_id', 'posts.id')
                    ->where('media.mime_type', 'like', 'image/%')
                    ->limit(1),
            ])
            ->with('categories:id,name')
            ->when($request->string('status')->isNotEmpty(), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Posts/Index', [
            'posts' => $posts,
            'filters' => $request->only('status'),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Posts/Create', [
            'categories' => $request->user()->categories()->orderBy('name')->get(['id', 'name']),
            'userMedia' => $request->user()->media()->select(['id', 'filename', 'path', 'mime_type', 'size'])->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:posts'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'media_ids' => ['nullable', 'array'],
            'media_ids.*' => ['integer', 'exists:media,id'],
            'media_files' => ['nullable', 'array'],
            'media_files.*' => ['file', 'max:10240'],
        ]);

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        if ($validated['status'] === 'draft') {
            $validated['published_at'] = null;
        }

        $categoryIds = $validated['category_ids'] ?? [];
        $mediaIds = $validated['media_ids'] ?? [];
        unset($validated['category_ids'], $validated['media_ids'], $validated['media_files']);

        $post = $request->user()->posts()->create($validated);
        $post->categories()->sync($categoryIds);

        foreach ($request->file('media_files', []) as $file) {
            $media = $request->user()->media()->create([
                'filename' => $file->getClientOriginalName(),
                'path' => $file->store('media', 'public'),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'disk' => 'public',
            ]);
            $mediaIds[] = $media->id;
        }

        $post->media()->sync($mediaIds);

        return redirect()->route('posts.index')
            ->with('status', 'success')
            ->with('message', 'Post created successfully.');
    }

    public function show(Post $post): Response
    {
        $post->load([
            'user:id,name',
            'categories:id,name,slug',
            'media:id,path,mime_type,filename,size',
            'comments' => fn ($q) => $q->with('user:id,name')->latest(),
        ]);

        return Inertia::render('Posts/Show', [
            'post' => $post,
        ]);
    }

    public function edit(Request $request, Post $post): Response
    {
        abort_if($request->user()->isNot($post->user), 403);

        return Inertia::render('Posts/Edit', [
            'post' => $post->load('categories', 'media'),
            'categories' => $request->user()->categories()->orderBy('name')->get(['id', 'name']),
            'userMedia' => $request->user()->media()->select(['id', 'filename', 'path', 'mime_type', 'size'])->latest()->get(),
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        abort_if($request->user()->isNot($post->user), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('posts')->ignore($post->id)],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'media_ids' => ['nullable', 'array'],
            'media_ids.*' => ['integer', 'exists:media,id'],
            'media_files' => ['nullable', 'array'],
            'media_files.*' => ['file', 'max:10240'],
        ]);

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = $post->published_at ?? now();
        }

        if ($validated['status'] === 'draft') {
            $validated['published_at'] = null;
        }

        $categoryIds = $validated['category_ids'] ?? [];
        $mediaIds = $validated['media_ids'] ?? [];
        unset($validated['category_ids'], $validated['media_ids'], $validated['media_files']);

        $post->update($validated);
        $post->categories()->sync($categoryIds);

        foreach ($request->file('media_files', []) as $file) {
            $media = $request->user()->media()->create([
                'filename' => $file->getClientOriginalName(),
                'path' => $file->store('media', 'public'),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'disk' => 'public',
            ]);
            $mediaIds[] = $media->id;
        }

        $post->media()->sync($mediaIds);

        return redirect()->route('posts.index')
            ->with('status', 'success')
            ->with('message', 'Post updated successfully.');
    }

    public function destroy(Request $request, Post $post): RedirectResponse
    {
        abort_if($request->user()->isNot($post->user), 403);

        $post->delete();

        return redirect()->route('posts.index')
            ->with('status', 'success')
            ->with('message', 'Post deleted successfully.');
    }
}
