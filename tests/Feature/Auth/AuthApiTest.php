<?php

use App\Domain\User\Models\User;

test('register creates a new user', function () {
    $response = $this->postJson('/api/v1/public/auth/register', [
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'first_name' => 'Test',
        'last_name' => 'User',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user',
                'tokens' => ['access_token', 'refresh_token', 'token_type'],
            ],
        ])
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
});

test('register fails with duplicate email', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->postJson('/api/v1/public/auth/register', [
        'email' => 'existing@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'first_name' => 'Test',
        'last_name' => 'User',
    ]);

    $response->assertStatus(422);
});

test('register fails with weak password', function () {
    $response = $this->postJson('/api/v1/public/auth/register', [
        'email' => 'test@example.com',
        'password' => '123',
        'password_confirmation' => '123',
        'first_name' => 'Test',
        'last_name' => 'User',
    ]);

    $response->assertStatus(422);
});

test('login returns tokens for valid credentials', function () {
    User::factory()->create([
        'email' => 'login@example.com',
        'password' => bcrypt('Password123!'),
        'is_verified' => true,
    ]);

    $response = $this->postJson('/api/v1/public/auth/login', [
        'email' => 'login@example.com',
        'password' => 'Password123!',
    ]);

    $response->assertOk()
        ->assertJson(['success' => true])
        ->assertJsonStructure([
            'data' => [
                'user',
                'tokens' => ['access_token', 'refresh_token', 'token_type'],
            ],
        ]);
});

test('login fails with invalid credentials', function () {
    User::factory()->create([
        'email' => 'login@example.com',
        'password' => bcrypt('Password123!'),
    ]);

    $response = $this->postJson('/api/v1/public/auth/login', [
        'email' => 'login@example.com',
        'password' => 'WrongPassword!',
    ]);

    $response->assertStatus(401);
});

test('verify email with valid token', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->getJson("/api/v1/public/auth/verify-email/{$user->verification_token}");

    $response->assertOk()
        ->assertJson(['success' => true]);

    $user->refresh();
    expect($user->is_verified)->toBeTrue();
});

test('forgot password sends reset email', function () {
    User::factory()->create(['email' => 'reset@example.com']);

    $response = $this->postJson('/api/v1/public/auth/forgot-password', [
        'email' => 'reset@example.com',
    ]);

    $response->assertOk()
        ->assertJson(['success' => true]);
});
