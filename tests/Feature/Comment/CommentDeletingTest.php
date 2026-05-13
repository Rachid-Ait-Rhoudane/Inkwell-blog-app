<?php

use App\Models\Comment;
use App\Models\User;

it('deletes a comment', function () {
    $user = User::factory()->create();
    $comment = Comment::factory()->for($user)->create();

    $response = $this->actingAs($user)
        ->delete(route('comments.destroy', $comment));

    $response->assertRedirect(route('comments.index'));
    $response->assertSessionHas('status', 'success');

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});

it('prevents a non-owner from deleting a comment', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $comment = Comment::factory()->for($owner)->create();

    $this->actingAs($other)
        ->delete(route('comments.destroy', $comment))
        ->assertForbidden();

    $this->assertDatabaseHas('comments', ['id' => $comment->id]);
});

it('redirects guests away from comment deletion', function () {
    $comment = Comment::factory()->create();

    $this->delete(route('comments.destroy', $comment))
        ->assertRedirect(route('login'));

    $this->assertDatabaseHas('comments', ['id' => $comment->id]);
});
