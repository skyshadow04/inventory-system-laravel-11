<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserSession;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    /**
     * Show all active sessions for current user
     */
    public function index()
    {
        $user = Auth::user();
        $sessions = UserSession::where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->orderBy('last_activity', 'desc')
            ->get();

        $currentSessionId = session()->getId();

        return view('sessions.index', [
            'sessions' => $sessions,
            'currentSessionId' => $currentSessionId,
        ]);
    }

    /**
     * Revoke a specific session
     */
    public function revoke($sessionId)
    {
        $session = UserSession::find($sessionId);

        // Verify user owns this session
        if ($session->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action');
        }

        // If revoking current session, logout
        if (session()->getId() === $session->session_id) {
            Auth::logout();
            $session->delete();
            session()->invalidate();
            return redirect('/login')->with('success', 'You have been logged out successfully');
        }

        // Otherwise just delete the session record
        $session->delete();

        return redirect()->back()->with('success', 'Session terminated successfully');
    }

    /**
     * Revoke all other sessions (keep current session)
     */
    public function revokeAllOthers()
    {
        $user = Auth::user();
        $currentSessionId = session()->getId();

        UserSession::where('user_id', $user->id)
            ->where('session_id', '!=', $currentSessionId)
            ->delete();

        return redirect()->back()->with('success', 'All other sessions have been terminated');
    }

    /**
     * Get session info for dashboard/profile
     */
    public function getSessionInfo()
    {
        $user = Auth::user();
        $currentSession = UserSession::where('user_id', $user->id)
            ->where('session_id', session()->getId())
            ->first();

        return [
            'login_time' => $currentSession?->created_at->diffForHumans(),
            'last_activity' => $currentSession?->last_activity->diffForHumans(),
            'expires_at' => $currentSession?->expires_at,
            'device' => $currentSession?->getDeviceName(),
            'ip_address' => $currentSession?->ip_address,
            'minutes_remaining' => $currentSession?->minutesRemaining(),
        ];
    }
}
