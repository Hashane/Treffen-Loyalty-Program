<?php

use App\Models\Member;
use App\Models\OauthConnection;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated member can view their profile with oauth connections', function () {
    $member = Member::factory()->create();
    OauthConnection::factory()->google()->create(['member_id' => $member->id]);
    OauthConnection::factory()->facebook()->create(['member_id' => $member->id]);

    $response = $this->actingAs($member, 'sanctum')->getJson('/api/me');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'member_number',
                'referral_code',
                'first_name',
                'last_name',
                'full_name',
                'email',
                'connected_accounts' => [
                    '*' => ['member_id', 'provider', 'provider_avatar', 'created_at'],
                ],
            ],
        ]);

    expect($response->json('data.connected_accounts'))->toHaveCount(2);
});

test('authenticated member can view all oauth connections', function () {
    $member = Member::factory()->create();
    $googleConnection = OauthConnection::factory()->google()->create(['member_id' => $member->id]);
    $facebookConnection = OauthConnection::factory()->facebook()->create(['member_id' => $member->id]);

    $response = $this->actingAs($member, 'sanctum')->getJson('/api/oauth/connections');

    $response->assertSuccessful()
        ->assertJsonCount(2, 'data.connections')
        ->assertJsonFragment(['provider' => 'google'])
        ->assertJsonFragment(['provider' => 'facebook']);
});

test('member can unlink oauth provider', function () {
    $member = Member::factory()->create([
        'password' => bcrypt('password'),
    ]);
    $connection = OauthConnection::factory()->google()->create(['member_id' => $member->id]);

    $response = $this->actingAs($member, 'sanctum')
        ->deleteJson("/api/oauth/connections/{$connection->id}");

    $response->assertSuccessful();

    $this->assertDatabaseMissing('oauth_connections', [
        'id' => $connection->id,
    ]);
});

test('member cannot unlink non-existent oauth provider', function () {
    $member = Member::factory()->create();

    $response = $this->actingAs($member, 'sanctum')
        ->deleteJson('/api/oauth/connections/99999');

    $response->assertNotFound();
});

test('member cannot unlink last authentication method', function () {
    $member = Member::factory()->create([
        'password' => null,
    ]);
    $connection = OauthConnection::factory()->google()->create(['member_id' => $member->id]);

    $response = $this->actingAs($member, 'sanctum')
        ->deleteJson("/api/oauth/connections/{$connection->id}");

    $response->assertStatus(422);

    $this->assertDatabaseHas('oauth_connections', [
        'member_id' => $member->id,
        'provider' => 'google',
    ]);
});

test('member can unlink oauth provider if they have another oauth connection', function () {
    $member = Member::factory()->create([
        'password' => null,
    ]);
    $googleConnection = OauthConnection::factory()->google()->create(['member_id' => $member->id]);
    OauthConnection::factory()->facebook()->create(['member_id' => $member->id]);

    $response = $this->actingAs($member, 'sanctum')
        ->deleteJson("/api/oauth/connections/{$googleConnection->id}");

    $response->assertSuccessful();

    $this->assertDatabaseMissing('oauth_connections', [
        'member_id' => $member->id,
        'provider' => 'google',
    ]);

    $this->assertDatabaseHas('oauth_connections', [
        'member_id' => $member->id,
        'provider' => 'facebook',
    ]);
});

test('unauthenticated user cannot view oauth connections', function () {
    $response = $this->getJson('/api/oauth/connections');

    $response->assertUnauthorized();
});

test('unauthenticated user cannot unlink oauth provider', function () {
    $response = $this->deleteJson('/api/oauth/connections/1');

    $response->assertUnauthorized();
});

test('member only sees their own oauth connections', function () {
    $member1 = Member::factory()->create();
    $member2 = Member::factory()->create();

    OauthConnection::factory()->google()->create(['member_id' => $member1->id]);
    OauthConnection::factory()->facebook()->create(['member_id' => $member2->id]);

    $response = $this->actingAs($member1, 'sanctum')->getJson('/api/oauth/connections');

    $response->assertSuccessful()
        ->assertJsonCount(1, 'data.connections')
        ->assertJsonFragment(['provider' => 'google'])
        ->assertJsonMissing(['provider' => 'facebook']);
});
