<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;


class CombinadorHashEvidencias
{
    /** Hash determinístico e independiente del orden de subida. */
    public static function calcular(Collection $evidencias): ?string
    {
        if ($evidencias->isEmpty()) return null;

        $combinado = $evidencias
            ->pluck('hash_archivo')
            ->sort()
            ->implode('');

        return hash('sha256', $combinado);
    }
}

class EvidenciaRequeridaException extends \DomainException {}

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
