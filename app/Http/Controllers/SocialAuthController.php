<?php

namespace App\Http\Controllers;

use App\Models\SocialConnection;
use App\Models\DiscordWebhook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;  

class SocialAuthController extends Controller
{
     /** Scopes por proveedor */
    protected function scopesFor(string $provider): array
    {
         return match (strtolower($provider)) {
            // Reddit: identidad + permiso para publicar
            'reddit'  => ['identity', 'submit'],

            // Discord: identificar usuario, listar servidores y webhook entrante
            'discord' => ['identify', 'guilds', 'webhook.incoming'],

            default   => [],
        };
    }
            public function redirect(string $provider)
            {
                $provider = strtolower($provider);

                // (opcional) limitar a los soportados
                abort_unless(in_array($provider, ['reddit','discord']), 404);

                if ($provider === 'discord') {
                    return Socialite::driver('discord')
                        ->scopes(['identify', 'guilds', 'webhook.incoming'])
                        // 0x20000000 = 536870912 (Manage Webhooks)
                        ->with(['permissions' => 536870912])
                        ->redirect();
                }

                // Resto de proveedores (ej. Reddit) usando tus scopes
                return Socialite::driver($provider)
                    ->scopes($this->scopesFor($provider))
                    ->redirect();
            }

            public function callback(string $provider, Request $request)
        {
            try {
            $provider   = strtolower($provider);
            $user       = Auth::user();

            // Usa stateless() para evitar problemas de state al usar Sanctum
            $socialUser = Socialite::driver($provider)->stateless()->user();

            // Expiración del access token (si viene)
            $expiresAt = (property_exists($socialUser, 'expiresIn') && $socialUser->expiresIn)
                ? Carbon::now()->addSeconds($socialUser->expiresIn)
                : null;

            // Crudo del perfil + respuesta del intercambio de token (útil para depurar)
            $rawProfile   = method_exists($socialUser, 'getRaw') ? $socialUser->getRaw() : ($socialUser->user ?? []);
            $tokenPayload = $socialUser->accessTokenResponseBody ?? [];

            // Guarda/actualiza la conexión genérica
            $conn = SocialConnection::updateOrCreate(
                ['user_id' => $user->id, 'provider' => $provider],
                [
                    'provider_user_id' => (string) $socialUser->id,
                    'access_token'     => $socialUser->token,
                    'refresh_token'    => $socialUser->refreshToken ?? null,
                    'expires_at'       => $expiresAt,
                    'scopes'           => $this->scopesFor($provider), // asegúrate que discord tenga 'webhook.incoming'
                    'meta'             => [
                        'raw'             => $rawProfile,
                        'token_response'  => $tokenPayload,
                    ],
                ]
            );

            // Si es Discord e incluye webhook en el token response, lo persistimos
            if ($provider === 'discord') {
                $webhook = data_get($tokenPayload, 'webhook');
                if (is_array($webhook) && isset($webhook['id'], $webhook['token'])) {
                    $url = "https://discord.com/api/webhooks/{$webhook['id']}/{$webhook['token']}";

                    DiscordWebhook::updateOrCreate(
                        [
                            'user_id'            => $user->id,
                            'discord_webhook_id' => (string) $webhook['id'],
                        ],
                        [
                            'social_connection_id' => $conn->id,
                            'token'                => (string) $webhook['token'],
                            'url'                  => $url,
                            'guild_id'             => (string) data_get($webhook, 'guild_id'),
                            'channel_id'           => (string) data_get($webhook, 'channel_id'),
                            'name'                 => data_get($webhook, 'name'),
                            'avatar'               => data_get($webhook, 'avatar'),
                        ]
                    );
                }
            }

            return redirect()->route('social.connections')
                ->with('status', "Conectado con {$provider}.");
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('social.connections')
                ->with('status', "No se pudo conectar {$provider}: ".$e->getMessage());
        }
    }
}
