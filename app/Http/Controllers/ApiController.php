<?php

namespace App\Http\Controllers;

use App\Models\BorrowHistory;
use App\Models\BorrowRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function checkUserChanges(Request $request)
    {
        $user = Auth::user();

        $pendingRequestsCount = $user->borrowRequests()
            ->where('status', 'pending')
            ->count();

        $approvedRequestsCount = $user->borrowRequests()
            ->where('status', 'accepted')
            ->count();

        return response()->json([
            'pending_requests' => $pendingRequestsCount,
            'approved_requests' => $approvedRequestsCount,
        ]);
    }

    public function checkResourceOfficerChanges(Request $request)
    {
        $approvedRequestsCount = BorrowRequest::where('status', 'accepted')->count();
        $pendingReturnsCount = BorrowHistory::where('return_status', 'pending')->count();

        return response()->json([
            'approved_requests' => $approvedRequestsCount,
            'pending_returns' => $pendingReturnsCount,
        ]);
    }

    public function checkAdminChanges(Request $request)
    {
        $pendingRequestsCount = BorrowRequest::where('status', 'pending')->count();

        return response()->json([
            'pending_requests' => $pendingRequestsCount,
        ]);
    }
}