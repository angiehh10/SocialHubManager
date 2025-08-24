<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">
        @if (session('status'))<div class="mb-4 text-sm text-green-700 bg-green-50 p-3 rounded-xl">{{ session('status') }}</div>@endif
        <h1 class="text-2xl font-semibold mb-6">Horarios de publicación</h1>

        <form method="POST" action="{{ route('schedules.store') }}" class="grid md:grid-cols-3 gap-3 items-end">
            @csrf
            <div>
                <label class="label">Día</label>
                <select name="weekday" class="input w-full">
                    @php $dias = [0=>'Dom',1=>'Lun',2=>'Mar',3=>'Mié',4=>'Jue',5=>'Vie',6=>'Sáb']; @endphp
                    @foreach($dias as $d => $n)
                        <option value="{{ $d }}">{{ $n }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Hora</label>
                <input type="time" name="time" class="input w-full" required>
            </div>
            <div>
                <button class="btn btn-primary w-full">Agregar</button>
            </div>
        </form>

        <div class="mt-6 rounded-xl border">
            <table class="min-w-full text-sm">
                <thead><tr class="bg-base-100">
                    <th class="text-left p-3">Día</th>
                    <th class="text-left p-3">Hora</th>
                    <th class="text-left p-3">Estado</th>
                    <th class="p-3"></th>
                </tr></thead>
                <tbody>
                @foreach($items as $it)
                    <tr class="border-t">
                        <td class="p-3">{{ $dias[$it->weekday] ?? $it->weekday }}</td>
                        <td class="p-3">{{ \Illuminate\Support\Str::of($it->time->format('H:i')) }}</td>
                        <td class="p-3">{{ $it->active ? 'Activo' : 'Inactivo' }}</td>
                        <td class="p-3">
                            <form method="POST" action="{{ route('schedules.destroy', $it->id) }}" onsubmit="return confirm('Eliminar horario?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
