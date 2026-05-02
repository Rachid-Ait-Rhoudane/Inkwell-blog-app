<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

test('shows the category listing', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('categories.index'))
        ->assertSuccessful();
});

test('redirects guests away from category listing', function () {
    $this->get(route('categories.index'))
        ->assertRedirect(route('login'));
});

test('only shows the authenticated user\'s own categories', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    Category::factory()->for($user)->count(2)->create();
    Category::factory()->for($other)->count(3)->create();

    $this->actingAs($user)
        ->get(route('categories.index'))
        ->assertInertia(fn ($page) => $page
            ->has('categories.data', 2)
        );
});

test('includes the post count for each category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();
    $posts = Post::factory()->for($user)->count(3)->create();
    $category->posts()->attach($posts->pluck('id'));

    $this->actingAs($user)
        ->get(route('categories.index'))
        ->assertInertia(fn ($page) => $page
            ->where('categories.data.0.posts_count', 3)
        );
});

test('orders categories alphabetically by name', function () {
    $user = User::factory()->create();

    Category::factory()->for($user)->create(['name' => 'Zebra', 'slug' => 'zebra']);
    Category::factory()->for($user)->create(['name' => 'Apple', 'slug' => 'apple']);
    Category::factory()->for($user)->create(['name' => 'Mango', 'slug' => 'mango']);

    $this->actingAs($user)
        ->get(route('categories.index'))
        ->assertInertia(fn ($page) => $page
            ->where('categories.data.0.name', 'Apple')
            ->where('categories.data.1.name', 'Mango')
            ->where('categories.data.2.name', 'Zebra')
        );
});

test('paginates categories at 15 per page', function () {
    $user = User::factory()->create();

    Category::factory()->for($user)->count(16)->create();

    $this->actingAs($user)
        ->get(route('categories.index'))
        ->assertInertia(fn ($page) => $page
            ->has('categories.data', 15)
        );
});
