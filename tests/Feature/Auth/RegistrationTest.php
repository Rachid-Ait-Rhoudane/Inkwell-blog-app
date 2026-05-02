<?php

test('shows the register page', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('allows new users to register via the register page', function () {

    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'johnd@test.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();

    $response->assertStatus(302);

    $response->assertRedirect(route('welcome', absolute: false));
});

test('doesn\'t allow registration with an invalid email', function () {

    $response = $this->from(route('register'))
        ->post(route('register.store'), [
            'name' => 'John Doe',
            'email' => 'johndtest.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

    $this->assertGuest();

    $response->assertRedirect(route('register', absolute: false));

    $response->assertSessionHasErrors(['email']);
});

test('doesn\'t allow registration with an inconfirmed password', function () {

    $response = $this->from(route('register'))
        ->post(route('register.store'), [
            'name' => 'John Doe',
            'email' => 'johnd@test.com',
            'password' => 'password',
            'password_confirmation' => '6565465464',
        ]);

    $this->assertGuest();

    $response->assertRedirect(route('register', absolute: false));

    $response->assertSessionHasErrors(['password']);
});

test('doesn\'t allow registration with a missing name field', function () {

    $response = $this->from(route('register'))
        ->post(route('register.store'), [
            'name' => '',
            'email' => 'johnd@test.com',
            'password' => 'password',
            'password_confirmation' => '6565465464',
        ]);

    $this->assertGuest();

    $response->assertRedirect(route('register', absolute: false));

    $response->assertSessionHasErrors(['name']);
});
