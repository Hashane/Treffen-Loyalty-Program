<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\VerificationCodeType;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Http\Resources\Api\V1\MemberResource;
use App\Mail\EmailVerificationMail;
use App\Models\Member;
use App\Models\VerificationCode;
use App\VerifiesCode;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mail;

class MemberController extends Controller
{
    use VerifiesCode;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Member $member)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        //
    }

    /**
     * Update the authenticated member's profile.
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $member = $request->user();

        $member->update($request->validated());

        return response()->success(
            new MemberResource($member->load('membershipTier', 'oauthConnections')),
            'Profile updated successfully'
        );
    }

    public function sendVerificationCode(Request $request)
    {
        $member = $request->user();

        if ($member->hasVerifiedEmail()) {
            return response()->error('Your email is already verified', 422);
        }

        // Delete any existing email verification codes for this member
        VerificationCode::where('identifier', $member->email)
            ->where('type', VerificationCodeType::EmailVerification)
            ->delete();

        $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        VerificationCode::create([
            'member_id' => $member->id,
            'identifier' => $member->email,
            'type' => VerificationCodeType::EmailVerification,
            'code' => Hash::make($otpCode),
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
        ]);

        Mail::to($member->email)->send(new EmailVerificationMail($member, $otpCode));

        return response()->success(null, 'Email verification code sent successfully.');
    }

    /**
     * Handle email verification.
     */
    public function verifyEmail(VerifyEmailRequest $request)
    {
        $verificationCode = $this->findAndVerifyCode($request->email, $request->code, VerificationCodeType::EmailVerification);

        $member = Member::where('email', $request->email)->first();

        if (! $member) {
            return response()->error('Member not found.', 404);
        }

        if ($member->hasVerifiedEmail()) {
            return response()->error('Your email is already verified', 422);
        }

        // Mark the member's email as verified
        if ($member->markEmailAsVerified()) {
            event(new Verified($member));
        }

        $verificationCode->delete();

        return response()->success(null, 'Your email has been verified');
    }
}
