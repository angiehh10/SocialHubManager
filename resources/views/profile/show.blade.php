<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Perfil') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                @livewire('profile.update-profile-information-form')

                <x-section-border />
            @endif

            {{-- 🔗 Foto de perfil desde URL (opción adicional a la original) --}}
            <div class="mt-10 sm:mt-0">
                <x-action-section>
                    <x-slot name="title">
                        {{ __('Foto desde URL') }}
                    </x-slot>

                    <x-slot name="description">
                        {{ __('Pega la URL de una imagen (JPG/PNG/WEBP) para usarla como foto de perfil. Esta opción convive con la de subir archivo.') }}
                    </x-slot>

                    <x-slot name="content">
                        <form method="POST" action="{{ route('profile.photo-url') }}" class="max-w-xl space-y-3">
                            @csrf

                            <x-input id="avatar_url"
                                     name="avatar_url"
                                     type="url"
                                     class="w-full"
                                     placeholder="https://ejemplo.com/mi-avatar.jpg"
                                     required />

                            @error('avatar_url')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            @if (session('status') === 'photo-url-updated')
                                <p class="text-sm text-green-600">{{ __('Foto actualizada correctamente.') }}</p>
                            @endif

                            <x-button type="submit">
                                {{ __('Usar URL') }}
                            </x-button>
                        </form>
                    </x-slot>
                </x-action-section>
            </div>

            <x-section-border />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.update-password-form')
                </div>

                <x-section-border />
            @endif

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.two-factor-authentication-form')
                </div>

                <x-section-border />
            @endif

            <div class="mt-10 sm:mt-0">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <x-section-border />

                <div class="mt-10 sm:mt-0">
                    @livewire('profile.delete-user-form')
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
