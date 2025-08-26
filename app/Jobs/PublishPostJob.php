<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\Social\RedditService;
use App\Services\Social\DiscordService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;  
use Illuminate\Queue\InteractsWithQueue;      
use Illuminate\Queue\SerializesModels; 
use Illuminate\Support\Facades\Log;


class PublishPostJob implements ShouldQueue
{
   use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $postId) {}

   
    public function handle(RedditService $reddit, DiscordService $discord): void
    {
        $post = Post::with(['user', 'targets'])->find($this->postId);
        if (!$post || !in_array($post->status, ['queued','scheduled','publishing'])) {
            return;
        }

        $post->update(['status' => 'publishing']);

        foreach ($post->targets as $t) {
            try {
                // -------- DISCORD --------
                if ($t->provider === 'discord') {
                    $ok = $discord->sendViaWebhook((string) $t->discord_webhook_url, (string) $post->body);

                    if ($ok) {
                        $t->update([
                            'status'      => 'sent',     // OK para post_targets
                            'external_id' => null,
                            'error'       => null,
                        ]);
                    } else {
                        Log::error('Discord webhook failed', ['post_id' => $post->id, 'target_id' => $t->id]);
                        $t->update(['status' => 'failed', 'error' => 'Discord webhook failed']);
                    }
                }

                // -------- REDDIT --------
                if ($t->provider === 'reddit') {
                    $conn = $post->user->socialConnection('reddit');
                    if (!$conn) {
                        $t->update(['status' => 'failed', 'error' => 'Sin conexión a Reddit']);
                        continue;
                    }

                    if ($t->reddit_kind === 'self') {
                        $res = $reddit->submitText($conn->access_token, $t->reddit_subreddit, (string) $post->title, (string) $post->body);
                    } else {
                        $res = $reddit->submitLink($conn->access_token, $t->reddit_subreddit, (string) $post->title, (string) $post->link_url);
                    }

                    if ($res['ok']) {
                        $t->update([
                            'status'      => 'sent',        // NO uses published aquí
                            'external_id' => $res['external_id'],
                            'error'       => null,
                        ]);
                    } else {
                        Log::error('Reddit submit failed', ['post_id' => $post->id, 'target_id' => $t->id, 'error' => $res['error']]);
                        $t->update(['status' => 'failed', 'error' => $res['error']]);
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Publish target exception', [
                    'post_id'   => $post->id,
                    'provider'  => $t->provider,
                    'target_id' => $t->id,
                    'e'         => $e->getMessage(),
                ]);
                $t->update(['status' => 'failed', 'error' => $e->getMessage()]);
            }
        }

        // -------- Estado final del Post --------
        if ($post->targets()->where('status', 'failed')->exists()) {
            $post->update(['status' => 'failed']);
        } elseif ($post->targets()->where('status', 'sent')->count() === $post->targets()->count()) {
            // todos OK -> publicamos el Post
            $post->update([
                'status'       => 'published', 
                'published_at' => now(config('app.timezone', 'America/Costa_Rica')),
            ]);
        } else {
            // quedó algo pendiente
            $post->update(['status' => 'queued']);
        }
    }
}