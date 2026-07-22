<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Relations\MorphMany;


trait RequiereEvidenciaParaTransicion
{
    abstract protected function relacionEvidencias(): MorphMany;
    abstract protected function estadosQueRequierenEvidencia(): array;

    public function tieneEvidencia(): bool
    {
        return $this->relacionEvidencias()->exists();
    }

    protected function validarEvidenciaPara(string $nuevoEstado): void
    {
        if (in_array($nuevoEstado, $this->estadosQueRequierenEvidencia(), true) && !$this->tieneEvidencia()) {
            throw new EvidenciaRequeridaException(
                class_basename($this) . " #{$this->id} no puede pasar a '{$nuevoEstado}' sin evidencia adjunta."
            );
        }
    }

    protected function hashEvidenciaSiAplica(string $nuevoEstado): ?string
    {
        return in_array($nuevoEstado, $this->estadosQueRequierenEvidencia(), true)
            ? CombinadorHashEvidencias::calcular($this->relacionEvidencias()->get())
            : null;
    }
}
