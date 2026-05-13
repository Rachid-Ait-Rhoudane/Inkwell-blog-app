<?php

use App\Models\Category;
use App\Models\User;

it('redirects guests to the login page', function () {
    $category = Category::factory()->create();

    visit("/categories/{$category->id}/edit")->assertPathIs('/login');
});

it('shows the edit category page', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();

    $this->actingAs($user);

    $page = visit('/dashboard');

    $page->assertSeeLink('Categories')
        ->click('Categories')
        ->click('Edit')
        ->assertTitle('Edit Category')
        ->assertSee('Back to Categories');
});

it('updates a category with valid data', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();

    $this->actingAs($user);

    visit("/categories/{$category->id}/edit")
        ->fill('name', 'Updated Name')
        ->fill('slug', 'updated-slug')
        ->click('Update Category')
        ->assertPathIs('/categories')
        ->assertSee('Category updated successfully.');
});

it('shows an error for a duplicate slug', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();
    $other = Category::factory()->for($user)->create();

    $this->actingAs($user);

    visit("/categories/{$category->id}/edit")
        ->fill('slug', $other->slug)
        ->click('Update Category')
        ->assertSee('The slug has already been taken.');
});

it('shows an error for a missing name', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();

    $this->actingAs($user);

    visit("/categories/{$category->id}/edit")
        ->fill('name', '')
        ->click('Update Category')
        ->assertSee('The name field is required.');
});
