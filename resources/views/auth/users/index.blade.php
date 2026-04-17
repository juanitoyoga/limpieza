@include('partials._maincorto');
<body>


@include('partials._header');


<div class="container-fluid">
    <h2 class="mb-4">Listado de Usuarios</h2>

    <div class="table-responsive" style="max-height: 600px; overflow: auto;">
        <table class="table table-bordered table-hover table-striped text-nowrap">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre completo</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Fecha de nacimiento</th>
                    <th>Género</th>
                    <th>Idioma</th>
                    <th>Zona horaria</th>
                    <th>Último acceso</th>
                    <th>Activo</th>
                    <th>Avatar</th>
                    <th>Creado</th>
                    <th>Actualizado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->full_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? '—' }}</td>
                    <td>{{ optional($user->birthdate)->format('d/m/Y') }}</td>
                    <td>{{ $user->gender ?? '—' }}</td>
                    <td>{{ $user->language }}</td>
                    <td>{{ $user->timezone }}</td>
                    <td>{{ optional($user->last_login_at)->format('d/m/Y H:i') }}</td>
                    <td>
                        <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                            {{ $user->is_active ? 'Sí' : 'No' }}
                        </span>
                    </td>
                    <td>
                        <img src="{{ $user->avatar_url }}" alt="Avatar" width="40" height="40" class="rounded-circle">
                    </td>
                    <td>{{ optional($user->created_at)->format('d/m/Y') }}</td>
                    <td>{{ optional($user->updated_at)->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="13" class="text-center">No hay usuarios registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $users->links() }}
    </div>
</div>


@include('partials._footer');
</body>
</html>