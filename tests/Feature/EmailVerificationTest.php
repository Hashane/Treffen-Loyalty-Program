<?php

use App\Enums\VerificationCodeType;
use App\Mail\EmailVerificationMail;
use App\Models\Member;
use App\Models\VerificationCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('authenticated member can request verification code', function () {
    Mail::fake();

    $member = Member::factory()->create([
        'email' => 'test@example.com',
        'email_verified_at' => null,
    ]);

    $response = $this->actingAs($member, 'sanctum')
        ->postJson('/api/email/send-verification-code');

    $response->assertSuccessful();

    expect(VerificationCode::where('identifier', $member->email)->exists())->toBeTrue();

    Mail::assertSent(EmailVerificationMail::class, function ($mail) use ($member) {
        return $mail->hasTo($member->email) &&
               $mail->member->id === $member->id;
    });
});

test('unauthenticated user cannot request verification code', function () {
    $response = $this->postJson('/api/email/send-verification-code');

    $response->assertUnauthorized();
});

test('member with verified email cannot request new code', function () {
    $member = Member::factory()->create([
        'email' => 'test@example.com',
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($member, 'sanctum')
        ->postJson('/api/email/send-verification-code');

    $response->assertStatus(422);
});

test('old verification codes are deleted when sending new code', function () {
    $member = Member::factory()->create([
        'email' => 'test@example.com',
        'email_verified_at' => null,
    ]);

    // Create old verification code
    VerificationCode::create([
        'member_id' => $member->id,
        'identifier' => $member->email,
        'type' => VerificationCodeType::EmailVerification,
        'code' => Hash::make('123456'),
        'expires_at' => now()->addMinutes(10),
        'attempts' => 0,
    ]);

    expect(VerificationCode::where('identifier', $member->email)->count())->toBe(1);

    $this->actingAs($member, 'sanctum')
        ->postJson('/api/email/send-verification-code');

    // Should still be 1, not 2
    expect(VerificationCode::where('identifier', $member->email)->count())->toBe(1);
});

test('member can verify email with correct code', function () {
    $member = Member::factory()->create([
        'email' => 'test@example.com',
        'email_verified_at' => null,
    ]);

    $code = '123456';
    VerificationCode::create([
        'member_id' => $member->id,
        'identifier' => $member->email,
        'type' => VerificationCodeType::EmailVerification,
        'code' => Hash::make($code),
        'expires_at' => now()->addMinutes(10),
        'attempts' => 0,
    ]);

    $response = $this->actingAs($member, 'sanctum')
        ->postJson('/api/email/verify', [
            'email' => $member->email,
            'code' => $code,
        ]);

    $response->assertSuccessful();

    $member->refresh();
    expect($member->email_verified_at)->not->toBeNull();

    // Verification code should be deleted after use
    expect(VerificationCode::where('identifier', $member->email)->exists())->toBeFalse();
});

test('cannot verify email with invalid code', function () {
    $member = Member::factory()->create([
        'email' => 'test@example.com',
        'email_verified_at' => null,
    ]);

    VerificationCode::create([
        'member_id' => $member->id,
        'identifier' => $member->email,
        'type' => VerificationCodeType::EmailVerification,
        'code' => Hash::make('123456'),
        'expires_at' => now()->addMinutes(10),
        'attempts' => 0,
    ]);

    $response = $this->actingAs($member, 'sanctum')
        ->postJson('/api/email/verify', [
            'email' => $member->email,
            'code' => '999999',
        ]);

    $response->assertStatus(400);

    $member->refresh();
    expect($member->email_verified_at)->toBeNull();
});

test('cannot verify email with expired code', function () {
    $member = Member::factory()->create([
        'email' => 'test@example.com',
        'email_verified_at' => null,
    ]);

    VerificationCode::create([
        'member_id' => $member->id,
        'identifier' => $member->email,
        'type' => VerificationCodeType::EmailVerification,
        'code' => Hash::make('123456'),
        'expires_at' => now()->subMinutes(1),
        'attempts' => 0,
    ]);

    $response = $this->actingAs($member, 'sanctum')
        ->postJson('/api/email/verify', [
            'email' => $member->email,
            'code' => '123456',
        ]);

    $response->assertStatus(400);

    $member->refresh();
    expect($member->email_verified_at)->toBeNull();
});

test('cannot verify email after max attempts', function () {
    $member = Member::factory()->create([
        'email' => 'test@example.com',
        'email_verified_at' => null,
    ]);

    VerificationCode::create([
        'member_id' => $member->id,
        'identifier' => $member->email,
        'type' => VerificationCodeType::EmailVerification,
        'code' => Hash::make('123456'),
        'expires_at' => now()->addMinutes(10),
        'attempts' => 3,
    ]);

    $response = $this->actingAs($member, 'sanctum')
        ->postJson('/api/email/verify', [
            'email' => $member->email,
            'code' => '123456',
        ]);

    $response->assertStatus(400);

    $member->refresh();
    expect($member->email_verified_at)->toBeNull();
});

test('attempts are incremented on invalid code', function () {
    $member = Member::factory()->create([
        'email' => 'test@example.com',
        'email_verified_at' => null,
    ]);

    VerificationCode::create([
        'member_id' => $member->id,
        'identifier' => $member->email,
        'type' => VerificationCodeType::EmailVerification,
        'code' => Hash::make('123456'),
        'expires_at' => now()->addMinutes(10),
        'attempts' => 0,
    ]);

    $this->actingAs($member, 'sanctum')
        ->postJson('/api/email/verify', [
            'email' => $member->email,
            'code' => '999999',
        ]);

    $verificationCode = VerificationCode::where('identifier', $member->email)->first();
    expect($verificationCode->attempts)->toBe(1);
});

test('send verification code endpoint is rate limited', function () {
    $member = Member::factory()->create([
        'email' => 'test@example.com',
        'email_verified_at' => null,
    ]);

    // Make 5 requests
    for ($i = 0; $i < 5; $i++) {
        $response = $this->actingAs($member, 'sanctum')
            ->postJson('/api/email/send-verification-code');

        $response->assertSuccessful();
    }

    // 6th request should be rate limited
    $response = $this->actingAs($member, 'sanctum')
        ->postJson('/api/email/send-verification-code');

    $response->assertStatus(429);
});

test('verify email endpoint is rate limited', function () {
    $member = Member::factory()->create([
        'email' => 'test@example.com',
        'email_verified_at' => null,
    ]);

    VerificationCode::create([
        'member_id' => $member->id,
        'identifier' => $member->email,
        'type' => VerificationCodeType::EmailVerification,
        'code' => Hash::make('123456'),
        'expires_at' => now()->addMinutes(10),
        'attempts' => 0,
    ]);

    // Make 5 requests
    for ($i = 0; $i < 5; $i++) {
        $response = $this->actingAs($member, 'sanctum')
            ->postJson('/api/email/verify', [
                'email' => $member->email,
                'code' => '999999',
            ]);

        $response->assertStatus(400);
    }

    // 6th request should be rate limited
    $response = $this->actingAs($member, 'sanctum')
        ->postJson('/api/email/verify', [
            'email' => $member->email,
            'code' => '999999',
        ]);

    $response->assertStatus(429);
});

test('validation fails with invalid email format', function () {
    $member = Member::factory()->create([
        'email_verified_at' => null,
    ]);

    VerificationCode::create([
        'member_id' => $member->id,
        'identifier' => $member->email,
        'type' => VerificationCodeType::EmailVerification,
        'code' => Hash::make('123456'),
        'expires_at' => now()->addMinutes(10),
        'attempts' => 0,
    ]);

    $response = $this->actingAs($member, 'sanctum')
        ->postJson('/api/email/verify', [
            'email' => 'invalid-email',
            'code' => '123456',
        ]);

    $response->assertStatus(422);
});

test('validation fails with code not 6 digits', function () {
    $member = Member::factory()->create([
        'email_verified_at' => null,
    ]);

    VerificationCode::create([
        'member_id' => $member->id,
        'identifier' => $member->email,
        'type' => VerificationCodeType::EmailVerification,
        'code' => Hash::make('123456'),
        'expires_at' => now()->addMinutes(10),
        'attempts' => 0,
    ]);

    $response = $this->actingAs($member, 'sanctum')
        ->postJson('/api/email/verify', [
            'email' => $member->email,
            'code' => '12345',
        ]);

    $response->assertStatus(422);
});
