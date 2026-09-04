<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\PagoMultaJob;

class PagoMultaCommand extends Command
{
    protected $signature = 'multas:simular-pagos';
    protected $description = 'Simula el pago de todas las multas pendientes vencidas y distribuye ingresos contables';

    public function handle(): int
    {
        PagoMultaJob::dispatch();
        return self::SUCCESS;
    }
}
