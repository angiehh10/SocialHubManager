<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return view('dashboard', [
        'stats' => [
            'connections'     => auth()->user()->socialConnections()->count(),
            'scheduled_today' => 0,  // calcula desde tu modelo de horarios
            'queue_pending'   => 0,  // cuenta en cola
            'published_week'  => 0,  // últimos 7 días
        ],
        'upcoming' => [
            // ['title' => 'Post Reddit', 'when' => 'Hoy 12:00', 'target' => 'r/miSubreddit', 'status' => 'Programada'],
        ],
    ]);
    
    }
}
