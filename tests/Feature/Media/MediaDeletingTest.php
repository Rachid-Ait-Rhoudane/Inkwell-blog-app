<?php

use App\Models\Media;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('deletes a media file', function () {
    $user = User::factory()->create();

    Storage::fake('public');
    Storage::disk('public')->put('media/test.jpg', 'fake content');

    $medium = Media::factory()->for($user)->create([
        'path' => 'media/test.jpg',
        'disk' => 'public',
    ]);

    $response = $this->actingAs($user)
        ->delete(route('media.destroy', $medium));

    $response->assertRedirect(route('media.index'));
    $response->assertSessionHas('status', 'success');

    $this->assertDatabaseMissing('media', ['id' => $medium->id]);
    Storage::disk('public')->assertMissing('media/test.jpg');
});

test('prevents a non-owner from deleting media', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $medium = Media::factory()->for($owner)->create();

    $this->actingAs($other)
        ->delete(route('media.destroy', $medium))
        ->assertForbidden();

    $this->assertDatabaseHas('media', ['id' => $medium->id]);
});

test('redirects guests away from media deletion', function () {
    $medium = Media::factory()->create();

    $this->delete(route('media.destroy', $medium))
        ->assertRedirect(route('login'));

    $this->assertDatabaseHas('media', ['id' => $medium->id]);
});
