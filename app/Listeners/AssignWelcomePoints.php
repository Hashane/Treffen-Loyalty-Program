<?php

namespace App\Listeners;

use App\Enums\PointsLedger\PointsType;
use App\Events\MemberRegistered;
use App\Services\PointsLedgerService;

class AssignWelcomePoints
{
    /**
     * Create the event listener.
     */
    public function __construct(private PointsLedgerService $pointsLedgerService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MemberRegistered $event): void
    {
        $member = $event->member;

        // Todo push points to admin settings
        if (! $member->pointsLedger()->where('points_type', PointsType::REGISTRATION_BONUS)->exists()) {
            $this->pointsLedgerService->addPoints($member->id, 100, PointsType::REGISTRATION_BONUS, 'Welcome bonus points');
        }
    }
}
