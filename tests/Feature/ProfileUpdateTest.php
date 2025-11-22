<?php

use App\Enums\Members\IdType;
use App\Enums\Members\PreferredCommunication;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated member can update their profile', function () {
    $member = Member::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'phone' => '12345678',
    ]);

    $response = $this->actingAs($member, 'sanctum')
        ->patchJson('/api/profile', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'phone' => '87654321',
        ]);

    $response->assertSuccessful();

    $member->refresh();
    expect($member->first_name)->toBe('Jane');
    expect($member->last_name)->toBe('Smith');
    expect($member->phone)->toBe('87654321');
});

test('unauthenticated user cannot update profile', function () {
    $response = $this->patchJson('/api/profile', [
        'first_name' => 'Jane',
    ]);

    $response->assertUnauthorized();
});

test('member can update only specific fields', function () {
    $member = Member::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'phone' => '12345678',
    ]);

    $response = $this->actingAs($member, 'sanctum')
        ->patchJson('/api/profile', [
            'first_name' => 'Jane',
        ]);

    $response->assertSuccessful();

    $member->refresh();
    expect($member->first_name)->toBe('Jane');
    expect($member->last_name)->toBe('Doe');
    expect($member->phone)->toBe('12345678');
});

test('member can update qatar id or passport', function () {
    $member = Member::factory()->create([
        'qatar_id_or_passport' => null,
        'id_type' => null,
    ]);

    $response = $this->actingAs($member, 'sanctum')
        ->patchJson('/api/profile', [
            'qatar_id_or_passport' => '12345678901',
            'id_type' => 'QATAR_ID',
        ]);

    $response->assertSuccessful();

    $member->refresh();
    expect($member->qatar_id_or_passport)->toBe('12345678901');
    expect($member->id_type)->toBe(IdType::QATAR_ID);
});

test('member can update date of birth', function () {
    $member = Member::factory()->create([
        'date_of_birth' => null,
    ]);

    $response = $this->actingAs($member, 'sanctum')
        ->patchJson('/api/profile', [
            'date_of_birth' => '1990-01-01',
        ]);

    $response->assertSuccessful();

    $member->refresh();
    expect($member->date_of_birth->format('Y-m-d'))->toBe('1990-01-01');
});

test('member can update preferred communication', function () {
    $member = Member::factory()->create([
        'preferred_communication' => PreferredCommunication::EMAIL,
    ]);

    $response = $this->actingAs($member, 'sanctum')
        ->patchJson('/api/profile', [
            'preferred_communication' => 'SMS',
        ]);

    $response->assertSuccessful();

    $member->refresh();
    expect($member->preferred_communication)->toBe(PreferredCommunication::SMS);
});

test('first name is required when provided', function () {
    $member = Member::factory()->create();

    $response = $this->actingAs($member, 'sanctum')
        ->patchJson('/api/profile', [
            'first_name' => '',
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('first_name');
});

test('last name is required when provided', function () {
    $member = Member::factory()->create();

    $response = $this->actingAs($member, 'sanctum')
        ->patchJson('/api/profile', [
            'last_name' => '',
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('last_name');
});

test('first name cannot exceed 50 characters', function () {
    $member = Member::factory()->create();

    $response = $this->actingAs($member, 'sanctum')
        ->patchJson('/api/profile', [
            'first_name' => str_repeat('a', 51),
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('first_name');
});

test('last name cannot exceed 50 characters', function () {
    $member = Member::factory()->create();

    $response = $this->actingAs($member, 'sanctum')
        ->patchJson('/api/profile', [
            'last_name' => str_repeat('a', 51),
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('last_name');
});

test('phone cannot exceed 20 characters', function () {
    $member = Member::factory()->create();

    $response = $this->actingAs($member, 'sanctum')
        ->patchJson('/api/profile', [
            'phone' => str_repeat('1', 21),
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('phone');
});

test('qatar id or passport cannot exceed 50 characters', function () {
    $member = Member::factory()->create();

    $response = $this->actingAs($member, 'sanctum')
        ->patchJson('/api/profile', [
            'qatar_id_or_passport' => str_repeat('a', 51),
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('qatar_id_or_passport');
});

test('id type must be valid enum value', function () {
    $member = Member::factory()->create();

    $response = $this->actingAs($member, 'sanctum')
        ->patchJson('/api/profile', [
            'id_type' => 'INVALID_TYPE',
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('id_type');
});

test('preferred communication must be valid enum value', function () {
    $member = Member::factory()->create();

    $response = $this->actingAs($member, 'sanctum')
        ->patchJson('/api/profile', [
            'preferred_communication' => 'INVALID_TYPE',
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('preferred_communication');
});

test('date of birth must be before today', function () {
    $member = Member::factory()->create();

    $response = $this->actingAs($member, 'sanctum')
        ->patchJson('/api/profile', [
            'date_of_birth' => now()->addDay()->format('Y-m-d'),
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('date_of_birth');
});

test('date of birth must be a valid date', function () {
    $member = Member::factory()->create();

    $response = $this->actingAs($member, 'sanctum')
        ->patchJson('/api/profile', [
            'date_of_birth' => 'invalid-date',
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('date_of_birth');
});

test('profile update returns updated member data', function () {
    $member = Member::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    $response = $this->actingAs($member, 'sanctum')
        ->patchJson('/api/profile', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);

    $response->assertSuccessful();
    $response->assertJsonPath('data.first_name', 'Jane');
    $response->assertJsonPath('data.last_name', 'Smith');
});

test('member can clear optional fields', function () {
    $member = Member::factory()->create([
        'phone' => '12345678',
        'qatar_id_or_passport' => '12345678901',
    ]);

    $response = $this->actingAs($member, 'sanctum')
        ->patchJson('/api/profile', [
            'phone' => null,
            'qatar_id_or_passport' => null,
        ]);

    $response->assertSuccessful();

    $member->refresh();
    expect($member->phone)->toBeNull();
    expect($member->qatar_id_or_passport)->toBeNull();
});

test('all updatable fields can be updated simultaneously', function () {
    $member = Member::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'phone' => '12345678',
        'qatar_id_or_passport' => null,
        'id_type' => null,
        'date_of_birth' => null,
        'preferred_communication' => PreferredCommunication::EMAIL,
    ]);

    $response = $this->actingAs($member, 'sanctum')
        ->patchJson('/api/profile', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'phone' => '87654321',
            'qatar_id_or_passport' => '12345678901',
            'id_type' => 'QATAR_ID',
            'date_of_birth' => '1990-01-01',
            'preferred_communication' => 'SMS',
        ]);

    $response->assertSuccessful();

    $member->refresh();
    expect($member->first_name)->toBe('Jane');
    expect($member->last_name)->toBe('Smith');
    expect($member->phone)->toBe('87654321');
    expect($member->qatar_id_or_passport)->toBe('12345678901');
    expect($member->id_type)->toBe(IdType::QATAR_ID);
    expect($member->date_of_birth->format('Y-m-d'))->toBe('1990-01-01');
    expect($member->preferred_communication)->toBe(PreferredCommunication::SMS);
});
