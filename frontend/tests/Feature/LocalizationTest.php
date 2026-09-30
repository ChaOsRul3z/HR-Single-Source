<?php

use App\Models\User;
use Mcamara\LaravelLocalization\LaravelLocalization;

afterEach(function () {
    putenv(LaravelLocalization::ENV_ROUTE_KEY);
});

test('dutch is the default locale without a url prefix', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/dashboard')
        ->assertOk()
        ->assertSee('Actieve documenten');
});

test('english pages live under the en prefix', function () {
    putenv(LaravelLocalization::ENV_ROUTE_KEY.'=en');
    $this->refreshApplication();
    $this->artisan('migrate');

    $this->actingAs(User::factory()->create());

    $this->get('/en/dashboard')
        ->assertOk()
        ->assertSee('Active documents')
        ->assertDontSee('Actieve documenten');
});
