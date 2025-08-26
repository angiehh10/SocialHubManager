<?php

namespace App\Http\Controllers;

use App\Jobs\PublishPostJob;
use App\Models\Post;
use App\Models\PublishSchedule;
use App\Models\PostTarget;
use App\Models\DiscordWebhook;  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon; 



class PostController extends Controller
{

        public function create()
    {
                $discordWebhooks = \App\Models\DiscordWebhook::valid()   // 👈 filtra huérfanos
                ->where('user_id', \Auth::id())
                ->orderByDesc('id')
                ->get();

                $slots = PublishSchedule::where('user_id', Auth::id())
                ->where(function($q){
                    $q->whereNull('active')->orWhere('active', true);
                })
                ->orderBy('weekday')->orderBy('time')
                ->get();

            return view('posts.create', compact('discordWebhooks','slots'));
    }

        public function store(Request $r)
    {
            // 1) Validación
        $r->validate([
            'provider_choice' => 'required|in:reddit,discord',
            'mode'            => 'required|in:now,queue,queue_slot,scheduled', 
            'schedule_slot_id' => 'required_if:mode,queue_slot|nullable|exists:publish_schedules,id',

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

            // --- HORARIO (si usa queue_slot) ---
            'schedule_slot_id'    => 'required_if:mode,queue_slot|exists:publish_schedules,id',
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
        $scheduledFor = null;

           if ($mode === 'queue_slot') {
            $slot = PublishSchedule::where('user_id', $user->id)
                ->where(function($q){ $q->whereNull('active')->orWhere('active', true); })
                ->findOrFail($r->input('schedule_slot_id'));

            if ($r->boolean('choose_date')) {
                // Fecha exacta: usa la fecha elegida + hora del slot, ajusta si no coincide
                $scheduledFor = $this->combineDateWithSlotOrAdjust(
                    $r->input('schedule_date'),
                    (int)$slot->weekday,
                    (string)$slot->time
                );
            } else {
                // Sin fecha: siempre la semana siguiente en ese mismo slot
                $scheduledFor = $this->nextOccurrence((int)$slot->weekday, (string)$slot->time, true);
            }

            $mode = 'scheduled';

        }

            // Si eligió “queue” (sin horario) pero SÍ tienes horarios, usa el próximo disponible
        if ($mode === 'queue') {
            $slot = PublishSchedule::where('user_id', Auth::id())
                ->where(function($q){ $q->whereNull('active')->orWhere('active', true); })
                ->orderBy('weekday')->orderBy('time')
                ->get()
                ->first();

            if ($slot) {
                $scheduledFor = $this->nextOccurrence((int) $slot->weekday, $slot->time);
                $mode = 'scheduled';
            }
        }

            // Si eligió “queue” (sin horario) pero SÍ tienes horarios, usa el próximo disponible
        if ($mode === 'queue') {
            $slot = PublishSchedule::where('user_id', Auth::id())
                ->where(function($q){ $q->whereNull('active')->orWhere('active', true); })
                ->orderBy('weekday')->orderBy('time')
                ->get()
                ->first();

            if ($slot) {
                $scheduledFor = $this->nextOccurrence((int) $slot->weekday, $slot->time);
                $mode = 'scheduled';
            }
        }

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
            'scheduled_for' => $scheduledFor, 
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
         if ($mode === 'scheduled' && $scheduledFor) {
            PublishPostJob::dispatch($post->id)->delay($scheduledFor);
        }

        return redirect()
            ->route('queue.index')
            ->with('status', $mode === 'scheduled'
                ? 'Publicación programada.'
                : 'Publicación enviada a la cola.'
            );

    }
    
    protected function nextOccurrence(int $weekday, $time, bool $forceNextWeek = false): Carbon
    {
        $tz = config('app.timezone', 'America/Costa_Rica');

        $timeNormalized = Carbon::parse((string)$time, $tz)->format('H:i:s');
        [$h,$m,$s] = explode(':', $timeNormalized);

        $now = Carbon::now($tz);
        $candidate = $now->copy()->setTime((int)$h, (int)$m, (int)$s);

        $delta = ($weekday - $candidate->dayOfWeek + 7) % 7;
        if ($delta > 0) $candidate->addDays($delta);

        if ($forceNextWeek || $candidate->lessThanOrEqualTo($now)) {
            $candidate->addDays(7);
        }
        return $candidate; // <- SIN setTimezone('UTC')
    }

    protected function combineDateWithSlotOrAdjust(string $date, int $weekday, string $time): Carbon
    {
        $tz = config('app.timezone', 'America/Costa_Rica');

        $timeNormalized = Carbon::parse((string)$time, $tz)->format('H:i:s');
        [$h,$m,$s] = explode(':', $timeNormalized);

        $chosen = Carbon::createFromFormat('Y-m-d H:i:s', "{$date} {$h}:{$m}:{$s}", $tz);

        $delta = ($weekday - $chosen->dayOfWeek + 7) % 7;
        if ($delta !== 0) {
            $chosen->addDays($delta);
            session()->flash('warning', 'La fecha no coincidía con el día del horario; se ajustó a la próxima ocurrencia.');
        }

        $now = Carbon::now($tz);
        if ($chosen->lessThanOrEqualTo($now)) {
            $chosen->addDays(7);
        }
        return $chosen; // <- SIN convertir
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

    public function editSchedule(Post $post)
    {
        abort_unless($post->user_id === Auth::id(), 403);

        if (!$post->scheduled_for) {
            return back()->with('error', 'Esta publicación no tiene hora programada.');
        }

        $tz   = config('app.timezone', 'America/Costa_Rica');
        $date = $post->scheduled_for->copy()->timezone($tz)->toDateString();

        return view('posts.edit-schedule', compact('post', 'date', 'tz'));
    }

    public function updateSchedule(Request $r, Post $post)
    {
        abort_unless($post->user_id === Auth::id(), 403);

        $tz = config('app.timezone', 'America/Costa_Rica');

        $r->validate([
            'schedule_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.now($tz)->toDateString()],
        ]);

        if (!$post->scheduled_for) {
            return back()->with('error', 'Esta publicación no tiene hora programada.');
        }

        // 1) Tomar la hora/min/seg ACTUAL
        $current = $post->scheduled_for->copy()->timezone($tz);
        $h = (int) $current->format('H');
        $m = (int) $current->format('i');
        $s = (int) $current->format('s');

        // 2) Combinar SOLO la nueva fecha con la misma hora
        $newDate = Carbon::createFromFormat('Y-m-d', $r->input('schedule_date'), $tz)
            ->setTime($h, $m, $s);

        // 3) Guardar tal cual (misma hora)
        $post->update([
            'scheduled_for' => $newDate,
            'status'        => 'scheduled',
        ]);

        return redirect()->route('queue.index')->with('status', 'Fecha actualizada.');
    }
}
