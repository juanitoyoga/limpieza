@section('page-title', 'Logs del Sistema')
@section('page-description', 'Auditoría técnica y trazabilidad de eventos')

<div class="space-y-4">
    <livewire:operacion.log-sistema.filtro />
    <livewire:operacion.log-sistema.lista />
</div>