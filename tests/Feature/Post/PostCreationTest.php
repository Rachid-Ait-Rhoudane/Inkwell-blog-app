<?php

use App\Models\Category;
use App\Models\Media;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('shows the create new post listing', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('posts.create'))
        ->assertSuccessful();
});

test('creates a new Post', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()->make();

    Storage::fake('public');

    $file = UploadedFile::fake()->image('cover.jpg');

    $response = $this->actingAs($user)
        ->from(route('posts.create'))
        ->post(route('posts.store'), [
            'title' => $post->title,
            'slug' => $post->slug,
            'content' => $post->content,
            'excerpt' => $post->excerpt,
            'status' => $post->status,
            'published_at' => $post->published_at,
            'category_ids' => [$category->id],
            'media_files' => [$file],
        ]);

    $response->assertRedirect(route('posts.index', absolute: false));
    $response->assertSessionHas('status', 'success');

    $newPost = Post::first();

    expect($newPost->title)->toBe($post->title);
    expect($newPost->slug)->toBe($post->slug);
    expect($newPost->content)->toBe($post->content);
    expect($newPost->user_id)->toBe($user->id);
    expect($newPost->categories)->toHaveCount(1);
    expect($newPost->categories->first()->id)->toBe($category->id);
    expect($newPost->media)->toHaveCount(1);

    $this->assertDatabaseHas('media', ['filename' => $file->getClientOriginalName()]);
    Storage::disk('public')->assertExists('media/'.$file->hashName());
});

test('sets published_at automatically when publishing without a date', function () {
    $user = User::factory()->create();
    $post = Post::factory()->make();

    $this->actingAs($user)
        ->post(route('posts.store'), [
            'title' => $post->title,
            'slug' => $post->slug,
            'content' => $post->content,
            'status' => 'published',
            'published_at' => null,
        ]);

    expect(Post::first()->published_at)->not->toBeNull();
});

test('clears published_at when saving as draft', function () {
    $user = User::factory()->create();
    $post = Post::factory()->make();

    $this->actingAs($user)
        ->post(route('posts.store'), [
            'title' => $post->title,
            'slug' => $post->slug,
            'content' => $post->content,
            'status' => 'draft',
            'published_at' => '2025-01-01',
        ]);

    expect(Post::first()->published_at)->toBeNull();
});

test('attaches existing media via media_ids', function () {
    $user = User::factory()->create();
    $post = Post::factory()->make();
    $media = Media::factory()->for($user)->create();

    $this->actingAs($user)
        ->post(route('posts.store'), [
            'title' => $post->title,
            'slug' => $post->slug,
            'content' => $post->content,
            'status' => $post->status,
            'media_ids' => [$media->id],
        ]);

    $newPost = Post::first();

    expect($newPost->media)->toHaveCount(1);
    expect($newPost->media->first()->id)->toBe($media->id);
});

test('redirects guests away from post creation', function () {
    $this->get(route('posts.create'))
        ->assertRedirect(route('login'));

    $this->post(route('posts.store'))
        ->assertRedirect(route('login'));
});

test('rejects a post missing a title', function () {
    $user = User::factory()->create();
    $post = Post::factory()->make();

    $this->actingAs($user)
        ->from(route('posts.create'))
        ->post(route('posts.store'), [
            'slug' => $post->slug,
            'content' => $post->content,
            'status' => $post->status,
        ])
        ->assertSessionHasErrors(['title']);
});

test('rejects a post missing a slug', function () {
    $user = User::factory()->create();
    $post = Post::factory()->make();

    $this->actingAs($user)
        ->from(route('posts.create'))
        ->post(route('posts.store'), [
            'title' => $post->title,
            'content' => $post->content,
            'status' => $post->status,
        ])
        ->assertSessionHasErrors(['slug']);
});

test('rejects a post missing content', function () {
    $user = User::factory()->create();
    $post = Post::factory()->make();

    $this->actingAs($user)
        ->from(route('posts.create'))
        ->post(route('posts.store'), [
            'title' => $post->title,
            'slug' => $post->slug,
            'status' => $post->status,
        ])
        ->assertSessionHasErrors(['content']);
});

test('rejects a post missing a status', function () {
    $user = User::factory()->create();
    $post = Post::factory()->make();

    $this->actingAs($user)
        ->from(route('posts.create'))
        ->post(route('posts.store'), [
            'title' => $post->title,
            'slug' => $post->slug,
            'content' => $post->content,
        ])
        ->assertSessionHasErrors(['status']);
});

test('rejects a duplicate slug', function () {
    $user = User::factory()->create();
    $existing = Post::factory()->for($user)->create();
    $post = Post::factory()->make();

    $this->actingAs($user)
        ->from(route('posts.create'))
        ->post(route('posts.store'), [
            'title' => $post->title,
            'slug' => $existing->slug,
            'content' => $post->content,
            'status' => $post->status,
        ])
        ->assertSessionHasErrors(['slug']);
});

test('rejects an invalid status', function (string $status) {
    $user = User::factory()->create();
    $post = Post::factory()->make();

    $this->actingAs($user)
        ->from(route('posts.create'))
        ->post(route('posts.store'), [
            'title' => $post->title,
            'slug' => $post->slug,
            'content' => $post->content,
            'status' => $status,
        ])
        ->assertSessionHasErrors(['status']);
})->with(['scheduled', 'pending', 'archived']);

test('rejects a non-existent category id', function () {
    $user = User::factory()->create();
    $post = Post::factory()->make();

    $this->actingAs($user)
        ->from(route('posts.create'))
        ->post(route('posts.store'), [
            'title' => $post->title,
            'slug' => $post->slug,
            'content' => $post->content,
            'status' => $post->status,
            'category_ids' => [999999],
        ])
        ->assertSessionHasErrors(['category_ids.0']);
});

test('rejects a non-existent media id', function () {
    $user = User::factory()->create();
    $post = Post::factory()->make();

    $this->actingAs($user)
        ->from(route('posts.create'))
        ->post(route('posts.store'), [
            'title' => $post->title,
            'slug' => $post->slug,
            'content' => $post->content,
            'status' => $post->status,
            'media_ids' => [999999],
        ])
        ->assertSessionHasErrors(['media_ids.0']);
});
