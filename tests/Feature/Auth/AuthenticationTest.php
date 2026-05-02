<?php

use App\Models\User;

test('shows the login page', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);

    $response->assertOk();
});

test('user can authenticate using the login screen', function () {

    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();

    $response->assertStatus(302);

    $response->assertRedirect(route('welcome', absolute: false));
});

test("wrong email can't be allowed to login", function () {

    $response = $this->post(route('login.store'), [
        'email' => 'wrong@example.com',
        'password' => 'password',
    ]);

    $response->assertRedirectBackWithErrors(['email']);

    $this->assertGuest();
});

test("wrong password can't be allowed to login", function () {

    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'passwo64684684rd',
    ]);

    $response->assertRedirectBackWithErrors(['email']);

    $this->assertGuest();
});

test('the email field of the login must be a valide email', function () {

    $response = $this->post(route('login.store'), [
        'email' => 'wrongexample.com',
        'password' => 'password',
    ]);

    $response->assertStatus(302);

    $response->assertRedirectBackWithErrors(['email']);

    $this->assertGuest();
});
