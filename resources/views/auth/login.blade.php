<x-guest-layout>
    <div class="max-w-md mx-auto mt-16 p-6 card">
        <h1 class="text-2xl font-semibold mb-6 text-center">Iniciar sesión</h1>

        @if (session('status'))
            <div class="mb-4 text-sm text-green-700 bg-green-50 p-3 rounded-xl">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label class="label" for="email">Correo electrónico</label>
                <input id="email" class="input @error('email') border-red-500 @enderror"
                       type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                @error('email')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="label" for="password">Contraseña</label>
                <input id="password" class="input @error('password') border-red-500 @enderror"
                       type="password" name="password" required autocomplete="current-password">
                @error('password')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="remember" class="rounded border-base-300">
                    <span>Recuérdame</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm hover:underline text-primary-700"
                       href="{{ route('password.request') }}">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <button type="submit" class="btn btn-primary w-full">Entrar</button>
        </form>

        <p class="text-center text-sm mt-6">
            ¿No tienes cuenta?
            <a href="{{ route('register') }}" class="text-primary-700 hover:underline">Crear cuenta</a>
        </p>
    </div>
</x-guest-layout>
