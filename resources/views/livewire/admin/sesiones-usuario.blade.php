<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold">Sesiones activas</h2>
            <p class="text-sm text-gray-500">{{ $usuario->nombre_completo ?? $usuario->email }}</p>
        </div>

        @if ($tokens->count() > 0)
            <button
                wire:click="confirmarRevocarTodas"
                class="px-4 py-2 rounded-md bg-red-600 text-white text-sm hover:bg-red-700"
            >
                Revocar todas las sesiones
            </button>
        @endif
    </div>

    @if ($tokens->isEmpty())
        <p class="text-gray-500 text-sm">Este usuario no tiene sesiones activas.</p>
    @else
        <div class="overflow-x-auto border rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Dispositivo / nombre</th>
                        <th class="px-4 py-2 text-left">Creado</th>
                        <th class="px-4 py-2 text-left">Último uso</th>
                        <th class="px-4 py-2 text-left">Expira</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($tokens as $token)
                        <tr>
                            <td class="px-4 py-2">{{ $token->name }}</td>
                            <td class="px-4 py-2">{{ $token->created_at?->diffForHumans() }}</td>
                            <td class="px-4 py-2">
                                {{ $token->last_used_at?->diffForHumans() ?? 'Nunca' }}
                            </td>
                            <td class="px-4 py-2">
                                @if ($token->expires_at)
                                    {{ $token->expires_at->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-gray-400">Sin expiración</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right">
                                <button
                                    wire:click="confirmarRevocar({{ $token->id }})"
                                    class="text-red-600 hover:underline text-sm"
                                >
                                    Revocar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Modal: confirmar revocación individual --}}
    @if ($tokenIdAConfirmar)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-sm w-full space-y-4">
                <h3 class="font-semibold">¿Revocar esta sesión?</h3>
                <p class="text-sm text-gray-600">
                    El dispositivo perderá acceso inmediatamente y tendrá que volver a iniciar sesión.
                </p>
                <div class="flex justify-end gap-2">
                    <button wire:click="cancelarConfirmacion" class="px-4 py-2 text-sm rounded-md border">
                        Cancelar
                    </button>
                    <button
                        wire:click="revocar({{ $tokenIdAConfirmar }})"
                        class="px-4 py-2 text-sm rounded-md bg-red-600 text-white"
                    >
                        Sí, revocar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: confirmar revocación masiva --}}
    @if ($confirmandoRevocarTodas)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-sm w-full space-y-4">
                <h3 class="font-semibold">¿Revocar TODAS las sesiones?</h3>
                <p class="text-sm text-gray-600">
                    Todos los dispositivos de este usuario perderán acceso inmediatamente,
                    incluyendo la app móvil si estaba en medio de una sincronización.
                </p>
                <div class="flex justify-end gap-2">
                    <button wire:click="cancelarConfirmacion" class="px-4 py-2 text-sm rounded-md border">
                        Cancelar
                    </button>
                    <button wire:click="revocarTodas" class="px-4 py-2 text-sm rounded-md bg-red-600 text-white">
                        Sí, revocar todas
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
