<?php

namespace App\Services\Social;

use Illuminate\Support\Facades\Http;

class DiscordService
{
    public function sendViaWebhook(string $url, string $content, array $options = []): bool
    {
        $payload = array_filter([
            'content'    => $content,
            'username'   => $options['username']   ?? null,
            'avatar_url' => $options['avatar_url'] ?? null,
            'tts'        => $options['tts']        ?? false,
        ], static fn($v) => !is_null($v));

        if (!empty($options['embeds'])) {
            $payload['embeds'] = $options['embeds'];
        }

        $resp = Http::asJson()->post($url, $payload);

        // Discord responde 204 No Content en éxito; también cuenta cualquier 2xx
        return $resp->noContent() || $resp->successful();
    }
}
