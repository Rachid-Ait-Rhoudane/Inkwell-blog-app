<?php

use App\Models\Category;
use App\Models\User;

it('redirects guests to the login page', function () {
    visit('/categories')->assertPathIs('/login');
});

it('deletes a category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();

    $this->actingAs($user);

    visit('/categories')
        ->click('Delete')
        ->assertSee('Are you sure you want to delete')
        ->click('@delete')
        ->assertSee('Category deleted successfully.');
});
