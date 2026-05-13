<?php

use App\Models\Media;
use App\Models\User;

it('shows the media listing', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('media.index'))
        ->assertSuccessful();
});

it('redirects guests away from media listing', function () {
    $this->get(route('media.index'))
        ->assertRedirect(route('login'));
});

it('only shows the authenticated user\'s own media', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    Media::factory()->for($user)->count(2)->create();
    Media::factory()->for($other)->count(3)->create();

    $this->actingAs($user)
        ->get(route('media.index'))
        ->assertInertia(fn ($page) => $page
            ->has('media.data', 2)
        );
});

it('paginates media at 24 per page', function () {
    $user = User::factory()->create();

    Media::factory()->for($user)->count(25)->create();

    $this->actingAs($user)
        ->get(route('media.index'))
        ->assertInertia(fn ($page) => $page
            ->has('media.data', 24)
        );
});
