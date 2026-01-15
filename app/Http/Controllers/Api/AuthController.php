<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateMemberAction;
use App\Enums\VerificationCodeType;
use App\Events\MemberRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\Api\V1\MemberResource;
use App\Http\Resources\Api\V1\MemberHomeResource;
use App\Mail\PasswordResetOtpMail;
use App\Models\Member;
use App\Models\OauthConnection;
use App\Models\VerificationCode;
use App\Services\FacebookOAuthService;
use App\Services\GoogleOAuthService;
use App\Traits\VerifiesCode;
use Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mail;

class AuthController extends Controller
{
    use VerifiesCode;

    public function __construct(protected CreateMemberAction $createMember) {}

    public function register(RegisterRequest $request)
    {
        $member = $this->createMember->execute([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($member));
        event(new MemberRegistered($member));

        $token = $member->createToken(
            'auth-token for '.$member->email,
            ['*'],
            now()->addDay()
        )->plainTextToken;

        return response()->success([
            'member' => new MemberResource($member),
            'access_token' => $token,
        ], 'Registered Successfully', 201);
    }

    public function login(LoginRequest $request)
    {
        $request->validated($request->all());

        if (! Auth::guard('member')->attempt($request->only('email', 'password'))) {
            return response()->success(null, 'Invalid credentials', 401);
        }

        $member = Auth::guard('member')->user();
        $token = $member->createToken('auth-token')->plainTextToken;

        return response()->success(
            [
                'member' => new MemberResource($member),
                'access_token' => $token,
            ], 'Successfully logged in'
        );
    }

    public function me(Request $request)
    {
        return response()->success(
            new MemberHomeResource($request->user()->load('membershipTier')),
            'Profile fetched successfully'
        );
    }

    public function oauthConnections(Request $request)
    {
        return response()->success([
            'connections' => $request->user()->oauthConnections()->get(['id', 'provider', 'avatar', 'created_at']),
        ]);
    }

    public function unlinkOAuthProvider(Request $request, OauthConnection $oauthConnection)
    {
        // Ensure the connection belongs to the authenticated user
        if ($oauthConnection->member_id !== $request->user()->id) {
            return response()->error('OAuth connection not found', 404);
        }

        // Ensure member has either a password or another OAuth connection
        $hasPassword = ! empty($request->user()->password);
        $otherConnectionsCount = $request->user()->oauthConnections()
            ->where('id', '!=', $oauthConnection->id)
            ->count();

        if (! $hasPassword && $otherConnectionsCount === 0) {
            return response()->error('Cannot unlink last authentication method. Please set a password first.', 422);
        }

        $oauthConnection->delete();

        return response()->success(null, 'OAuth connection unlinked successfully');
    }

    public function redirectToGoogle(GoogleOAuthService $service)
    {
        return response()->success(['url' => $service->getRedirectUrl()]);
    }

    public function handleGoogleCallback(GoogleOAuthService $service)
    {
        $result = $service->handleCallback();

        return response()->success($result);
    }

    public function redirectToFacebook(FacebookOAuthService $service)
    {
        return response()->success(['url' => $service->getRedirectUrl()]);
    }

    public function handleFacebookCallback(FacebookOAuthService $service)
    {
        $result = $service->handleCallback();

        return response()->success($result);
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $member = Member::where('email', $request->email)->first();

        if (! $member) {
            return response()->error('We could not find an account with that email address.', 404);
        }

        // Delete any existing password reset codes for this member
        VerificationCode::where('identifier', $request->email)
            ->where('type', VerificationCodeType::PasswordReset)
            ->delete();

        $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        VerificationCode::create([
            'member_id' => $member->id,
            'identifier' => $request->email,
            'type' => VerificationCodeType::PasswordReset,
            'code' => Hash::make($otpCode),
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
        ]);

        Mail::to($member->email)->send(new PasswordResetOtpMail($member, $otpCode));

        return response()->success(null, 'Password reset code sent successfully.');
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $verificationCode = $this->findAndVerifyCode($request->email, $request->code, VerificationCodeType::PasswordReset);

        $member = Member::where('email', $request->email)->first();

        if (! $member) {
            return response()->error('Member not found.', 404);
        }

        $member->forceFill([
            'password' => Hash::make($request->password),
        ])->setRememberToken(Str::random(60));

        $member->save();

        // Delete the used verification code
        $verificationCode->delete();

        event(new PasswordReset($member));

        return response()->success(null, 'Password has been reset successfully.');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->success(null, 'Logged out successfully.');
    }
}
