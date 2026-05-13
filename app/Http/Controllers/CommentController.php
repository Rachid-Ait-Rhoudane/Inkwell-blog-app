<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CommentController extends Controller
{
    public function index(Request $request): Response
    {
        $comments = $request->user()
            ->comments()
            ->select(['id', 'post_id', 'body', 'created_at'])
            ->with('post:id,title,slug')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Comments/Index', [
            'comments' => $comments,
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request, Post $post): RedirectResponse
    {
        $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $post->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->string('body')->trim()->value(),
        ]);

        return redirect()->back()
            ->with('status', 'success')
            ->with('message', 'Comment posted.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Request $request, Comment $comment): Response
    {
        abort_if($request->user()->isNot($comment->user), 403);

        $comment->load('post:id,title,slug');

        return Inertia::render('Comments/Edit', [
            'comment' => $comment,
        ]);
    }

    public function update(Request $request, Comment $comment): RedirectResponse
    {
        abort_if($request->user()->isNot($comment->user), 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $comment->update($validated);

        return redirect()->route('comments.index')
            ->with('status', 'success')
            ->with('message', 'Comment updated successfully.');
    }

    public function destroy(Request $request, Comment $comment): RedirectResponse
    {
        abort_if($request->user()->isNot($comment->user), 403);

        $comment->delete();

        return redirect()->route('comments.index')
            ->with('status', 'success')
            ->with('message', 'Comment deleted successfully.');
    }
}
