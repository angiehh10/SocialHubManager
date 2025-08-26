<x-app-layout>
    <div class="max-w-5xl mx-auto p-6 space-y-8">
        @if (session('status'))<div class="mb-4 text-sm text-green-700 bg-green-50 p-3 rounded-xl">{{ session('status') }}</div>@endif

        <div>
            <h2 class="text-xl font-semibold mb-3">Pendientes (cola / programadas)</h2>
            @forelse($pending as $p)
                <div class="rounded-xl border p-4 mb-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium">{{ $p->title ?? 'Sin título' }}</div>
                            <div class="text-xs text-base-500">
                                Estado: {{ $p->status }} · {{ $p->scheduled_for ? 'Prog: '.$p->scheduled_for->format('Y-m-d H:i') : 'Sin fecha (cola)' }}
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a class="btn btn-secondary"
                                href="{{ route('posts.schedule.edit', $p->id) }}">
                                Actualizar fecha
                            </a>
                            <form method="POST" action="{{ route('queue.cancel', $p->id) }}">
                                @csrf <button class="px-3 py-1.5 rounded-lg bg-base-100">Cancelar</button>
                            </form>
                            <form method="POST" action="{{ route('queue.send_now', $p->id) }}">
                                @csrf <button class="px-3 py-1.5 rounded-lg bg-primary-600 text-white">Enviar ahora</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-sm text-base-600">No tienes publicaciones pendientes.</div>
            @endforelse
        </div>

        <div>
            <h2 class="text-xl font-semibold mb-3">Histórico</h2>
            @forelse($published as $p)
                <div class="rounded-xl border p-4 mb-3">
                    <div class="font-medium">{{ $p->title ?? 'Sin título' }}</div>
                    <div class="text-xs text-base-500">
                        Publicado: {{ optional($p->published_at)->format('Y-m-d H:i') }} · Estado: {{ $p->status }}
                    </div>
                </div>
            @empty
                <div class="text-sm text-base-600">Aún no hay publicaciones publicadas.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
