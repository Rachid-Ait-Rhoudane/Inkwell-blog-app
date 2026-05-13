<?php

use App\Models\Category;
use App\Models\Media;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('updates a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();
    $category = Category::factory()->create();
    $updated = Post::factory()->make();

    Storage::fake('public');

    $file = UploadedFile::fake()->image('cover.jpg');

    $response = $this->actingAs($user)
        ->from(route('posts.edit', $post))
        ->put(route('posts.update', $post), [
            'title' => $updated->title,
            'slug' => $updated->slug,
            'content' => $updated->content,
            'excerpt' => $updated->excerpt,
            'status' => $updated->status,
            'published_at' => $updated->published_at,
            'category_ids' => [$category->id],
            'media_files' => [$file],
        ]);

    $response->assertRedirect(route('posts.index', absolute: false));
    $response->assertSessionHas('status', 'success');

    $post->refresh();

    expect($post->title)->toBe($updated->title);
    expect($post->slug)->toBe($updated->slug);
    expect($post->content)->toBe($updated->content);
    expect($post->categories)->toHaveCount(1);
    expect($post->categories->first()->id)->toBe($category->id);
    expect($post->media)->toHaveCount(1);

    $this->assertDatabaseHas('media', ['filename' => $file->getClientOriginalName()]);
    Storage::disk('public')->assertExists('media/'.$file->hashName());
});

it('attaches existing media via media_ids on update', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();
    $media = Media::factory()->for($user)->create();
    $updated = Post::factory()->make();

    $this->actingAs($user)
        ->put(route('posts.update', $post), [
            'title' => $updated->title,
            'slug' => $updated->slug,
            'content' => $updated->content,
            'status' => $updated->status,
            'media_ids' => [$media->id],
        ]);

    $post->refresh();

    expect($post->media)->toHaveCount(1);
    expect($post->media->first()->id)->toBe($media->id);
});

it('prevents a non-owner from updating a post', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $post = Post::factory()->for($owner)->create();
    $updated = Post::factory()->make();

    $this->actingAs($other)
        ->put(route('posts.update', $post), [
            'title' => $updated->title,
            'slug' => $updated->slug,
            'content' => $updated->content,
            'status' => $updated->status,
        ])
        ->assertForbidden();
});

it('redirects guests away from post update', function () {
    $post = Post::factory()->create();

    $this->put(route('posts.update', $post))
        ->assertRedirect(route('login'));
});

it('allows updating a post with its own slug', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();
    $updated = Post::factory()->make();

    $this->actingAs($user)
        ->put(route('posts.update', $post), [
            'title' => $updated->title,
            'slug' => $post->slug,
            'content' => $updated->content,
            'status' => $updated->status,
        ])
        ->assertRedirect(route('posts.index', absolute: false));
});

it('rejects a slug already taken by another post', function () {
    $user = User::factory()->create();
    $postA = Post::factory()->for($user)->create();
    $postB = Post::factory()->for($user)->create();
    $updated = Post::factory()->make();

    $this->actingAs($user)
        ->from(route('posts.edit', $postA))
        ->put(route('posts.update', $postA), [
            'title' => $updated->title,
            'slug' => $postB->slug,
            'content' => $updated->content,
            'status' => $updated->status,
        ])
        ->assertSessionHasErrors(['slug']);
});

it('sets published_at to now when publishing for the first time', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create(['status' => 'draft', 'published_at' => null]);
    $updated = Post::factory()->make();

    $this->actingAs($user)
        ->put(route('posts.update', $post), [
            'title' => $updated->title,
            'slug' => $updated->slug,
            'content' => $updated->content,
            'status' => 'published',
            'published_at' => null,
        ]);

    expect($post->refresh()->published_at)->not->toBeNull();
});

it('preserves existing published_at when re-publishing', function () {
    $originalDate = '2024-06-01 00:00:00';
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create([
        'status' => 'published',
        'published_at' => $originalDate,
    ]);
    $updated = Post::factory()->make();

    $this->actingAs($user)
        ->put(route('posts.update', $post), [
            'title' => $updated->title,
            'slug' => $updated->slug,
            'content' => $updated->content,
            'status' => 'published',
            'published_at' => null,
        ]);

    expect($post->refresh()->published_at->toDateTimeString())->toBe($originalDate);
});

it('clears published_at when changing to draft', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->published()->create();
    $updated = Post::factory()->make();

    $this->actingAs($user)
        ->put(route('posts.update', $post), [
            'title' => $updated->title,
            'slug' => $updated->slug,
            'content' => $updated->content,
            'status' => 'draft',
        ]);

    expect($post->refresh()->published_at)->toBeNull();
});

it('rejects an update missing a title', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    $this->actingAs($user)
        ->from(route('posts.edit', $post))
        ->put(route('posts.update', $post), [
            'slug' => $post->slug,
            'content' => $post->content,
            'status' => $post->status,
        ])
        ->assertSessionHasErrors(['title']);
});

it('rejects an update missing a slug', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    $this->actingAs($user)
        ->from(route('posts.edit', $post))
        ->put(route('posts.update', $post), [
            'title' => $post->title,
            'content' => $post->content,
            'status' => $post->status,
        ])
        ->assertSessionHasErrors(['slug']);
});

it('rejects an update missing content', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    $this->actingAs($user)
        ->from(route('posts.edit', $post))
        ->put(route('posts.update', $post), [
            'title' => $post->title,
            'slug' => $post->slug,
            'status' => $post->status,
        ])
        ->assertSessionHasErrors(['content']);
});

it('rejects an update missing a status', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    $this->actingAs($user)
        ->from(route('posts.edit', $post))
        ->put(route('posts.update', $post), [
            'title' => $post->title,
            'slug' => $post->slug,
            'content' => $post->content,
        ])
        ->assertSessionHasErrors(['status']);
});

it('rejects an invalid status on update', function (string $status) {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    $this->actingAs($user)
        ->from(route('posts.edit', $post))
        ->put(route('posts.update', $post), [
            'title' => $post->title,
            'slug' => $post->slug,
            'content' => $post->content,
            'status' => $status,
        ])
        ->assertSessionHasErrors(['status']);
})->with(['scheduled', 'pending', 'archived']);

it('rejects a non-existent category id on update', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    $this->actingAs($user)
        ->from(route('posts.edit', $post))
        ->put(route('posts.update', $post), [
            'title' => $post->title,
            'slug' => $post->slug,
            'content' => $post->content,
            'status' => $post->status,
            'category_ids' => [999999],
        ])
        ->assertSessionHasErrors(['category_ids.0']);
});

it('rejects a non-existent media id on update', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    $this->actingAs($user)
        ->from(route('posts.edit', $post))
        ->put(route('posts.update', $post), [
            'title' => $post->title,
            'slug' => $post->slug,
            'content' => $post->content,
            'status' => $post->status,
            'media_ids' => [999999],
        ])
        ->assertSessionHasErrors(['media_ids.0']);
});
