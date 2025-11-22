<?php

use App\Models\Member;
use App\Models\MembershipTier;
use App\Models\OauthConnection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->mockSocialiteUser = \Mockery::mock(SocialiteUser::class);

    // Create a default membership tier for OAuth registration tests
    MembershipTier::factory()->create([
        'tier_level' => 1,
    ]);
});

test('google redirect returns authorization url', function () {
    $response = $this->getJson('/api/auth/google/redirect');

    $response->assertSuccessful()
        ->assertJsonStructure(['url']);

    expect($response->json('url'))->toContain('google');
});

test('facebook redirect returns authorization url', function () {
    $response = $this->getJson('/api/auth/facebook/redirect');

    $response->assertSuccessful()
        ->assertJsonStructure(['url']);

    expect($response->json('url'))->toContain('facebook');
});

test('new user can register via google oauth', function () {
    $this->mockSocialiteUser
        ->shouldReceive('getId')->andReturn('123456789')
        ->shouldReceive('getEmail')->andReturn('newuser@example.com')
        ->shouldReceive('getName')->andReturn('John Doe')
        ->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

    $this->mockSocialiteUser->token = 'mock-token';
    $this->mockSocialiteUser->refreshToken = 'mock-refresh-token';

    Socialite::shouldReceive('driver->stateless->user')
        ->andReturn($this->mockSocialiteUser);

    $response = $this->getJson('/api/auth/google/callback');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'member' => ['id', 'first_name', 'last_name', 'email'],
            'token',
        ]);

    $this->assertDatabaseHas('members', [
        'email' => 'newuser@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    $this->assertDatabaseHas('oauth_connections', [
        'provider' => 'google',
        'provider_id' => '123456789',
        'avatar' => 'https://example.com/avatar.jpg',
    ]);
});

test('existing member can link google oauth', function () {
    $member = Member::factory()->create([
        'email' => 'existing@example.com',
    ]);

    $this->mockSocialiteUser
        ->shouldReceive('getId')->andReturn('987654321')
        ->shouldReceive('getEmail')->andReturn('existing@example.com')
        ->shouldReceive('getName')->andReturn('Jane Smith')
        ->shouldReceive('getAvatar')->andReturn('https://example.com/jane.jpg');

    $this->mockSocialiteUser->token = 'mock-token';
    $this->mockSocialiteUser->refreshToken = 'mock-refresh-token';

    Socialite::shouldReceive('driver->stateless->user')
        ->andReturn($this->mockSocialiteUser);

    $response = $this->getJson('/api/auth/google/callback');

    $response->assertSuccessful();

    $this->assertDatabaseHas('oauth_connections', [
        'member_id' => $member->id,
        'provider' => 'google',
        'provider_id' => '987654321',
    ]);

    expect(OauthConnection::where('member_id', $member->id)->count())->toBe(1);
});

test('member with existing oauth connection can login', function () {
    $member = Member::factory()->create();
    $oauthConnection = OauthConnection::factory()->google()->create([
        'member_id' => $member->id,
        'provider_id' => '111222333',
    ]);

    $this->mockSocialiteUser
        ->shouldReceive('getId')->andReturn('111222333')
        ->shouldReceive('getEmail')->andReturn($member->email)
        ->shouldReceive('getName')->andReturn($member->first_name.' '.$member->last_name)
        ->shouldReceive('getAvatar')->andReturn('https://example.com/updated.jpg');

    $this->mockSocialiteUser->token = 'new-token';
    $this->mockSocialiteUser->refreshToken = 'new-refresh-token';

    Socialite::shouldReceive('driver->stateless->user')
        ->andReturn($this->mockSocialiteUser);

    $response = $this->getJson('/api/auth/google/callback');

    $response->assertSuccessful()
        ->assertJsonPath('member.id', $member->id);

    $oauthConnection->refresh();

    expect($oauthConnection->avatar)->toBe('https://example.com/updated.jpg');
});

test('member can link both google and facebook', function () {
    $member = Member::factory()->create([
        'email' => 'multi@example.com',
    ]);

    OauthConnection::factory()->google()->create([
        'member_id' => $member->id,
        'provider_id' => 'google-123',
    ]);

    $this->mockSocialiteUser
        ->shouldReceive('getId')->andReturn('facebook-456')
        ->shouldReceive('getEmail')->andReturn('multi@example.com')
        ->shouldReceive('getName')->andReturn('Multi User')
        ->shouldReceive('getAvatar')->andReturn('https://facebook.com/avatar.jpg');

    $this->mockSocialiteUser->token = 'fb-token';
    $this->mockSocialiteUser->refreshToken = null;

    Socialite::shouldReceive('driver->stateless->user')
        ->andReturn($this->mockSocialiteUser);

    $response = $this->getJson('/api/auth/facebook/callback');

    $response->assertSuccessful();

    expect(OauthConnection::where('member_id', $member->id)->count())->toBe(2);

    $this->assertDatabaseHas('oauth_connections', [
        'member_id' => $member->id,
        'provider' => 'google',
    ]);

    $this->assertDatabaseHas('oauth_connections', [
        'member_id' => $member->id,
        'provider' => 'facebook',
    ]);
});

test('oauth callback handles errors gracefully', function () {
    Socialite::shouldReceive('driver->stateless->user')
        ->andThrow(new \Exception('OAuth provider error'));

    $response = $this->getJson('/api/auth/google/callback');

    $response->assertStatus(500)
        ->assertJson([
            'message' => 'Authentication failed',
            'error' => 'OAuth provider error',
        ]);
});

test('new user via oauth gets email verified automatically', function () {
    $this->mockSocialiteUser
        ->shouldReceive('getId')->andReturn('verified-user-123')
        ->shouldReceive('getEmail')->andReturn('verified@example.com')
        ->shouldReceive('getName')->andReturn('Verified User')
        ->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

    $this->mockSocialiteUser->token = 'mock-token';
    $this->mockSocialiteUser->refreshToken = null;

    Socialite::shouldReceive('driver->stateless->user')
        ->andReturn($this->mockSocialiteUser);

    $this->getJson('/api/auth/google/callback');

    $member = Member::where('email', 'verified@example.com')->first();

    expect($member->email_verified_at)->not->toBeNull();
});
