<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Social Hub Manager</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-base-50 text-base-800 dark:bg-base-900 dark:text-base-100">
    {{-- Fondo decorativo --}}
    <div aria-hidden="true" class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-32 -left-32 h-80 w-80 rounded-full blur-3xl opacity-30 bg-primary-400 dark:bg-primary-700"></div>
        <div class="absolute -bottom-24 -right-24 h-96 w-96 rounded-full blur-3xl opacity-20 bg-primary-300 dark:bg-primary-800"></div>
    </div>

    {{-- Header --}}
    <header class="sticky top-0 z-20 backdrop-blur supports-[backdrop-filter]:bg-white/60 bg-white/70 dark:bg-base-900/70 border-b border-base-200 dark:border-base-800">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-primary-600 text-white">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 7h16M7 12h10M9 17h6" />
                        <circle cx="12" cy="12" r="10" class="opacity-30" />
                    </svg>
                </span>
                <span class="text-base font-semibold">Social Hub Manager</span>
            </a>

            <nav class="flex items-center gap-2">
                <a href="{{ route('privacy') }}"
                   class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium ring-1 ring-base-300 hover:ring-base-400 dark:ring-base-700 dark:hover:ring-base-600 transition">
                    Privacidad
                </a>
                <a href="{{ route('terms') }}"
                   class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium ring-1 ring-base-300 hover:ring-base-400 dark:ring-base-700 dark:hover:ring-base-600 transition">
                    Términos
                </a>

                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium ring-1 ring-base-300 hover:ring-base-400 dark:ring-base-700 dark:hover:ring-base-600 transition">
                        Ir al dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium ring-1 ring-base-300 hover:ring-base-400 dark:ring-base-700 dark:hover:ring-base-600 transition">
                        Iniciar sesión
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium bg-primary-600 text-white hover:bg-primary-700 active:bg-primary-800 transition">
                            Crear cuenta
                        </a>
                    @endif
                @endauth
            </nav>
        </div>
    </header>

    {{-- Hero --}}
    <section class="container mx-auto px-4 py-16 lg:py-24">
        <div class="grid items-center gap-12 lg:grid-cols-2">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full bg-primary-50 text-primary-700 dark:bg-primary-800/40 dark:text-primary-100 px-3 py-1 text-xs ring-1 ring-primary-200 dark:ring-primary-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-primary-600"></span>
                    Administra tus redes en un solo lugar
                </span>

                <h1 class="mt-5 text-4xl sm:text-5xl font-bold leading-tight tracking-tight">
                    Programa, publica y controla<br class="hidden sm:block">
                    <span class="text-primary-700 dark:text-primary-300">Reddit</span> y <span class="text-primary-700 dark:text-primary-300">Discord</span>
                    sin complicarte.
                </h1>

                <p class="mt-4 text-base sm:text-lg text-base-600 dark:text-base-300 max-w-xl">
                    Conecta tus cuentas, crea publicaciones, agenda horarios y deja que nosotros nos encarguemos de la cola.
                    Seguridad con <strong>2FA</strong> incluido.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    @auth
                        <a href="{{ route('social.connections') }}"
                           class="btn btn-primary px-5 py-3 rounded-xl">
                            Conectar redes
                        </a>
                        <a href="{{ route('schedules.index') }}"
                           class="inline-flex items-center px-5 py-3 rounded-xl ring-1 ring-base-300 hover:ring-base-400 dark:ring-base-700 dark:hover:ring-base-600 transition">
                            Configurar horarios
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="btn btn-primary px-5 py-3 rounded-xl">
                            Comenzar gratis
                        </a>
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center px-5 py-3 rounded-xl ring-1 ring-base-300 hover:ring-base-400 dark:ring-base-700 dark:hover:ring-base-600 transition">
                            Ya tengo cuenta
                        </a>
                    @endauth
                </div>

                {{-- Marcas / integraciones --}}
                <div class="mt-10 flex items-center gap-6 text-base-500 dark:text-base-400">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>
                        <span class="text-sm">OAuth seguro</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                        <span class="text-sm">Cola y programación</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1l3 7h7l-5.5 4 2 7L12 16l-6.5 3 2-7L2 8h7z"/></svg>
                        <span class="text-sm">2FA opcional</span>
                    </div>
                </div>
            </div>

            {{-- Mock UI card --}}
            <di
