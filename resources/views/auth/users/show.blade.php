@include('partials._main');
<body>


@include('partials._header');


@section('title', 'Detalle de Usuario')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Detalle del Usuario</h2>

    <div class="card">
        <div class="card-header d-flex align-items-center">
            <img src="{{ $user->avatar_url }}" alt="Avatar" class="rounded-circle me-3" width="60" height="60">
            <h5 class="mb-0">{{ $user->full_name }}</h5>
        </div>

        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">{{ $user->id }}</dd>

                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $user->email }}</dd>

                <dt class="col-sm-3">Teléfono</dt>
                <dd class="col-sm-9">{{ $user->phone ?? '—' }}</dd>

                <dt class="col-sm-3">Fecha de nacimiento</dt>
                <dd class="col-sm-9">{{ optional($user->birthdate)->format('d/m/Y') }}</dd>

                <dt class="col-sm-3">Género</dt>
                <dd class="col-sm-9">{{ $user->gender ?? '—' }}</dd>

                <dt class="col-sm-3">Idioma</dt>
                <dd class="col-sm-9">{{ $user->language }}</dd>

                <dt class="col-sm-3">Zona horaria</dt>
                <dd class="col-sm-9">{{ $user->timezone }}</dd>

                <dt class="col-sm-3">Último acceso</dt>
                <dd class="col-sm-9">{{ optional($user->last_login_at)->format('d/m/Y H:i') }}</dd>

                <dt class="col-sm-3">Activo</dt>
                <dd class="col-sm-9">
                    <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                        {{ $user->is_active ? 'Sí' : 'No' }}
                    </span>
                </dd>

                <dt class="col-sm-3">Creado</dt>
                <dd class="col-sm-9">{{ optional($user->created_at)->format('d/m/Y H:i') }}</dd>

                <dt class="col-sm-3">Actualizado</dt>
                <dd class="col-sm-9">{{ optional($user->updated_at)->format('d/m/Y H:i') }}</dd>
            </dl>
        </div>

        <div class="card-footer text-end">
            <a href="{{ route('users.index') }}" class="btn btn-secondary">← Volver al listado</a>
        </div>
    </div>
</div>
@endsection
@include('partials._footer');
</body>
</html>