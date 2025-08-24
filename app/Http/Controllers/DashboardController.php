<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\SocialConnection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // --- Stats
        $stats = [
            'connections'     => SocialConnection::where('user_id', $user->id)->count(),
            'scheduled_today' => Post::where('user_id', $user->id)
                ->where('status', 'scheduled')
                ->whereDate('scheduled_for', Carbon::today())
                ->count(),
            'queue_pending'   => Post::where('user_id', $user->id)
                ->where('status', 'queued')
                ->count(),
            'published_week'  => Post::where('user_id', $user->id)
                ->where('status', 'published')
                ->where('published_at', '>=', now()->subDays(7))
                ->count(),
        ];

        // --- Próximas publicaciones (programadas primero, luego en cola)
        $upcoming = Post::where('user_id', $user->id)
            ->whereIn('status', ['queued','scheduled'])
            ->with('targets')
            // no nulas primero (programadas), luego por fecha
            ->orderByRaw('scheduled_for IS NULL, scheduled_for ASC')
            ->limit(5)
            ->get();

        // --- ¿Falta activar 2FA?
        $needs2fa = is_null($user->two_factor_confirmed_at);

        return view('dashboard', compact('stats', 'upcoming', 'needs2fa'));
    }
}
