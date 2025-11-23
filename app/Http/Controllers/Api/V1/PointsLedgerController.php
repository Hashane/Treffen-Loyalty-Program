<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PointsLedgerResource;
use App\Models\PointsLedger;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PointsLedgerController extends Controller
{
    public function myPointsHistory(Request $request): AnonymousResourceCollection
    {
        $member = $request->user();

        $pointsLedger = PointsLedger::where('member_id', $member->id)
            ->with(['transaction', 'redemption'])
            ->latest('created_at')
            ->paginate();

        return PointsLedgerResource::collection($pointsLedger);
    }
}
