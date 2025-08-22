{{-- resources/views/social/connections.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Conexiones sociales
                </h2>
                <p class="text-sm text-base-600 dark:text-base-400 mt-1">
                    Autoriza tus cuentas para poder publicar desde Social Hub Manager.
                </p>
            </div>
            <span class="hidden md:inline-flex items-center gap-2 text-xs px-2.5 py-1 rounded-lg ring-1 ring-base-300 dark:ring-base-700">
                Seguro con OAuth & 2FA
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="mb-2 rounded-xl border border-green-200/70 bg-green-50/70 dark:border-green-900/40 dark:bg-green-900/20 px-4 py-3 text-sm text-green-800 dark:text-green-200">
                    {{ session('status') }}
                </div>
            @endif

            @php
                $labels = ['reddit' => 'Reddit', 'discord' => 'Discord'];

                $icons = [
                    'reddit' => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor"><circle cx="12" cy="12" r="10" class="opacity-20"></circle><path d="M18 13a6 6 0 0 1-12 0c0-.1 0-.2.01-.31A2 2 0 0 1 8 11c.52 0 .99.21 1.33.54A6.9 6.9 0 0 1 12 11c.98 0 1.9.22 2.67.54.34-.33.81-.54 1.33-.54 1.1 0 2 .9 2 2 0 .1 0 .2-.01.31z"/></svg>',
                    'discord' => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor"><path d="M20 4a16 16 0 0 0-4-.9l-.2.4a14 14 0 0 1 3.4 1.2c-3-1.5-6.3-1.5-9.4 0A14 14 0 0 1 12 3.5l-.2-.4A16 16 0 0 0 8 4C3.7 7.9 3 12.6 3.3 17.3 5 18.7 7 19.7 9.2 20l.5-1.3c-.9-.3-1.8-.7-2.6-1.2.2-.1.4-.3.6-.4 2.4 1.1 5.2 1.1 7.6 0 .2.1.4.3.6.4-.8.5-1.7.9-2.6 1.2l.5 1.3c2.2-.3 4.2-1.3 5.9-2.7C21 12.6 20.3 7.9 16 4ZM9.8 14.5c-.8 0-1.5-.8-1.5-1.7s.7-1.7 1.5-1.7 1.5.8 1.5 1.7-.7 1.7-1.5 1.7Zm4.4 0c-.8 0-1.5-.8-1.5-1.7s.7-1.7 1.5-1.7 1.5.8 1.5 1.7-.7 1.7-1.5 1.7Z"/></svg>',
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($providers as $p)
                    @php $conn = $existing->get($p); @endphp

                    <div class="rounded-2xl border border-base-200 dark:border-base-800 bg-white/80 dark:bg-base-900/70 backdrop-blur p-6 shadow-sm hover:shadow-md transition">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <span class="h-10 w-10 rounded-xl bg-base-100 dark:bg-base-800 text-base-700 dark:text-base-200 flex items-center justify-center">
                                    {!! $icons[$p] !!}
                                </span>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $labels[$p] ?? ucfirst($p) }}
                                    </h3>
                                    <div class="mt-1 text-sm">
                                        @if($conn)
                                            <span class="inline-flex items-center gap-1.5 text-green-700 dark:text-green-300">
                                                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                                Conectado
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 text-amber-700 dark:text-amber-300">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                No conectado
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                @if(!$conn)
                                    <a href="{{ route('social.redirect', $p) }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-600 text-white hover:bg-primary-700">
                                        Conectar
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('social.disconnect', $p) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700"
                                                onclick="return confirm('¿Desconectar {{ $labels[$p] ?? ucfirst($p) }}?')">
                                            Desconectar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        @if($conn)
                            <div class="mt-4 grid gap-2 text-xs text-base-600 dark:text-base-400">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-md bg-base-100 dark:bg-base-800">
                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 7h18M3 12h14M3 17h10"/></svg>
                                    </span>
                                    <span><span class="opacity-70">ID proveedor:</span> {{ $conn->provider_user_id }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-md bg-base-100 dark:bg-base-800">
                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                    </span>
                                    <span><span class="opacity-70">Conectado el:</span> {{ optional($conn->created_at)->format('Y-m-d H:i') }}</span>
                                </div>
                                @if(!empty($conn->scopes))
                                    <div class="flex items-start gap-2">
                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-md bg-base-100 dark:bg-base-800">
                                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 6v12M6 12h12"/></svg>
                                        </span>
                                        <span><span class="opacity-70">Scopes:</span> {{ implode(', ', $conn->scopes) }}</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="mt-4 text-xs text-base-600 dark:text-base-400">
                                Conecta tu cuenta para poder publicar desde {{ $labels[$p] ?? ucfirst($p) }}.
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
