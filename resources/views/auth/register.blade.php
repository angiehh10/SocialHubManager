<x-guest-layout>
    <div class="max-w-md mx-auto mt-16 p-6 card">
        <h1 class="text-2xl font-semibold mb-6 text-center">Crear cuenta</h1>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label class="label" for="name">Nombre completo</label>
                <input id="name" class="input @error('name') border-red-500 @enderror"
                       type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                @error('name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="label" for="email">Correo electrónico</label>
                <input id="email" class="input @error('email') border-red-500 @enderror"
                       type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                @error('email')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="label" for="password">Contraseña</label>
                <input id="password" class="input @error('password') border-red-500 @enderror"
                       type="password" name="password" required autocomplete="new-password">
                @error('password')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="label" for="password_confirmation">Confirmar contraseña</label>
                <input id="password_confirmation" class="input"
                       type="password" name="password_confirmation" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary w-full">Registrarme</button>
        </form>

        <p class="text-center text-sm mt-6">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" class="text-primary-700 hover:underline">Inicia sesión</a>
        </p>
    </div>
</x-guest-layout>
