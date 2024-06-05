<?php

use App\Actions\Auth\LogoutUser;
use App\Models\User;

test('can log out a user', function () {
    $user = User::factory()->create();
    $logoutUser = new LogoutUser();

    $this->actingAs($user);

    expect(Auth::check())->toBeTrue();

    $logoutUser();

    expect(Auth::check())->toBeFalse();
});

test('can log out a guest user', function () {
    $logoutUser = new LogoutUser();

    $logoutUser();

    expect(Auth::check())->toBeFalse();
});
