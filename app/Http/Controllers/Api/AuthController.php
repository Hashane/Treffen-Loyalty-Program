<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateMemberAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\Api\V1\MemberResource;
use App\Models\OauthConnection;
use App\Services\FacebookOAuthService;
use App\Services\GoogleOAuthService;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
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

        $token = $member->createToken('auth-token')->plainTextToken;

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
        return response()->success(new MemberResource($request->user()->load('membershipTier', 'oauthConnections')));

    }

    public function oauthConnections(Request $request)
    {
        return response()->success([
            'connections' => $request->user()->oauthConnections()->get(['id', 'provider', 'avatar', 'created_at']),
        ]);
    }

    public function unlinkOAuthProvider(Request $request, OauthConnection $connection)
    {
        if (! $connection) {
            return response()->error('OAuth connection not found', 404);
        }

        // Ensure member has either a password or another OAuth connection
        $hasPassword = ! empty($request->user()->password);
        $otherConnectionsCount = $request->user()->oauthConnections()
            ->where('id', '!=', $connection->id)
            ->count();

        if (! $hasPassword && $otherConnectionsCount === 0) {
            return response()->error('Cannot unlink last authentication method. Please set a password first.', 422);
        }

        $connection->delete();

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
}
