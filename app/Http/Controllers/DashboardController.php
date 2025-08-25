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

        // --- Métricas
        $stats = [
            'connections' => SocialConnection::where('user_id', $user->id)->count(),

            'scheduled_today' => Post::where('user_id', $user->id)
                ->where('status', 'scheduled')
                ->whereBetween('scheduled_for', [now()->startOfDay(), now()->endOfDay()])
                ->count(),

            // Cuenta cola = queued + scheduled
            'queue_pending' => Post::where('user_id', $user->id)
                ->whereIn('status', ['queued', 'scheduled'])
                ->count(),

            'published_week' => Post::where('user_id', $user->id)
                ->where('status', 'published')
                ->where('published_at', '>=', now()->subDays(7))
                ->count(),
        ];

        // --- Próximas publicaciones (programadas primero, luego en cola), 5 items
        $upcomingRaw = Post::where('user_id', $user->id)
            ->whereIn('status', ['queued','scheduled'])
            ->with('targets')
            ->orderByRaw('CASE WHEN scheduled_for IS NULL THEN 1 ELSE 0 END')
            ->orderBy('scheduled_for')
            ->limit(5)
            ->get();

        // Mapear a lo que la vista espera
        $upcoming = $upcomingRaw->map(function (Post $p) {
            $when = $p->scheduled_for
                ? $p->scheduled_for->format('Y-m-d H:i')
                : 'Sin fecha (cola)';

            // Resumen de destinos (ej: "Reddit: r/test · Discord")
            $target = $p->targets->map(function ($t) {
                if ($t->provider === 'reddit') {
                    return 'Reddit: r/' . ($t->reddit_subreddit ?? '—');
                }
                if ($t->provider === 'discord') {
                    return 'Discord';
                }
                return ucfirst((string)$t->provider);
            })->implode(' · ');

            return [
                'title'  => $p->title ?: 'Sin título',
                'when'   => $when,
                'target' => $target ?: '—',
                'status' => $p->status,
            ];
        })->toArray();

        // --- ¿Falta activar 2FA?
        $needs2fa = is_null($user->two_factor_confirmed_at);

        return view('dashboard', compact('stats', 'upcoming', 'needs2fa'));
    }
}
