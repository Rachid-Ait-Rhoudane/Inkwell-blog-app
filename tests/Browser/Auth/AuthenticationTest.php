<?php

use App\Models\User;

it('shows the login page from the home page', function () {
    $page = visit('/');

    $page->assertTitle('Welcome')
        ->click("Log in")
        ->assertSee('Welcome back')
        ->assertSee('Sign in to your account to continue');
});

it('logs in with valid credentials', function () {
    $user = User::factory()->create();

    $page = visit('/login');

    $page->fill('email', $user->email)
         ->fill('password', 'password')
         ->click('Sign in')
         ->assertPathIs('/');
});

it('shows an error for wrong credentials', function () {
    $user = User::factory()->create();

    $page = visit('/login');

    $page->fill('email', $user->email)
         ->fill('password', 'wrong-password')
         ->click('Sign in')
         ->assertSee('These credentials do not match our records.');
});

it('shows an error for an unknown email', function () {
    $page = visit('/login');

    $page->fill('email', 'nobody@example.com')
         ->fill('password', 'password')
         ->click('Sign in')
         ->assertSee('These credentials do not match our records.');
});
