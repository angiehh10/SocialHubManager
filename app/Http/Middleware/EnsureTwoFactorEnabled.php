<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            // Consideramos “listo” cuando tiene secreto y está confirmado
            $is2faEnabled   = ! empty($user->two_factor_secret);
            $is2faConfirmed = ! is_null($user->two_factor_confirmed_at);
            $needs2fa       = ! ($is2faEnabled && $is2faConfirmed);

            if ($needs2fa && ! $this->isOnAllowedPath($request)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Debes habilitar y confirmar 2FA antes de continuar.',
                    ], 403);
                }

                return redirect()
                    ->route('profile.show')
                    ->with('status', 'Por favor, habilita la autenticación en dos pasos (2FA) antes de continuar.');
            }
        }

        return $next($request);
    }

    protected function isOnAllowedPath(Request $request): bool
    {
        // Evitar bucle cuando ya está en el perfil o notificaciones de verificación
        $routeName = optional($request->route())->getName();
        if (in_array($routeName, ['profile.show', 'verification.notice'], true)) {
            return true;
        }

        // Permitir rutas necesarias para el flujo de 2FA / auth
        $path = '/'.ltrim($request->path(), '/');
        $allowedPrefixes = [
            '/two-factor-challenge',           // reto de 2FA al iniciar sesión
            '/logout',                         // cerrar sesión
            '/email/verification-notification' // re-enviar verificación (si usas verified)
        ];

        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
