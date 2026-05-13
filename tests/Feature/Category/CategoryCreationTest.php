<?php

use App\Models\Category;
use App\Models\User;

it('shows the create category page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('categories.create'))
        ->assertSuccessful();
});

it('creates a new category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->make();

    $response = $this->actingAs($user)
        ->from(route('categories.create'))
        ->post(route('categories.store'), [
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
        ]);

    $response->assertRedirect(route('categories.index'));
    $response->assertSessionHas('status', 'success');

    $this->assertDatabaseHas('categories', [
        'name' => $category->name,
        'slug' => $category->slug,
        'user_id' => $user->id,
    ]);
});

it('creates a category without a description', function () {
    $user = User::factory()->create();
    $category = Category::factory()->make();

    $this->actingAs($user)
        ->post(route('categories.store'), [
            'name' => $category->name,
            'slug' => $category->slug,
        ])
        ->assertRedirect(route('categories.index'))
        ->assertSessionHas('status', 'success');

    $this->assertDatabaseHas('categories', [
        'slug' => $category->slug,
        'description' => null,
    ]);
});

it('redirects guests away from category creation', function () {
    $this->get(route('categories.create'))
        ->assertRedirect(route('login'));

    $this->post(route('categories.store'))
        ->assertRedirect(route('login'));
});

it('rejects a category missing a name', function () {
    $user = User::factory()->create();
    $category = Category::factory()->make();

    $this->actingAs($user)
        ->from(route('categories.create'))
        ->post(route('categories.store'), [
            'slug' => $category->slug,
        ])
        ->assertSessionHasErrors(['name']);
});

it('rejects a category missing a slug', function () {
    $user = User::factory()->create();
    $category = Category::factory()->make();

    $this->actingAs($user)
        ->from(route('categories.create'))
        ->post(route('categories.store'), [
            'name' => $category->name,
        ])
        ->assertSessionHasErrors(['slug']);
});

it('rejects a duplicate slug', function () {
    $user = User::factory()->create();
    $existing = Category::factory()->for($user)->create();
    $category = Category::factory()->make();

    $this->actingAs($user)
        ->from(route('categories.create'))
        ->post(route('categories.store'), [
            'name' => $category->name,
            'slug' => $existing->slug,
        ])
        ->assertSessionHasErrors(['slug']);
});
