<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    /**
     * Display a list of all users with their verification status.
     */
    public function userManagement(Request $request)
    {
        $status = $request->query('status', 'all'); // all, verified, pending

        $query = User::query();

        if ($status === 'verified') {
            $query->where('is_verified', true);
        } elseif ($status === 'pending') {
            $query->where('is_verified', false);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('superadmin.userManagement', compact('users', 'status'));
    }

    /**
     * Approve (verify) a user account.
     */
    public function approveUser(User $user)
    {
        // Prevent approving already verified users
        if ($user->is_verified) {
            return redirect()->route('superadmin.user-management')->with('info', 'User is already verified.');
        }

        $user->update(['is_verified' => true]);

        return redirect()->route('superadmin.user-management')->with('success', "User '{$user->name}' has been approved and can now login.");
    }

    /**
     * Reject (deny) a user account (soft delete by marking as unverified).
     */
    public function rejectUser(User $user)
    {
        if (!$user->is_verified) {
            return redirect()->route('superadmin.user-management')->with('info', 'User is already pending approval.');
        }

        $user->update(['is_verified' => false]);

        return redirect()->route('superadmin.user-management')->with('success', "User '{$user->name}' has been deactivated.");
    }

    /**
     * Deactivate user account (prevent login).
     */
    public function deactivateUser(User $user)
    {
        $user->update(['is_verified' => false]);

        return redirect()->route('superadmin.user-management')->with('success', "User '{$user->name}' account has been deactivated successfully.");
    }

    /**
     * Reactivate user account (allow login again).
     */
    public function reactivateUser(User $user)
    {
        $user->update(['is_verified' => true]);

        return redirect()->route('superadmin.user-management')->with('success', "User '{$user->name}' account has been reactivated.");
    }

    /**
     * Display the super admin dashboard.
     */
    public function dashboard()
    {
        $stats = $this->getStatistics();
        $pendingUsers = User::where('is_verified', false)->get();
        $recentUsers = User::latest('created_at')->take(5)->get();

        return view('superadmin.dashboard', compact('stats', 'pendingUsers', 'recentUsers'));
    }

    /**
     * Get statistics about user verification.
     */
    public function getStatistics()
    {
        $totalUsers = User::count();
        $verifiedUsers = User::where('is_verified', true)->count();
        $pendingUsers = User::where('is_verified', false)->count();
        $adminUsers = User::where('is_superadmin', true)->count();

        return [
            'total' => $totalUsers,
            'verified' => $verifiedUsers,
            'pending' => $pendingUsers,
            'admins' => $adminUsers,
        ];
    }
}
