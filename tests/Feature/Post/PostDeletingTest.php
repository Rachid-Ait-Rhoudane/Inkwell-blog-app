<?php

use App\Models\Post;
use App\Models\User;

it('deletes a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    $response = $this->actingAs($user)
        ->delete(route('posts.destroy', $post));

    $response->assertRedirect(route('posts.index', absolute: false));
    $response->assertSessionHas('status', 'success');

    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
});

it('prevents a non-owner from deleting a post', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $post = Post::factory()->for($owner)->create();

    $this->actingAs($other)
        ->delete(route('posts.destroy', $post))
        ->assertForbidden();

    $this->assertDatabaseHas('posts', ['id' => $post->id]);
});

it('redirects guests away from post deletion', function () {
    $post = Post::factory()->create();

    $this->delete(route('posts.destroy', $post))
        ->assertRedirect(route('login'));

    $this->assertDatabaseHas('posts', ['id' => $post->id]);
});
