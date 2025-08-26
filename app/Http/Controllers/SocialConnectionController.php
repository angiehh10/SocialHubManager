<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocialConnectionController extends Controller
{
    /**
     * Lista el estado de conexiones sociales del usuario actual.
     */
    public function index()
    {
        $user = Auth::user();

        // Los proveedores que vas a mostrar en la UI
        $providers = ['reddit', 'discord'];

        // Trae TODAS las conexiones de este usuario y clavea por provider
        $existing = $user->socialConnections()->get()->keyBy('provider'); // ['reddit' => Model|null, 'discord' => Model|null]

        // (Compat) Si tu vista espera 'connections', lo construimos con la misma forma
        $connections = collect($providers)->mapWithKeys(function ($p) use ($existing) {
            return [$p => $existing->get($p)];
        });

        return view('social.connections', [
            'providers'   => $providers,  // para iterar tarjetas
            'existing'    => $existing,   // map por provider
            'connections' => $connections // compat con tu vista actual
        ]);
    }

    /**
     * Desconecta y elimina los tokens de un provider.
     */
    public function destroy(string $provider)
    {
        $user = Auth::user();
        $provider = strtolower($provider);

        if (! in_array($provider, ['reddit', 'discord'], true)) {
            abort(404);
        }

         if ($conn = $user->socialConnection($provider)) {
            $conn->delete(); // 👈 con CASCADE se van sus webhooks
        }

        return back()->with('status', "Has desconectado {$provider}.");
    }
}
