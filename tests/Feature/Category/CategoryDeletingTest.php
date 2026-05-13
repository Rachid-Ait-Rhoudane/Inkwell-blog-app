<?php

use App\Models\Category;
use App\Models\User;

it('deletes a category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();

    $response = $this->actingAs($user)
        ->delete(route('categories.destroy', $category));

    $response->assertRedirect(route('categories.index'));
    $response->assertSessionHas('status', 'success');

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

it('prevents a non-owner from deleting a category', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $category = Category::factory()->for($owner)->create();

    $this->actingAs($other)
        ->delete(route('categories.destroy', $category))
        ->assertForbidden();

    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});

it('redirects guests away from category deletion', function () {
    $category = Category::factory()->create();

    $this->delete(route('categories.destroy', $category))
        ->assertRedirect(route('login'));

    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});
