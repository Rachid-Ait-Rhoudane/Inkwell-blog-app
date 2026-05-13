<?php

use App\Models\Comment;
use App\Models\User;

test('shows the edit page', function () {
    $user = User::factory()->create();
    $comment = Comment::factory()->for($user)->create();

    $this->actingAs($user)
        ->get("/comments/{$comment->id}/edit")
        ->assertSuccessful();
});

test('prevents a non-owner from viewing the edit page', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $comment = Comment::factory()->for($owner)->create();

    $this->actingAs($other)
        ->get("/comments/{$comment->id}/edit")
        ->assertForbidden();
});

test('redirects guests away from the edit page', function () {
    $comment = Comment::factory()->create();

    $this->get("/comments/{$comment->id}/edit")
        ->assertRedirect(route('login'));
});

test('eager loads the post on the edit page', function () {
    $user = User::factory()->create();
    $comment = Comment::factory()->for($user)->create();

    $this->actingAs($user)
        ->get("/comments/{$comment->id}/edit")
        ->assertInertia(fn ($page) => $page
            ->has('comment.post')
        );
});

test('updates a comment', function () {
    $user = User::factory()->create();
    $comment = Comment::factory()->for($user)->create();

    $this->actingAs($user)
        ->from("/comments/{$comment->id}/edit")
        ->put("/comments/{$comment->id}", [
            'body' => 'Updated body text.',
        ])
        ->assertRedirect(route('comments.index'))
        ->assertSessionHas('status', 'success');

    expect($comment->refresh()->body)->toBe('Updated body text.');
});

test('prevents a non-owner from updating a comment', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $comment = Comment::factory()->for($owner)->create();

    $this->actingAs($other)
        ->put("/comments/{$comment->id}", ['body' => 'Hacked.'])
        ->assertForbidden();

    $this->assertDatabaseMissing('comments', ['id' => $comment->id, 'body' => 'Hacked.']);
});

test('redirects guests away from comment update', function () {
    $comment = Comment::factory()->create();

    $this->put("/comments/{$comment->id}", ['body' => 'Attempt.'])
        ->assertRedirect(route('login'));
});

test('rejects a comment update missing a body', function () {
    $user = User::factory()->create();
    $comment = Comment::factory()->for($user)->create();

    $this->actingAs($user)
        ->from("/comments/{$comment->id}/edit")
        ->put("/comments/{$comment->id}", [])
        ->assertSessionHasErrors(['body']);
});

test('rejects a body exceeding 2000 characters', function () {
    $user = User::factory()->create();
    $comment = Comment::factory()->for($user)->create();

    $this->actingAs($user)
        ->from("/comments/{$comment->id}/edit")
        ->put("/comments/{$comment->id}", [
            'body' => str_repeat('a', 2001),
        ])
        ->assertSessionHasErrors(['body']);
});
