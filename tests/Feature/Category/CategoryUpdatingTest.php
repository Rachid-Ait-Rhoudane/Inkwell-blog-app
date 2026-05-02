<?php

use App\Models\Category;
use App\Models\User;

test('shows the edit category page', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('categories.edit', $category))
        ->assertSuccessful();
});

test('prevents a non-owner from viewing the edit page', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $category = Category::factory()->for($owner)->create();

    $this->actingAs($other)
        ->get(route('categories.edit', $category))
        ->assertForbidden();
});

test('updates a category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();
    $updated = Category::factory()->make();

    $response = $this->actingAs($user)
        ->from(route('categories.edit', $category))
        ->put(route('categories.update', $category), [
            'name' => $updated->name,
            'slug' => $updated->slug,
            'description' => $updated->description,
        ]);

    $response->assertRedirect(route('categories.index'));
    $response->assertSessionHas('status', 'success');

    $category->refresh();

    expect($category->name)->toBe($updated->name);
    expect($category->slug)->toBe($updated->slug);
});

test('allows updating a category with its own slug', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();
    $updated = Category::factory()->make();

    $this->actingAs($user)
        ->put(route('categories.update', $category), [
            'name' => $updated->name,
            'slug' => $category->slug,
        ])
        ->assertRedirect(route('categories.index'));
});

test('rejects a slug already taken by another category', function () {
    $user = User::factory()->create();
    $categoryA = Category::factory()->for($user)->create();
    $categoryB = Category::factory()->for($user)->create();
    $updated = Category::factory()->make();

    $this->actingAs($user)
        ->from(route('categories.edit', $categoryA))
        ->put(route('categories.update', $categoryA), [
            'name' => $updated->name,
            'slug' => $categoryB->slug,
        ])
        ->assertSessionHasErrors(['slug']);
});

test('prevents a non-owner from updating a category', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $category = Category::factory()->for($owner)->create();
    $updated = Category::factory()->make();

    $this->actingAs($other)
        ->put(route('categories.update', $category), [
            'name' => $updated->name,
            'slug' => $updated->slug,
        ])
        ->assertForbidden();
});

test('redirects guests away from category update', function () {
    $category = Category::factory()->create();

    $this->put(route('categories.update', $category))
        ->assertRedirect(route('login'));
});

test('rejects an update missing a name', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();

    $this->actingAs($user)
        ->from(route('categories.edit', $category))
        ->put(route('categories.update', $category), [
            'slug' => $category->slug,
        ])
        ->assertSessionHasErrors(['name']);
});

test('rejects an update missing a slug', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();

    $this->actingAs($user)
        ->from(route('categories.edit', $category))
        ->put(route('categories.update', $category), [
            'name' => $category->name,
        ])
        ->assertSessionHasErrors(['slug']);
});
