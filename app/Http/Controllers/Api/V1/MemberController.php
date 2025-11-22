<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class MemberController extends Controller
{
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
     * Handle email verification.
     */
    public function verifyEmail(Request $request)
    {
        $member = Member::findOrFail($request->route('id'));

        // Check if the verification link is valid
        if ($member->hasVerifiedEmail()) {
            return response()->error('Your email is already verified');
        }

        // Mark the member's email as verified
        if ($member->markEmailAsVerified()) {
            event(new Verified($member));
        }

        return response()->success(null, 'Your email has been verified');
    }
}
