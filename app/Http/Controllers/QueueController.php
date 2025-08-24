<?php

namespace App\Http\Controllers;

use App\Jobs\PublishPostJob;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class QueueController extends Controller
{
     public function index()
    {
        $pending   = Post::where('user_id', Auth::id())->pendingLike()->orderBy('scheduled_for')->orderBy('id')->get();
        $published = Post::where('user_id', Auth::id())->where('status','published')->orderByDesc('published_at')->limit(50)->get();

        return view('queue.index', compact('pending','published'));
    }

    public function cancel(Post $queuedPost)
    {
        abort_unless($queuedPost->user_id === Auth::id(), 403);
        if (in_array($queuedPost->status, ['queued','scheduled'])) {
            $queuedPost->targets()->update(['status' => 'failed', 'error' => 'Cancelado por usuario']);
            $queuedPost->update(['status' => 'failed', 'error' => 'Cancelado por usuario']);
        }
        return back()->with('status','Publicación cancelada.');
    }

    public function sendNow(Post $queuedPost)
    {
        abort_unless($queuedPost->user_id === Auth::id(), 403);
        if (in_array($queuedPost->status, ['queued','scheduled'])) {
            dispatch(new PublishPostJob($queuedPost->id));
            return back()->with('status','Publicación enviada ahora.');
        }
        return back();
    }
}
