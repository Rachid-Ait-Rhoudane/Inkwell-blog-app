<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render("Home");
});

Route::get('/hello', function () {
    return Inertia::render("Hello");
});

Route::get('/welcome', function () {
    return Inertia::render("Welcome");
});
