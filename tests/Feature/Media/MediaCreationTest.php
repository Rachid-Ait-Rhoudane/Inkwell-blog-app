<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('uploads a media file', function () {
    $user = User::factory()->create();

    Storage::fake('public');

    $file = UploadedFile::fake()->image('photo.jpg');

    $response = $this->actingAs($user)
        ->post(route('media.store'), [
            'files' => [$file],
        ]);

    $response->assertRedirect(route('media.index'));
    $response->assertSessionHas('status', 'success');

    $this->assertDatabaseHas('media', [
        'filename' => 'photo.jpg',
        'mime_type' => 'image/jpeg',
        'disk' => 'public',
        'user_id' => $user->id,
    ]);

    Storage::disk('public')->assertExists('media/'.$file->hashName());
});

it('uploads multiple files at once', function () {
    $user = User::factory()->create();

    Storage::fake('public');

    $files = [
        UploadedFile::fake()->image('first.jpg'),
        UploadedFile::fake()->image('second.jpg'),
        UploadedFile::fake()->image('third.jpg'),
    ];

    $this->actingAs($user)
        ->post(route('media.store'), [
            'files' => $files,
        ])
        ->assertRedirect(route('media.index'))
        ->assertSessionHas('status', 'success');

    expect($user->media()->count())->toBe(3);
});

it('redirects guests away from media upload', function () {
    $this->post(route('media.store'))
        ->assertRedirect(route('login'));
});

it('rejects an upload with no files field', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('media.store'), [])
        ->assertSessionHasErrors(['files']);
});

it('rejects an upload with an empty files array', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('media.store'), [
            'files' => [],
        ])
        ->assertSessionHasErrors(['files']);
});
