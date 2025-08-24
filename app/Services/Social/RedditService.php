<?php

namespace App\Services\Social;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class RedditService
{
    protected function client(User $user)
    {
        $conn = $user->socialConnection('reddit');
        abort_unless($conn, 400, 'Reddit no conectado');

        $ua = config('services.reddit.user_agent', env('REDDIT_USER_AGENT', 'SocialHubManager'));
        return Http::withToken($conn->access_token)
            ->withHeaders(['User-Agent' => $ua]);
    }

        public function submitText(string $accessToken, string $subreddit, string $title, string $text): array
        {
            $resp = Http::asForm()
                ->withHeaders([
                    'Authorization' => "Bearer {$accessToken}",
                    'User-Agent'    => 'web:socialhubmanager.v1 (by /u/Distinct-Brief-1091)',
                ])
                ->post('https://oauth.reddit.com/api/submit', [
                    'sr'       => $subreddit,
                    'kind'     => 'self',
                    'title'    => $title,
                    'text'     => $text,
                    'resubmit' => true,
                    'api_type' => 'json',
                ]);

            $json    = $resp->json();
            $errors  = data_get($json, 'json.errors', []);
            if (!empty($errors)) {
                // Convierte [["CODE","Mensaje","campo"], ...] a una línea legible
                $flat = collect($errors)->map(fn($e) => implode(' | ', $e))->implode(' || ');
                return ['ok' => false, 'error' => $flat, 'external_id' => null];
            }

            // Cuando es correcto viene data.url y a veces name (t3_xxx)
            $ext = data_get($json, 'json.data.name') ?: data_get($json, 'json.data.url');
            return ['ok' => true, 'error' => null, 'external_id' => $ext];
        }

        public function submitLink(string $accessToken, string $subreddit, string $title, string $url): array
        {
            $resp = Http::asForm()
                ->withHeaders([
                    'Authorization' => "Bearer {$accessToken}",
                    'User-Agent'    => 'web:socialhubmanager.v1 (by /u/Distinct-Brief-1091)',
                ])
                ->post('https://oauth.reddit.com/api/submit', [
                    'sr'       => $subreddit,
                    'kind'     => 'link',
                    'title'    => $title,
                    'url'      => $url,
                    'resubmit' => true,
                    'api_type' => 'json',
                ]);

            $json   = $resp->json();
            $errors = data_get($json, 'json.errors', []);
            if (!empty($errors)) {
                $flat = collect($errors)->map(fn($e) => implode(' | ', $e))->implode(' || ');
                return ['ok' => false, 'error' => $flat, 'external_id' => null];
            }

            $ext = data_get($json, 'json.data.name') ?: data_get($json, 'json.data.url');
            return ['ok' => true, 'error' => null, 'external_id' => $ext];
        }
        
    protected function okOrLog(string $where, int $status, ?array $json, string $rawBody): bool
    {
        $errors = (array) data_get($json, 'json.errors', []);
        $ok = $status === 200 && empty($errors);

        if (! $ok) {
            // Construye mensaje legible (ej: RATELIMIT, subreddit rules, etc.)
            $msg = collect($errors)->map(function ($e) {
                // reddit devuelve arrays con códigos y mensajes
                return is_array($e) ? implode(' | ', $e) : (string) $e;
            })->implode(' || ');

            \Log::error("Reddit {$where} failed", [
                'status' => $status,
                'json'   => $json,
                'raw'    => $rawBody,
                'msg'    => $msg ?: 'Error desconocido',
            ]);
        }

        return $ok;
    }
}

