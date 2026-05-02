<?php

use App\Models\Post;
use App\Models\User;

test('shows the post listing', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('posts.index'))
        ->assertSuccessful();
});

test('redirects guests away from post listing', function () {
    $this->get(route('posts.index'))
        ->assertRedirect(route('login'));
});

test('only shows the authenticated user\'s own posts', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    Post::factory()->for($user)->count(2)->create();
    Post::factory()->for($other)->count(3)->create();

    $this->actingAs($user)
        ->get(route('posts.index'))
        ->assertInertia(fn ($page) => $page
            ->has('posts.data', 2)
        );
});

test('filters posts by status', function () {
    $user = User::factory()->create();

    Post::factory()->for($user)->count(2)->create(['status' => 'draft']);
    Post::factory()->for($user)->published()->count(3)->create();

    $this->actingAs($user)
        ->get(route('posts.index', ['status' => 'draft']))
        ->assertInertia(fn ($page) => $page
            ->has('posts.data', 2)
        );
});

test('passes status filter to the view', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('posts.index', ['status' => 'published']))
        ->assertInertia(fn ($page) => $page
            ->where('filters.status', 'published')
        );
});

test('paginates posts at 10 per page', function () {
    $user = User::factory()->create();

    Post::factory()->for($user)->count(11)->create();

    $this->actingAs($user)
        ->get(route('posts.index'))
        ->assertInertia(fn ($page) => $page
            ->has('posts.data', 10)
        );
});
