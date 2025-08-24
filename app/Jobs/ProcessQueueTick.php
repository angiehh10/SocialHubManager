<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\PublishSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class ProcessQueueTick implements ShouldQueue
{
     use Dispatchable, Queueable;

    public function handle(): void
    {
        // Procesa por usuario: si hay un slot debido, toma UNA entrada de la cola y publícala
        $now = now(); // usa tz de app: config('app.timezone')

        // Usuarios que tienen algo en cola
        $userIds = Post::select('user_id')
            ->queued()
            ->groupBy('user_id')
            ->pluck('user_id');

        foreach ($userIds as $uid) {
            // ¿Hay un slot debido ahora?
            $slot = $this->nextDueSlotForUser($uid, $now);
            if (!$slot || $slot->gt($now)) continue;

            // Toma UNA publicación en cola (FIFO) y lánzala
            $post = Post::where('user_id', $uid)->queued()->orderBy('id')->first();
            if (!$post) continue;

            $post->update(['status' => 'scheduled', 'scheduled_for' => $now]);

            dispatch(new PublishPostJob($post->id));
        }

        // Publicaciones programadas explícitamente para ahora o antes (ignora horario del usuario)
        $due = Post::scheduled()
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for', '<=', $now)
            ->pluck('id');

        foreach ($due as $pid) {
            dispatch(new PublishPostJob($pid));
        }
    }

    protected function nextDueSlotForUser(int $userId, Carbon $now): ?Carbon
    {
        // Busca el slot más cercano >= now (en hoy o próximos 7 días)
        $schedules = PublishSchedule::where('user_id', $userId)->where('active', true)->get();
        if ($schedules->isEmpty()) return null;

        $candidates = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $now->copy()->addDays($i);
            $weekday = (int)$day->dayOfWeek; // 0..6 (Domingo..Sábado)

            foreach ($schedules as $s) {
                if ((int)$s->weekday !== $weekday) continue;
                [$h, $m, $ss] = explode(':', $s->time->format('H:i:s'));
                $candidate = $day->copy()->setTime((int)$h, (int)$m, (int)$ss);

                if ($candidate->gte($now)) $candidates[] = $candidate;
            }
        }

        return collect($candidates)->sort()->first() ?? null;
    }
}
