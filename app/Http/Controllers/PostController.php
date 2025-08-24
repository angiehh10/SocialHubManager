<?php

namespace App\Http\Controllers;

use App\Jobs\PublishPostJob;
use App\Models\Post;
use App\Models\PostTarget;
use App\Models\DiscordWebhook;  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PostController extends Controller
{
     public function create()
    {
                $discordWebhooks = DiscordWebhook::where('user_id', Auth::id())
                ->orderByDesc('id')
                ->get();

            return view('posts.create', compact('discordWebhooks'));
    }

        public function store(Request $r)
    {
            // 1) Validación
        $r->validate([
            'provider_choice' => 'required|in:reddit,discord',
            'mode'            => 'required|in:now,queue,scheduled',

            'title'         => 'nullable|string|max:300',
            'body'          => 'nullable|string',
            'scheduled_for' => 'nullable|date',

            // --- REDDIT ---
            'reddit.subreddit' => 'exclude_unless:provider_choice,reddit|required|string|max:80',
            'reddit.kind'      => 'exclude_unless:provider_choice,reddit|required|in:self,link',
            'link_url'         => 'exclude_unless:provider_choice,reddit|nullable|url|required_if:reddit.kind,link',

            // --- DISCORD ---
            // Puedes recibir un webhook guardado (por ID) o una URL pegada
            'discord.webhook_id'  => 'exclude_unless:provider_choice,discord|nullable|integer|exists:discord_webhooks,id',
            'discord.webhook_url' => 'exclude_unless:provider_choice,discord|nullable|url',
            'discord.message'     => 'exclude_unless:provider_choice,discord|required|string|max:2000',
        ], [
            'provider_choice.required' => 'Elige un destino: Reddit o Discord.',
        ]);

        // Requiere al menos webhook_id o webhook_url si eligió Discord
        if (
            $r->input('provider_choice') === 'discord'
            && ! $r->filled('discord.webhook_id')
            && ! $r->filled('discord.webhook_url')
        ) {
            return back()
                ->withErrors(['discord.webhook_id' => 'Selecciona un webhook guardado o pega una URL válida.'])
                ->withInput();
        }

        $user = Auth::user();
        $mode = $r->input('mode');

        // 2) Preparar campos del post según el destino
        $bodyForSave = $r->input('provider_choice') === 'discord'
            ? (string) $r->input('discord.message')
            : (string) $r->input('body');

        $titleForSave = $r->input('provider_choice') === 'reddit'
            ? (string) $r->input('title')
            : null;

        // 3) Crear Post
        $post = Post::create([
            'user_id'       => $user->id,
            'title'         => $titleForSave,
            'body'          => $bodyForSave,
            'link_url'      => (string) $r->input('link_url'),
            'mode'          => $mode,
            'scheduled_for' => $mode === 'scheduled' ? $r->date('scheduled_for') : null,
            'status'        => $mode === 'scheduled' ? 'scheduled' : 'queued',
        ]);

        // 4) Crear PostTarget según el destino
        if ($r->input('provider_choice') === 'reddit') {
            PostTarget::create([
                'post_id'          => $post->id,
                'provider'         => 'reddit',
                'reddit_subreddit' => (string) $r->input('reddit.subreddit'),
                'reddit_kind'      => $r->input('reddit.kind', $r->filled('link_url') ? 'link' : 'self'),
                'status'           => 'pending',
            ]);
        } else {
            // Discord: prioriza la URL pegada; si no viene, tómala desde la BD por webhook_id (y verifica que sea del usuario)
            $url = (string) $r->input('discord.webhook_url');
            if (! $url && $id = $r->input('discord.webhook_id')) {
                $wh = DiscordWebhook::where('user_id', $user->id)->findOrFail($id);
                $url = $wh->url;
            }

            PostTarget::create([
                'post_id'             => $post->id,
                'provider'            => 'discord',
                'discord_webhook_url' => $url,
                'status'              => 'pending',
            ]);
        }

            // 5) Envío inmediato
        if ($mode === 'now') {
            // Ejecuta el job en el momento (no requiere queue worker)
            PublishPostJob::dispatchSync($post->id);

            // Vuelve a leer el estado que dejó el job
            $post->refresh();

            if ($post->status === 'published') {
                return redirect()->route('queue.index')->with('status', 'Publicación enviada.');
            }

            // Si falló, junta los errores de los targets
            $errs = $post->targets()->where('status','failed')->pluck('error')->filter()->implode(' | ');
            return back()->with('error', $errs ?: 'Falló la publicación (revisa logs)')->withInput();
        }

        // 6) Cola o programada
        if ($mode === 'scheduled' && $post->scheduled_for) {
            // Opción A (recomendado): programa el job para esa fecha/hora
            PublishPostJob::dispatch($post->id)->delay(Carbon::parse($post->scheduled_for));
            // Nota: Para que esto corra necesitas un worker: php artisan queue:work
        }

        return redirect()
            ->route('queue.index')
            ->with('status', $mode === 'scheduled'
                ? 'Publicación programada.'
                : 'Publicación enviada a la cola.'
            );

    }

    public function history()
    {
          $userId = auth()->id();

        // Pendientes: en cola o programadas
        $pending = Post::where('user_id', $userId)
            ->whereIn('status', ['queued', 'scheduled'])
            ->orderByRaw('COALESCE(scheduled_for, created_at) asc')
            ->get();

        // Históricas: publicadas (y opcionalmente fallidas)
        $published = Post::where('user_id', $userId)
            ->whereIn('status', ['published']) // agrega 'failed' si quieres ver fallidas
            ->orderByDesc('published_at')
            ->limit(50)
            ->get();

        
        return view('queue.index', compact('pending', 'published'));
    }
}
