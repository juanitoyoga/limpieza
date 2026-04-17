<flux:dropdown position="bottom" align="start">
    <flux:profile
        :name="auth()->user()?->name ?? 'Invitado'"
        :initials="auth()->user()?->initials() ?? 'I'"
        icon-trailing="chevrons-up-down"
    />

    <flux:menu class="w-[220px]">
        <flux:menu.radio.group>
            <div class="p-0 text-sm font-normal">
                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                        <span
                            class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                        >
                            {{ auth()->user()?->initials() ?? 'I' }}
                        </span>
                    </span>

                    <div class="grid flex-1 text-start text-sm leading-tight">
                        <span class="truncate font-semibold">{{ auth()->user()?->name ?? 'Invitado' }}</span>
                        <span class="truncate text-xs">{{ auth()->user()?->email ?? 'Sin email' }}</span>
                    </div>
                </div>
            </div>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <flux:menu.radio.group>
            <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                {{ __('Settings') }}
            </flux:menu.item>
        </flux:menu.radio.group>

        {{-- Solo Administrador --}}
        @if(auth()->user()?->obtiene_rol?->name === 'Admin')
            <flux:menu.radio.group>
                <flux:menu.item :href="route('admin.dashboard')" icon="shield-check" wire:navigate>
                    {{ __('Administración') }}
                </flux:menu.item>
            </flux:menu.radio.group>
        @endif

        <flux:menu.radio.group>
            <flux:menu.item :href="route('operacion.layouts.welcome')" icon="home" wire:navigate>
                {{ __('LimpiaTuRincon') }}
            </flux:menu.item>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                {{ __('Log Out') }}
            </flux:menu.item>
        </form>
    </flux:menu>
</flux:dropdown>
