<?php

namespace App\Livewire\Operacion\Nominations;

use Livewire\Component;

use App\Models\Nomination;

use Livewire\Attributes\Layout;

use App\Services\GenerarDocumentoNominacion;

#[Layout('layouts.operacion')]
class Imprimir extends Component
{
    public $nomination;

    public $path;

    private $generarDocumentoNominacion;

    public function mount($id, GenerarDocumentoNominacion $generarDocumentoNominacion)
    {
        $this->nomination = Nomination::with([
            'nominator',
            'candidate',
            'verifier',
            'approver',
            'rejecter'
        ])->findOrFail($id);
        $this->generarDocumentoNominacion = $generarDocumentoNominacion;
        $this->imprimir();
    }


    public function imprimir()
    {


        if (!$this->nomination->nominator || !$this->nomination->candidate) {
            $this->dispatch('notify', message: 'Falta información del nominador o candidato');
            return;
        }
        $this->path = $this->generarDocumentoNominacion->generarDocumentoCreado($this->nomination);
    }

    public function render()
    {

        return view('livewire.operacion.nominations.imprimir');
    }
}
