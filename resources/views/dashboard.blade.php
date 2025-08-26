<x-app-layout>
    {{-- Banner superior --}}
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-primary-100 via-white to-transparent dark:from-primary-900/30 dark:via-base-900 dark:to-transparent"></div>

        <div class="max-w-6xl mx-auto px-6 py-8">
            @if($needs2fa)
                <div class="mb-4 rounded-xl border border-amber-200/60 bg-amber-50/60 dark:border-amber-800/50 dark:bg-amber-900/20 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-amber-600 dark:text-amber-300 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 9v4M12 17h.01"/><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
                        <div class="text-sm">
                            <div class="font-medium">Activa la verificación en dos pasos (2FA).</div>
                            <div class="opacity-80">Protege tu cuenta con códigos TOTP. Puedes hacerlo desde tu perfil.</div>
                        </div>
                        <a href="{{ route('profile.show') }}" class="ml-auto inline-flex items-center px-3 py-1.5 rounded-lg text-sm bg-amber-600 text-white hover:bg-amber-700">Configurar 2FA</a>
                    </div>
                </div>
            @endif

            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-semibold">¡Hola, {{ Str::of(auth()->user()->name ?? 'creador')->before(' ') }}!</h1>
                    <p class="mt-1 text-base text-base-600 dark:text-base-300">
                        Administra tus conexiones, horarios y la cola de publicaciones desde aquí.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('posts.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-600 text-white hover:bg-primary-700">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14"/></svg>
                        Nueva publicación
                    </a>
                    <a href="{{ route('social.connections') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl ring-1 ring-base-300 hover:ring-base-400 dark:ring-base-700 dark:hover:ring-base-600">
                        Gestionar conexiones
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Tarjetas de métricas --}}
    <div class="max-w-6xl mx-auto px-6 py-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl border border-base-200 dark:border-base-800 bg-white dark:bg-base-900 p-5">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-base-600 dark:text-base-400">Conexiones</span>
                    <span class="h-8 w-8 rounded-xl bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-200 flex items-center justify-center">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M7 7h.01M17 17h.01M7 17l10-10"/></svg>
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold">{{ $stats['connections'] }}</div>
                <div class="text-xs text-base-500 mt-1">Reddit / Discord</div>
            </div>

            <div class="rounded-2xl border border-base-200 dark:border-base-800 bg-white dark:bg-base-900 p-5">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-base-600 dark:text-base-400">Programadas hoy</span>
                    <span class="h-8 w-8 rounded-xl bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-200 flex items-center justify-center">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 5h18M8 3v4M16 3v4M3 11h18M7 19h6"/></svg>
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold">{{ $stats['scheduled_today'] }}</div>
                <div class="text-xs text-base-500 mt-1">Por horario</div>
            </div>

            <div class="rounded-2xl border border-base-200 dark:border-base-800 bg-white dark:bg-base-900 p-5">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-base-600 dark:text-base-400">En cola</span>
                    <span class="h-8 w-8 rounded-xl bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-200 flex items-center justify-center">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 7h16M4 12h12M4 17h8"/></svg>
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold">{{ $stats['queue_pending'] }}</div>
                <div class="text-xs text-base-500 mt-1">Pendientes por enviar</div>
            </div>

            <div class="rounded-2xl border border-base-200 dark:border-base-800 bg-white dark:bg-base-900 p-5">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-base-600 dark:text-base-400">Publicadas (7d)</span>
                    <span class="h-8 w-8 rounded-xl bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-200 flex items-center justify-center">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12l4 4L19 6"/></svg>
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold">{{ $stats['published_week'] }}</div>
                <div class="text-xs text-base-500 mt-1">Éxitos recientes</div>
            </div>
        </div>
    </div>

    {{-- Acciones rápidas --}}
    <div class="max-w-6xl mx-auto px-6 pb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('social.connections') }}" class="rounded-2xl border border-base-200 dark:border-base-800 bg-white dark:bg-base-900 p-5 hover:shadow-lg transition">
                <div class="flex items-center gap-3">
                    <span class="h-10 w-10 rounded-xl bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-200 flex items-center justify-center">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 8a6 6 0 0 1-6 6M6 16a6 6 0 0 1 6-6"/></svg>
                    </span>
                    <div>
                        <h3 class="font-semibold">Redes conectadas</h3>
                        <p class="text-sm text-base-600 dark:text-base-400">Vincula tus cuentas para publicar desde aquí.</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('schedules.index') }}" class="rounded-2xl border border-base-200 dark:border-base-800 bg-white dark:bg-base-900 p-5 hover:shadow-lg transition">
                <div class="flex items-center gap-3">
                    <span class="h-10 w-10 rounded-xl bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-200 flex items-center justify-center">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 5h18M3 12h18M3 19h18"/></svg>
                    </span>
                    <div>
                        <h3 class="font-semibold">Horarios de publicación</h3>
                        <p class="text-sm text-base-600 dark:text-base-400">Define días y horas automáticas.</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('queue.index') }}" class="rounded-2xl border border-base-200 dark:border-base-800 bg-white dark:bg-base-900 p-5 hover:shadow-lg transition">
                <div class="flex items-center gap-3">
                    <span class="h-10 w-10 rounded-xl bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-200 flex items-center justify-center">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 7h16M4 12h16M4 17h10"/></svg>
                    </span>
                    <div>
                        <h3 class="font-semibold">Cola de publicaciones</h3>
                        <p class="text-sm text-base-600 dark:text-base-400">Revisa pendientes e histórico.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Próximas publicaciones (si las pasas desde el controlador) --}}
    <div class="max-w-6xl mx-auto px-6 pb-10">
        <div class="rounded-2xl border border-base-200 dark:border-base-800 bg-white dark:bg-base-900 p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold">Próximas publicaciones</h3>
                <a href="{{ route('posts.create') }}" class="text-sm text-primary-700 dark:text-primary-300 hover:underline">Crear nueva</a>
            </div>

            @if(count($upcoming))
                <ul class="divide-y divide-base-200 dark:divide-base-800">
                    @foreach($upcoming as $item)
                        <li class="py-3 flex items-center justify-between">
                            <div class="min-w-0">
                                <div class="text-sm font-medium">{{ $item['title'] }}</div>
                                <div class="text-xs text-base-500 mt-0.5">
                                    {{ $item['when'] }} · {{ $item['target'] }}
                                </div>
                            </div>
                            @if(!empty($item['status']))
                                <span class="text-xs px-2 py-1 rounded-lg bg-base-100 dark:bg-base-800">{{ $item['status'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-sm text-base-600 dark:text-base-400">
                    No hay publicaciones programadas. <a href="{{ route('posts.create') }}" class="text-primary-700 dark:text-primary-300 hover:underline">Programa la primera</a>.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

