<x-app-layout>
    <div class="max-w-md mx-auto p-6">
        <h1 class="text-xl font-semibold mb-4">Actualizar fecha</h1>

        <form method="POST" action="{{ route('posts.schedule.update', $post->id) }}">
            @csrf
            @method('PUT')

            <label class="label">Nueva fecha</label>
            <input
                type="date"
                name="schedule_date"
                class="input w-full"
                value="{{ $date }}"
                min="{{ now($tz)->toDateString() }}"
                required
            >
            @error('schedule_date')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror

            <p class="text-xs text-base-500 mt-2">
                La <strong>hora</strong> se mantiene: {{ $post->scheduled_for->timezone($tz)->format('H:i:s') }}.
            </p>

            <div class="mt-4 flex gap-3">
                <a href="{{ route('queue.index') }}" class="px-4 py-2 rounded-xl ring-1">Cancelar</a>
                <button class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</x-app-layout>
