<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $postIds = $user->posts()->select('id');

        $totalPosts = $user->posts()->count();
        $publishedPosts = $user->posts()->where('status', 'published')->count();
        $totalViews = (int) $user->posts()->sum('views');
        $totalComments = Comment::whereIn('post_id', $postIds)->count();
        $postsThisMonth = $user->posts()
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        $recentPosts = $user->posts()
            ->select(['id', 'title', 'slug', 'status', 'views', 'published_at', 'created_at'])
            ->latest()
            ->limit(5)
            ->get();

        $recentComments = Comment::with([
            'post:id,title,slug',
            'user:id,name',
        ])
            ->whereIn('post_id', $postIds)
            ->latest()
            ->limit(5)
            ->get(['id', 'post_id', 'user_id', 'body', 'created_at']);

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalPosts' => $totalPosts,
                'publishedPosts' => $publishedPosts,
                'draftPosts' => $totalPosts - $publishedPosts,
                'totalViews' => $totalViews,
                'totalComments' => $totalComments,
                'postsThisMonth' => $postsThisMonth,
            ],
            'recentPosts' => $recentPosts,
            'recentComments' => $recentComments,
        ]);
    }
}
