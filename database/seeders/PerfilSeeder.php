<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PerfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            $perfiles = [
                'Super usuario',
                'Administrador',
                'Director',
                'Coordinador',
                'Técnico',
                'Usuario externo'
            ];

            foreach ($perfiles as $descripcion) {
                // Verificar si el perfil ya existe para evitar duplicados
                $exists = DB::table('perfil')
                    ->where('descripcion', $descripcion)
                    ->exists();

                if (!$exists) {
                    DB::table('perfil')->insert([
                        'descripcion' => $descripcion,
                    ]);
                }
            }

            DB::commit();
            $this->command->info('Perfiles insertados correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error al insertar perfiles: ' . $e->getMessage());
        }
    }
}
