<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EstadoPersonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            $estados = [
                'Activo',
                'Inactivo',
                'Bloqueado'
            ];

            foreach ($estados as $descripcion) {
                // Verificar si el estado ya existe para evitar duplicados
                $exists = DB::table('estado_persona')
                    ->where('descripcion', $descripcion)
                    ->exists();

                if (!$exists) {
                    DB::table('estado_persona')->insert([
                        'descripcion' => $descripcion,
                    ]);
                }
            }

            DB::commit();
            $this->command->info('Estados de persona insertados correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error al insertar estados de persona: ' . $e->getMessage());
        }
    }
}
