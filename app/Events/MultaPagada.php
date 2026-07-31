<?php

namespace App\Events;

use App\Models\Multa;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MultaPagada
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Multa $multa,
    ) {}
}
