<?php

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function () {
    Cache::flush();
});

test('login is rate limited to 5 attempts per minute', function () {
    $member = Member::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    // Make 5 failed login attempts (should all succeed in getting 401)
    for ($i = 0; $i < 5; $i++) {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
    }

    // 6th attempt should be rate limited
    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(429); // Too Many Requests
});

test('successful logins are also rate limited', function () {
    $member = Member::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    // Make 5 successful login attempts (all should succeed)
    for ($i = 0; $i < 5; $i++) {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertSuccessful();
    }

    // 6th attempt should be rate limited even with correct credentials
    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(429); // Too Many Requests
});

test('rate limit is per IP address', function () {
    $member = Member::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    // Make 5 attempts from first IP
    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ], ['REMOTE_ADDR' => '192.168.1.1']);
    }

    // 6th attempt from same IP should be blocked
    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ], ['REMOTE_ADDR' => '192.168.1.1']);

    $response->assertStatus(429);

    // But attempt from different IP should work
    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ], ['REMOTE_ADDR' => '192.168.1.2']);

    $response->assertStatus(401); // Not rate limited, just wrong password
});
