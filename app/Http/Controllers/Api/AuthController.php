<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateMemberAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
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

        return response()->json([
            'member' => $member,
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $request->validated($request->all());

        if (! Auth::guard('member')->attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $member = Auth::guard('member')->user();
        $token = $member->createToken('auth-token')->plainTextToken;

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }

    public function me(Request $request)
    {
        return response()->json([
            'member' => $request->user(),
            'oauth_connections' => $request->user()->oauthConnections()->get(['id', 'provider', 'avatar', 'created_at']),
        ]);
    }

    public function oauthConnections(Request $request)
    {
        return response()->json([
            'connections' => $request->user()->oauthConnections()->get(['id', 'provider', 'avatar', 'created_at']),
        ]);
    }

    public function unlinkOAuthProvider(Request $request, string $provider)
    {
        $connection = $request->user()->oauthConnections()
            ->where('provider', $provider)
            ->first();

        if (! $connection) {
            return response()->json([
                'message' => 'OAuth connection not found',
            ], 404);
        }

        // Ensure member has either a password or another OAuth connection
        $hasPassword = ! empty($request->user()->password);
        $otherConnectionsCount = $request->user()->oauthConnections()
            ->where('id', '!=', $connection->id)
            ->count();

        if (! $hasPassword && $otherConnectionsCount === 0) {
            return response()->json([
                'message' => 'Cannot unlink last authentication method. Please set a password first.',
            ], 422);
        }

        $connection->delete();

        return response()->json([
            'message' => 'OAuth connection unlinked successfully',
        ]);
    }

    public function redirectToGoogle(GoogleOAuthService $service)
    {
        return response()->json(['url' => $service->getRedirectUrl()]);
    }

    public function handleGoogleCallback(GoogleOAuthService $service)
    {
        $result = $service->handleCallback();

        return response()->json($result);
    }

    public function redirectToFacebook(FacebookOAuthService $service)
    {
        return response()->json(['url' => $service->getRedirectUrl()]);
    }

    public function handleFacebookCallback(FacebookOAuthService $service)
    {
        $result = $service->handleCallback();

        return response()->json($result);
    }
}
