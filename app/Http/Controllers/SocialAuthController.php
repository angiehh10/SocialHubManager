<?php

namespace App\Http\Controllers;

use App\Models\SocialConnection;
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
        $provider = strtolower($provider);

        return match ($provider) {
            // Reddit: identidad + permiso para publicar
            'reddit'  => ['identity', 'submit'],

            // Discord: identificar usuario, listar servidores y (opcional) webhook entrante
            'discord' => ['identify', 'guilds', 'webhook.incoming'],

            default => [],
        };
    }

    public function redirect(string $provider)
        {
            $provider = strtolower($provider);

            return Socialite::driver($provider)
                ->scopes($this->scopesFor($provider))
                ->redirect();
        }

        public function callback(string $provider, Request $request)
    {
        try {
            $provider = strtolower($provider);

            $socialUser = Socialite::driver($provider)->stateless()->user();
            $user = Auth::user();

            $expiresAt = null;
            if (property_exists($socialUser, 'expiresIn') && $socialUser->expiresIn) {
                $expiresAt = Carbon::now()->addSeconds($socialUser->expiresIn);
            }

            SocialConnection::updateOrCreate(
                ['user_id' => $user->id, 'provider' => $provider],
                [
                    'provider_user_id' => (string) $socialUser->id,
                    'access_token'     => $socialUser->token,
                    'refresh_token'    => $socialUser->refreshToken ?? null,
                    'expires_at'       => $expiresAt,
                    'scopes'           => $this->scopesFor($provider),
                    'meta'             => ['raw' => $socialUser->user ?? null],
                ]
            );

            return redirect()->route('social.connections')
                ->with('status', "Conectado con {$provider}.");
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('social.connections')
                ->with('status', "No se pudo conectar {$provider}: ".$e->getMessage());
        }
    }
}
