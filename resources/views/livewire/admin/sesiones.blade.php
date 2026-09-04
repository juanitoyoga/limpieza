@extends('layouts.admin') {{-- ajustar al layout real que uses --}}

@section('content')
<livewire:admin.sesiones-usuario :user-id="$userId" />
@endsection