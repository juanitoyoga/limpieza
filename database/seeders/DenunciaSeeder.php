<?php

namespace Database\Seeders;

use App\Models\Vecino;
use App\Models\Barrio;
use App\Models\Ordenanza332;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DenunciaSeeder extends Seeder
{
    /**
     * Genera denuncias variadas para alimentar dashboards:
     * - Distintos estados (pendiente, en_proceso, resuelto, rechazado)
     * - Distintas ordenanzas
     * - Distintos estados de blockchain (confirmed, failed, pending)
     * - Fechas distribuidas en los últimos ~90 días
     * - Verificación / aprobación / rechazo con rol + id
     */
    public function run(): void
    {
        $vecinos = Vecino::all();
        $ordenanzas = Ordenanza332::all();
        $barrios = Barrio::all();
        $dirigentes = DB::table('dirigentes')->get();
        $funcionarios = DB::table('funcionarios')->get();

        if ($vecinos->isEmpty() || $ordenanzas->isEmpty() || $barrios->isEmpty()) {
            $this->command->warn('No hay vecinos, ordenanzas o barrios para generar denuncias.');
            return;
        }

        $estadosPonderados = array_merge(
            array_fill(0, 4, 'Pendiente'),
            array_fill(0, 3, 'Verificado'),
            array_fill(0, 4, 'Aprobado'),
            array_fill(0, 1, 'Rechazado'),
        );

        $blockchainEstados = array_merge(
            array_fill(0, 7, 'Confirmado'),
            array_fill(0, 2, 'Errado'),
            array_fill(0, 1, 'Pendiente'),
        );

        $descripciones = [
            'Acumulación de basura en vía pública.',
            'Quema de desechos a cielo abierto.',
            'Disposición de escombros en terreno baldío.',
            'Comercio informal obstruyendo la vereda.',
            'Ruido excesivo fuera del horario permitido.',
            'Mal estado de contenedores de basura comunitarios.',
            'Vertido de aguas residuales en la calle.',
            'Animales sueltos generando desechos en parque.',
            'Grafiti y daños a mobiliario urbano.',
            'Obstrucción de alcantarilla con desechos sólidos.',
        ];

        $motivosRechazo = [
            'Evidencia insuficiente para verificar el hecho.',
            'La ubicación no corresponde al barrio reportado.',
            'Denuncia duplicada de un caso ya registrado.',
            'No se identifica una infracción dentro de la Ordenanza 332.',
        ];

        // Coordenadas aproximadas de Quito (variación pequeña para simular puntos distintos)
        $latBase = -0.1807;
        $lngBase = -78.4678;

        $totalDenuncias = 100;

        for ($i = 1; $i <= $totalDenuncias; $i++) {
            $vecino = $vecinos->random();
            $barrio = $barrios->random();
            $ordenanza = $ordenanzas->random();
            $estado = $estadosPonderados[array_rand($estadosPonderados)];
            $blockchainStatus = $blockchainEstados[array_rand($blockchainEstados)];
            $fecha = now()->subDays(rand(0, 90))->subHours(rand(0, 23));

            // ─── Verificación / Aprobación / Rechazo ───────────
            $verificadoPorId = null;
            $verificadoPorRol = null;
            $verificadoAt = null;

            $aprobadoPorId = null;
            $aprobadoPorRol = null;
            $aprobadoAt = null;

            $rechazadoPorId = null;
            $rechazadoPorRol = null;
            $rechazadoAt = null;
            $motivoRechazo = null;

            // Toda denuncia que avanzó de "pendiente" fue verificada por un dirigente
            if (in_array($estado, ['Pendiente', 'Verificado', 'Aprobado', 'Rechazado']) && $dirigentes->isNotEmpty()) {
                $verificadoPorId = $dirigentes->random()->id;
                $verificadoPorRol = 'Dirigente';
                $verificadoAt = $fecha->copy()->addHours(rand(1, 12));
            }

            // Resuelta => aprobada por un funcionario
            if ($estado === 'Aprobado' && $funcionarios->isNotEmpty()) {
                $aprobadoPorId = $funcionarios->random()->id;
                $aprobadoPorRol = 'Funcionario';
                $aprobadoAt = ($verificadoAt ?? $fecha)->copy()->addHours(rand(1, 24));
            }

            // Rechazada => rechazada por dirigente o funcionario
            if ($estado === 'Rechazado') {
                if ($funcionarios->isNotEmpty() && rand(0, 1)) {
                    $rechazadoPorId = $funcionarios->random()->id;
                    $rechazadoPorRol = 'Funcionario';
                } elseif ($dirigentes->isNotEmpty()) {
                    $rechazadoPorId = $dirigentes->random()->id;
                    $rechazadoPorRol = 'Dirigente';
                }
                $rechazadoAt = ($verificadoAt ?? $fecha)->copy()->addHours(rand(1, 24));
                $motivoRechazo = $motivosRechazo[array_rand($motivosRechazo)];
            }

            $descripcion = $descripciones[array_rand($descripciones)];
            $multa = null;

            if ($estado === 'Aprobado' && $ordenanza->nivel_gravedad) {
                $multa = match (strtolower((string) $ordenanza->nivel_gravedad)) {
                    'leve' => 25.00,
                    'grave' => 50.00,
                    'gravisima', 'gravísima' => 100.00,
                    default => 30.00,
                };
            }

            $lat = $latBase + (mt_rand(-300, 300) / 10000);
            $lng = $lngBase + (mt_rand(-300, 300) / 10000);

            $fileHash = hash('sha256', "denuncia-{$i}-{$vecino->id}-{$ordenanza->id}-{$fecha->timestamp}");
            $txHash = $blockchainStatus !== 'failed'
                ? '0x' . hash('sha256', "tx-{$fileHash}")
                : null;

            DB::table('denuncias')->insert([
                'vecino_id'          => $vecino->id,
                'barrio_id'          => $barrio->id,
                'ordenanza332_id'    => $ordenanza->id,

                'direccion'          => 'Calle ' . chr(65 + ($i % 26)) . ' y Av. ' . (($i % 12) + 1) . ' de ' . ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo'][($i % 5)],
                'direccion_gps'      => 'Quito, Ecuador',
                'descripcion'        => $descripcion,
                'fecha_denuncia'     => $fecha,
                'estado'             => $estado,
                'multa_calculada'    => $multa,

                'evidencia_path'     => 'denuncias/seed_evidencia_' . $i . '.jpg',
                'evidencia_tipo'     => 'foto',

                'latitud'            => $lat,
                'longitud'           => $lng,

                // Verificación
                'verificado_por_id'  => $verificadoPorId,
                'verificado_por_rol' => $verificadoPorRol,
                'verificado_at'      => $verificadoAt,

                // Aprobación
                'aprobado_por_id'    => $aprobadoPorId,
                'aprobado_por_rol'   => $aprobadoPorRol,
                'aprobado_at'        => $aprobadoAt,

                // Rechazo
                'rechazado_por_id'   => $rechazadoPorId,
                'rechazado_por_rol'  => $rechazadoPorRol,
                'rechazado_at'       => $rechazadoAt,
                'motivo_rechazo'     => $motivoRechazo,

                'app_uuid'           => (string) Str::uuid(),
                'device_id'          => 'seed-device-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'os_version'         => 'Android 14',
                'app_version'        => '1.0.0',

                'synced'             => true,
                'synced_at'          => $fecha->copy()->addMinutes(5),

                'file_hash'          => $fileHash,
                'tx_hash'            => $txHash,
                'blockchain_status'  => $blockchainStatus,
                'verified_on_chain'  => $blockchainStatus === 'Confirmado',

                'created_at'         => $fecha,
                'updated_at'         => $fecha,
            ]);
        }
    }
}
