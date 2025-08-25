<x-app-layout>
    <div class="max-w-3xl mx-auto p-6"
         x-data="{
            providerChoice: '{{ old('provider_choice', request('pref','')) }}',
            mode: '{{ old('mode','now') }}',
            chooseDate: {{ old('choose_date') ? 'true' : 'false' }}
         }">

        {{-- Flash: éxito --}}
        @if (session('status'))
            <div x-data="{show:true}" x-show="show"
                 x-init="setTimeout(()=>show=false, 5000)"
                 class="mb-4 rounded-xl border border-green-200/70 bg-green-50/70 dark:border-green-900/40 dark:bg-green-900/20 px-4 py-3 text-sm text-green-800 dark:text-green-200">
                {{ session('status') }}
            </div>
        @endif

        {{-- Flash: error --}}
        @if (session('error'))
            <div x-data="{show:true}" x-show="show"
                 class="mb-4 rounded-xl border border-red-200/70 bg-red-50/70 dark:border-red-900/40 dark:bg-red-900/20 px-4 py-3 text-sm text-red-800 dark:text-red-200">
                {{ session('error') }}
            </div>
        @endif

        {{-- Flash: aviso --}}
        @if (session('warning'))
            <div x-data="{show:true}" x-show="show"
                 class="mb-4 rounded-xl border border-amber-200/70 bg-amber-50/70 dark:border-amber-900/40 dark:bg-amber-900/20 px-4 py-3 text-sm text-amber-800 dark:text-amber-200">
                {{ session('warning') }}
            </div>
        @endif

        {{-- Resumen de validación --}}
        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-200/70 bg-red-50/70 dark:border-red-900/40 dark:bg-red-900/20 px-4 py-3">
                <div class="text-sm font-semibold text-red-800 dark:text-red-200 mb-2">
                    Por favor corrige los siguientes errores:
                </div>
                <ul class="list-disc ms-5 text-sm text-red-800 dark:text-red-200 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h1 class="text-2xl font-semibold mb-6">Nueva publicación</h1>

        <form method="POST" action="{{ route('posts.store') }}" class="space-y-6">
            @csrf

            {{-- Paso 1: Destino --}}
            <div class="rounded-2xl border p-5">
                <h2 class="font-semibold mb-3">¿Dónde quieres publicar?</h2>
                @error('provider_choice')<p class="text-sm text-red-600 mb-2">{{ $message }}</p>@enderror

                <div class="grid sm:grid-cols-2 gap-4">
                    <label class="cursor-pointer rounded-xl border p-4 hover:shadow transition"
                           :class="providerChoice === 'reddit' ? 'ring-2 ring-primary-500' : ''">
                        <input type="radio" name="provider_choice" value="reddit" class="sr-only"
                               @change="providerChoice='reddit'">
                        <div class="flex items-center gap-3">
                            <span class="h-10 w-10 rounded-xl bg-base-100 dark:bg-base-800 flex items-center justify-center">
                                {{-- icono reddit --}}
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor">
                                    <circle cx="12" cy="12" r="10" class="opacity-20"></circle>
                                    <path d="M18 13a6 6 0 0 1-12 0c0-.1 0-.2.01-.31A2 2 0 0 1 8 11c.52 0 .99.21 1.33.54A6.9 6.9 0 0 1 12 11c.98 0 1.9.22 2.67.54.34-.33.81-.54 1.33-.54 1.1 0 2 .9 2 2 0 .1 0 .2-.01.31z"/>
                                </svg>
                            </span>
                            <div>
                                <div class="font-medium">Reddit</div>
                                <div class="text-xs text-base-500">Texto o enlace a un subreddit</div>
                            </div>
                        </div>
                    </label>

                    <label class="cursor-pointer rounded-xl border p-4 hover:shadow transition"
                           :class="providerChoice === 'discord' ? 'ring-2 ring-primary-500' : ''">
                        <input type="radio" name="provider_choice" value="discord" class="sr-only"
                               @change="providerChoice='discord'">
                        <div class="flex items-center gap-3">
                            <span class="h-10 w-10 rounded-xl bg-base-100 dark:bg-base-800 flex items-center justify-center">
                                {{-- icono discord --}}
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor">
                                    <path d="M20 4a16 16 0 0 0-4-.9l-.2.4a14 14 0 0 1 3.4 1.2c-3-1.5-6.3-1.5-9.4 0A14 14 0 0 1 12 3.5l-.2-.4A16 16 0 0 0 8 4C3.7 7.9 3 12.6 3.3 17.3 5 18.7 7 19.7 9.2 20l.5-1.3c-.9-.3-1.8-.7-2.6-1.2.2-.1.4-.3.6-.4 2.4 1.1 5.2 1.1 7.6 0 .2.1.4.3.6.4-.8.5-1.7.9-2.6 1.2l.5 1.3c2.2-.3 4.2-1.3 5.9-2.7C21 12.6 20.3 7.9 16 4ZM9.8 14.5c-.8 0-1.5-.8-1.5-1.7s.7-1.7 1.5-1.7 1.5.8 1.5 1.7-.7 1.7-1.5 1.7Zm4.4 0c-.8 0-1.5-.8-1.5-1.7s.7-1.7 1.5-1.7 1.5.8 1.5 1.7-.7 1.7-1.5 1.7Z"/>
                                </svg>
                            </span>
                            <div>
                                <div class="font-medium">Discord</div>
                                <div class="text-xs text-base-500">Enviar a un canal vía webhook</div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Paso 2: Contenido (SOLO Reddit) --}}
            <div class="rounded-2xl border p-5" x-show="providerChoice === 'reddit'">
                <h2 class="font-semibold mb-3">Contenido (Reddit)</h2>
                <div class="grid gap-4">
                    <div>
                        <label class="label">Título (usar etiqueta [text])</label>
                        <input name="title" class="input w-full" value="{{ old('title') }}">
                        @error('title')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="label">Contenido</label>
                        <textarea name="body" rows="4" class="input w-full">{{ old('body') }}</textarea>
                        @error('body')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Campos específicos por destino --}}
            <div class="rounded-2xl border p-5 space-y-6">
                <h2 class="font-semibold">Ajustes del destino</h2>

                {{-- Reddit --}}
                <div x-show="providerChoice === 'reddit'">
                    <h3 class="font-medium mb-2">Reddit</h3>
                    <div class="grid md:grid-cols-3 gap-3">
                        <div>
                            <label class="label">Subreddit</label>
                            <input name="reddit[subreddit]" class="input w-full" placeholder="p.ej. r/testprojectisw" value="{{ old('reddit.subreddit') }}">
                            @error('reddit.subreddit')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label">Tipo</label>
                            <select name="reddit[kind]" class="input w-full">
                                <option value="self" @selected(old('reddit.kind')==='self')>Texto</option>
                                <option value="link" @selected(old('reddit.kind')==='link')>Enlace</option>
                            </select>
                            <p class="text-xs text-base-500 mt-1">Si eliges “Enlace”, indica la URL abajo.</p>
                        </div>
                        <div>
                            <label class="label">Enlace (opcional)</label>
                            <input name="link_url" class="input w-full" value="{{ old('link_url') }}" placeholder="https://...">
                            @error('link_url')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Discord --}}
                <div x-show="providerChoice === 'discord'" x-cloak>
                    <h3 class="font-medium mb-2">Discord</h3>

                    @if($discordWebhooks->count())
                        <div class="grid md:grid-cols-2 gap-4">
                            {{-- Selección de un canal guardado --}}
                            <div class="rounded-2xl border p-4">
                                <label class="label mb-2">Enviar a (canal guardado)</label>

                                <div class="space-y-2 max-h-56 overflow-auto pr-1">
                                    @foreach($discordWebhooks as $wh)
                                        @php
                                            $guildLabel   = $wh->guild_name ?? null;
                                            $channelLabel = $wh->channel_name ?? null;
                                            $webhookLabel = $wh->name ?? null;
                                            $urlShort     = \Illuminate\Support\Str::limit($wh->url, 36);
                                        @endphp

                                        <label class="flex items-start gap-3 p-2 rounded-lg hover:bg-base-50 dark:hover:bg-base-800/60 border border-base-200 dark:border-base-700">
                                            <input
                                                type="radio"
                                                name="discord[webhook_id]"
                                                value="{{ $wh->id }}"
                                                class="mt-1"
                                                :disabled="providerChoice !== 'discord'">
                                            <div class="text-sm">
                                                <div class="font-medium">
                                                    @if($guildLabel)
                                                        <span class="opacity-70">{{ $guildLabel }}</span> ·
                                                    @endif

                                                    @if($channelLabel)
                                                        <span>#{{ $channelLabel }}</span>
                                                    @else
                                                        Canal {{ $wh->channel_id }}
                                                    @endif
                                                </div>

                                                <div class="text-xs text-base-500">
                                                    @if($webhookLabel)
                                                        Webhook: {{ $webhookLabel }} ·
                                                    @endif
                                                    {{ $urlShort }}
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                <p class="text-xs text-base-500 mt-2">
                                    Estos canales provienen de tu conexión OAuth con Discord.
                                </p>
                            </div>

                            {{-- Mensaje --}}
                            <div class="rounded-2xl border p-4">
                                <label class="label">Mensaje (Discord)</label>
                                <textarea name="discord[message]" rows="6" class="input w-full"
                                        :disabled="providerChoice !== 'discord'">{{ old('discord.message') }}</textarea>
                                @error('discord.message')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    @else
                        {{-- Sin webhooks guardados --}}
                        <div class="rounded-2xl border p-4">
                            <p class="text-sm">
                                No tienes canales guardados. <a href="{{ route('social.connections') }}" class="underline">Conecta Discord</a> y elige un canal para guardarlo aquí.
                            </p>
                        </div>
                    @endif
                </div>

                            
                {{-- Paso 3: Modo de envío --}}
                <div class="rounded-2xl border p-5">
                    <h2 class="font-semibold mb-3">Modo de envío</h2>

                    <div class="grid md:grid-cols-3 gap-3">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="mode" value="now"
                                @change="mode='now'" :checked="mode==='now'">
                            <span>Publicar ahora</span>
                        </label>

                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="mode" value="queue"
                                @change="mode='queue'" :checked="mode==='queue'">
                            <span>Enviar a cola (sin horario)</span>
                        </label>

                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="mode" value="queue_slot"
                                @change="mode='queue_slot'" :checked="mode==='queue_slot'">
                            <span>Enviar a cola (usar horario)</span>
                        </label>
                    </div>

                {{-- Selector de horario cuando el modo es "queue_slot" --}}
                <div class="mt-4" x-show="mode==='queue_slot'" x-cloak>
                    <div class="rounded-2xl border p-4 space-y-4">
                        @if($slots->count())
                            <div>
                                <label class="label mb-2">Elige un horario</label>
                                <div class="space-y-2 max-h-60 overflow-auto pr-1">
                                    @php $days = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb']; @endphp
                                    @foreach($slots as $s)
                                        @php
                                            $timeStr = $s->time instanceof \DateTimeInterface
                                                ? $s->time->format('H:i')
                                                : \Carbon\Carbon::parse($s->time)->format('H:i');
                                        @endphp
                                        <label class="flex items-center gap-3 p-2 rounded-xl border hover:bg-base-50 dark:hover:bg-base-800/60">
                                            <input type="radio" name="schedule_slot_id"
                                                value="{{ $s->id }}"
                                                {{ old('schedule_slot_id')==$s->id ? 'checked' : '' }}>
                                            <span class="text-sm">
                                                <span class="font-medium">{{ $days[$s->weekday] }}</span>
                                                <span class="opacity-70">·</span>
                                                <span>{{ $timeStr }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('schedule_slot_id')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
                            </div>

                            {{-- NUEVO: ¿Quieres elegir la fecha exacta? --}}
                            <div class="rounded-xl border p-3">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="choose_date" value="1" x-model="chooseDate">
                                    <span class="text-sm">Elegir fecha exacta</span>
                                </label>

                                <div class="mt-3 grid sm:grid-cols-2 gap-3" x-show="chooseDate" x-cloak>
                                    <div>
                                        <label class="label">Fecha</label>
                                        @php
                                            $tz = auth()->user()->timezone ?? 'America/Costa_Rica';
                                            $minDate = \Carbon\Carbon::now($tz)->toDateString();
                                        @endphp
                                        <input type="date"
                                            name="schedule_date"
                                            class="input w-full"
                                            min="{{ $minDate }}"
                                            value="{{ old('schedule_date') }}">
                                        <p class="text-xs text-base-500 mt-1">
                                            Días anteriores están desactivados. Si eliges una fecha pasada por error, se moverá una semana adelante.
                                        </p>
                                        @error('schedule_date')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                </div>

                            </div>
                        @else
                            <div class="text-sm text-base-600">
                                Aún no tienes horarios. <a class="underline" href="{{ route('schedules.index') }}">Crea tus horarios</a>.
                            </div>
                        @endif
                    </div>
                </div>


                <div class="flex items-center justify-end gap-3">
        <a href="{{ route('queue.index') }}" class="px-4 py-2 rounded-xl ring-1 ring-base-300 hover:ring-base-400">Cancelar</a>
        <button class="btn btn-primary">Guardar</button>
    </div>
</form>
</div>
</x-app-layout>


