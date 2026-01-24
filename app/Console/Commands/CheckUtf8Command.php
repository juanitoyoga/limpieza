<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckUtf8Command extends Command
{
    protected $signature = 'db:check-utf8 {table=nominations}';
    protected $description = 'Detecta y limpia caracteres inválidos UTF-8 en columnas de texto';

    public function handle()
    {
        $table = $this->argument('table');
        $this->info("🔎 Revisando tabla: {$table}");

        $rows = DB::table($table)->get();

        foreach ($rows as $row) {
            foreach ($row as $column => $value) {
                if (is_string($value) && !mb_check_encoding($value, 'UTF-8')) {
                    $this->error("❌ Fila {$row->id}, columna {$column} contiene caracteres inválidos");

                    // Limpieza automática (opcional)
                    $clean = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    DB::table($table)->where('id', $row->id)->update([$column => $clean]);

                    $this->info("✅ Fila {$row->id}, columna {$column} limpiada");
                }
            }
        }

        $this->info("🎉 Revisión completa");
    }
}

