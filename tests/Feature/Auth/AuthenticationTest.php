<?php

use App\Models\User;

it('shows the login page', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);

    $response->assertOk();
});

it('user can authenticate using the login screen', function () {

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

it("wrong email can't be allowed to login", function () {

    $response = $this->post(route('login.store'), [
        'email' => 'wrong@example.com',
        'password' => 'password',
    ]);

    $response->assertRedirectBackWithErrors(['email']);

    $this->assertGuest();
});

it("wrong password can't be allowed to login", function () {

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

it('the email field of the login must be a valide email', function () {

    $response = $this->post(route('login.store'), [
        'email' => 'wrongexample.com',
        'password' => 'password',
    ]);

    $response->assertStatus(302);

    $response->assertRedirectBackWithErrors(['email']);

    $this->assertGuest();
});
