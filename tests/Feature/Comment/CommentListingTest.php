<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

it('shows the comment listing', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('comments.index'))
        ->assertSuccessful();
});

it('redirects guests away from comment listing', function () {
    $this->get(route('comments.index'))
        ->assertRedirect(route('login'));
});

it('only shows the authenticated user\'s own comments', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    Comment::factory()->for($user)->count(2)->create();
    Comment::factory()->for($other)->count(3)->create();

    $this->actingAs($user)
        ->get(route('comments.index'))
        ->assertInertia(fn ($page) => $page
            ->has('comments.data', 2)
        );
});

it('eager loads the post for each comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    Comment::factory()->for($user)->for($post)->create();

    $this->actingAs($user)
        ->get(route('comments.index'))
        ->assertInertia(fn ($page) => $page
            ->has('comments.data.0.post')
            ->whereNot('comments.data.0.post', null)
        );
});

it('paginates comments at 15 per page', function () {
    $user = User::factory()->create();

    Comment::factory()->for($user)->count(16)->create();

    $this->actingAs($user)
        ->get(route('comments.index'))
        ->assertInertia(fn ($page) => $page
            ->has('comments.data', 15)
        );
});
