<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NominationSeeder extends Seeder
{
    /**
     * Crea una "nomination" aprobada por cada usuario con rol de cargo
     * (Presidente, Dirigente, Supervisor, Funcionario), requerido como FK
     * en sus tablas respectivas (nomination_id).
     *
     * El nominador (nominator_id) es el Admin.
     */
    public function run(): void
    {
        $admin = User::where('role_name', 'Admin')->first();

        $candidatos = User::whereIn('role_name', ['Presidente', 'Dirigente', 'Supervisor', 'Funcionario'])
            ->orderBy('id')
            ->get();

        foreach ($candidatos as $index => $user) {
            DB::table('nominations')->insert([
                'nominator_id'          => $admin->id,
                'candidate_user_id'     => $user->id,
                'role_name'             => $user->role_name,
                'issuer_type'           => 'DMQ',
                'released_by'           => 'Secretaría General DMQ',
                'document_path'         => null,
                'fecha_emision'         => now()->subMonths(6)->toDateString(),
                'fecha_inicio_vigencia' => now()->subMonths(6)->toDateString(),
                'fecha_fin_vigencia'    => now()->addYears(2)->toDateString(),
                'estado'                => 'aprobado',
                'observaciones'         => 'Nominación inicial generada por seeder.',
                'verified_by'           => $admin->id,
                'approved_by'           => $admin->id,
                'rejected_by'           => null,
                'verified_at'           => now()->subMonths(6),
                'approved_at'           => now()->subMonths(6),
                'rejected_at'           => null,
                'hash_reference'        => hash('sha256', 'nomination-' . $user->id . '-' . $index),
                'version'               => 1,
                'numero_tramite'        => 'NOM-' . now()->format('Y') . '-' . str_pad((string) ($index + 1), 5, '0', STR_PAD_LEFT),
                'is_active'             => true,
                'created_at'            => now()->subMonths(6),
                'updated_at'            => now()->subMonths(6),
            ]);
        }
    }
}
