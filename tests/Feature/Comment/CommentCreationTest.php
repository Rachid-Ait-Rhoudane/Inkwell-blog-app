<?php

use App\Models\Post;
use App\Models\User;

test('posts a comment on a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    $response = $this->actingAs($user)
        ->from(route('posts.show', $post))
        ->post(route('comments.store', $post), [
            'body' => 'This is a great post!',
        ]);

    $response->assertRedirect(route('posts.show', $post));
    $response->assertSessionHas('status', 'success');

    $this->assertDatabaseHas('comments', [
        'post_id' => $post->id,
        'user_id' => $user->id,
        'body' => 'This is a great post!',
    ]);
});

test('redirects guests away from comment creation', function () {
    $post = Post::factory()->create();

    $this->post(route('comments.store', $post))
        ->assertRedirect(route('login'));
});

test('rejects a comment missing a body', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    $this->actingAs($user)
        ->from(route('posts.show', $post))
        ->post(route('comments.store', $post), [])
        ->assertSessionHasErrors(['body']);
});

test('trims whitespace from the comment body', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    $this->actingAs($user)
        ->post(route('comments.store', $post), [
            'body' => '  hello world  ',
        ]);

    $this->assertDatabaseHas('comments', [
        'post_id' => $post->id,
        'body' => 'hello world',
    ]);
});
