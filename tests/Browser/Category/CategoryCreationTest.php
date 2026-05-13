<?php

use App\Models\Category;
use App\Models\User;

it('redirects guests to the login page', function () {
    visit('/categories/create')->assertPathIs('/login');
});


it('shows the create category page', function () {

    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit('/dashboard');

    $page->assertSeeLink('Categories')
        ->click('Categories')
        ->assertSeeLink('New Category')
        ->click('New Category')
        ->assertPathIs("/categories/create");
});

it('creates a category with valid data', function () {

    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit('/categories/create');

    $page->fill('name', 'Technology')
    ->fill('slug', 'technology')
    ->click('Save Category')
    ->assertPathIs('/categories')
    ->assertSee("Category created successfully.");
});

it('shows an error for a duplicate slug', function () {

    $user = User::factory()->create();

    $existing = Category::factory()->for($user)->create();

    $this->actingAs($user);

    $page = visit('/categories/create');

    $page->fill('name', 'Some Name')
        ->fill('slug', $existing->slug)
        ->click('Save Category')
        ->assertSee('The slug has already been taken.');
});

it('shows an error for a missing name', function () {

    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit('/categories/create');

    $page->fill('slug', 'some-slug')
        ->click('Save Category')
        ->assertSee('The name field is required.');
});
