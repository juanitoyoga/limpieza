<div class="p-6 bg-white shadow rounded w-full max-w-3xl mx-auto">

    <h2 class="text-xl font-bold mb-4">Crear Nombramiento</h2>

    <!-- Mensaje de éxito -->
    <div 
        x-data="{ show: false }"
        x-on:nomination-created.window="
            show = true;
            setTimeout(() => show = false, 3000);
        "
        x-show="show"
        class="p-3 mb-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded"
    >
        Nombramiento creado correctamente.
    </div>

    <!-- Formulario -->
    <form wire:submit.prevent="save" class="space-y-4">

        <!-- Select de candidato -->
        <div>
            <label class="block font-semibold mb-1">Seleccione al candidato</label>
            <select wire:model="candidate_user_id"
                    class="w-full border px-3 py-2 rounded">
                <option value="">-- Seleccione --</option>

                @foreach ($users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>

            @error('candidate_user_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Select de rol -->
        <div>
            <label class="block font-semibold mb-1">Rol a asignar</label>
            <select wire:model="role"
                    class="w-full border px-3 py-2 rounded">
                <option value="">-- Seleccione un rol --</option>
                <option value="presidente">Presidente</option>
                <option value="dirigente">Dirigente</option>
                <option value="auditor">Auditor</option>
                <option value="funcionario">Funcionario</option>
                <option value="administrador">Administrador</option>
            </select>

            @error('role')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Razón -->
        <div>
            <label class="block font-semibold mb-1">Motivo</label>
            <textarea wire:model="reason"
                      class="w-full border px-3 py-2 rounded h-28"
                      placeholder="Explique por qué el candidato debe ocupar este rol..."
            ></textarea>

            @error('reason')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Botón -->
        <div>
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Guardar Nombramiento
            </button>
        </div>

    </form>
</div>
