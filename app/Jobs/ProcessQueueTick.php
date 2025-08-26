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
        // Todo en TZ de la app (CR)
        $now = now(config('app.timezone', 'America/Costa_Rica'));

        // ---- cola por slots (TZ app) ----
        $userIds = Post::select('user_id')->queued()->groupBy('user_id')->pluck('user_id');
        foreach ($userIds as $uid) {
            $slot = $this->nextDueSlotForUser($uid, $now);
            if (!$slot || $slot->gt($now)) continue;

            $post = Post::where('user_id', $uid)->queued()->orderBy('id')->first();
            if (!$post) continue;

            // marcamos que la estamos lanzando ahora (en TZ app)
            $post->update(['status' => 'scheduled', 'scheduled_for' => $now]);

            dispatch(new PublishPostJob($post->id));
        }

        // ---- programadas explícitas (TZ app) ----
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
        $tz = config('app.timezone', 'America/Costa_Rica');

        $schedules = PublishSchedule::where('user_id', $userId)
            ->where('active', true)->get();

        if ($schedules->isEmpty()) return null;

        $candidates = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $now->copy()->addDays($i);
            $weekday = (int) $day->dayOfWeek; // 0..6

            foreach ($schedules as $s) {
                if ((int) $s->weekday !== $weekday) continue;

                $hhmmss = Carbon::parse((string)$s->time, $tz)->format('H:i:s');
                [$h, $m, $ss] = explode(':', $hhmmss);

                $candidate = $day->copy()->setTime((int)$h, (int)$m, (int)$ss);
                if ($candidate->gte($now)) $candidates[] = $candidate;
            }
        }
        return collect($candidates)->sort()->first();
    }
}
