<?php

use App\Models\User;

it('shows the register page from the home page', function () {
    $page = visit('/');
    $page->assertTitle('Welcome')
        ->click("Get started")
        ->assertSee('Create an account')
        ->assertSee('Join Inkwell and start writing today');
});

it('registers with valid data', function () {
    $page = visit('/register');

    $page->fill('name', 'Jane Smith')
         ->fill('email', 'jane@example.com')
         ->fill('password', 'password')
         ->fill('password_confirmation', 'password')
         ->click('Create account')
         ->assertPathIs('/');
});

it('shows an error for a duplicate email', function () {
    $existing = User::factory()->create();

    $page = visit('/register');

    $page->fill('name', 'Jane Smith')
         ->fill('email', $existing->email)
         ->fill('password', 'password')
         ->fill('password_confirmation', 'password')
         ->click('Create account')
         ->assertSee('The email has already been taken.');
});

it('shows an error for mismatched passwords', function () {
    $page = visit('/register');

    $page->fill('name', 'Jane Smith')
         ->fill('email', 'jane@example.com')
         ->fill('password', 'password')
         ->fill('password_confirmation', 'different-password')
         ->click('Create account')
         ->assertSee('The password field confirmation does not match.');
});

it('shows an error for a password that is too short', function () {
    $page = visit('/register');

    $page->fill('name', 'Jane Smith')
         ->fill('email', 'jane@example.com')
         ->fill('password', 'short')
         ->fill('password_confirmation', 'short')
         ->click('Create account')
         ->assertSee('The password field must be at least 8 characters.');
});
