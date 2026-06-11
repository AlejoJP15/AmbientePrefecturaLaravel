<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ParroquiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            // 1. Obtener ID de Chimborazo
            $provincia = DB::table('provincia')
                ->where('nombre_provincia', 'Chimborazo')
                ->first();

            if (!$provincia) {
                throw new \Exception("Provincia Chimborazo no encontrada. Ejecuta ProvinciaSeeder primero.");
            }

            // 2. Mapeo completo de cantones y parroquias
            $data = [
                'Riobamba' => [
                    'Riobamba',
                    'Licto',
                    'Pungalá',
                    'Punín',
                    'Quimiag',
                    'San Juan',
                    'San Luis',
                    'Cacha',
                    'Calpi',
                    'Cubijíes',
                    'Flores',
                    'Licán',
                    'Pallatanga',
                    'Penipe',
                    'Maldonado',
                    'Bilbao'
                ],
                'Alausí' => [
                    'Alausí',
                    'Achupallas',
                    'Cumandá',
                    'Guasuntos',
                    'Huigra',
                    'Sevilla',
                    'Sibambe',
                    'Tixán'
                ],
                'Colta' => [
                    'Villa La Unión',
                    'Cajabamba',
                    'Cañi',
                    'Juan de Velasco',
                    'Santiago de Quito'
                ],
                'Chambo' => [
                    'Chambo'
                ],
                'Chunchi' => [
                    'Chunchi',
                    'Capzol',
                    'Compud',
                    'Gonzol',
                    'Llagos'
                ],
                'Guamote' => [
                    'Guamote',
                    'Cebadas',
                    'Palmira'
                ],
                'Pallatanga' => [
                    'Pallatanga',
                    'El Corazón',
                    'Bilbao'
                ],
                'Penipe' => [
                    'Penipe',
                    'El Altar',
                    'Matus',
                    'Puela',
                    'San Andrés',
                    'La Candelaria',
                    'Bayushig'
                ],
                'Cumandá' => [
                    'Cumandá'
                ]
            ];

            foreach ($data as $cantonNombre => $parroquias) {
                // 3. Buscar el cantón
                $canton = DB::table('canton')
                    ->where('nombre_canton', $cantonNombre)
                    ->where('id_provincia', $provincia->id_provincia)
                    ->first();

                if (!$canton) {
                    throw new \Exception("Cantón $cantonNombre no encontrado. Ejecuta CantonSeeder primero.");
                }

                // 4. Insertar parroquias
                foreach ($parroquias as $parroquiaNombre) {
                    $exists = DB::table('parroquia')
                        ->where('nombre_parroquia', $parroquiaNombre)
                        ->where('id_canton', $canton->id_canton)
                        ->exists();

                    if (!$exists) {
                        DB::table('parroquia')->insert([
                            'nombre_parroquia' => $parroquiaNombre,
                            'id_canton' => $canton->id_canton,
                        ]);
                    }
                }
            }

            DB::commit();
            $this->command->info('Parroquias de Chimborazo insertadas correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error: ' . $e->getMessage());
        }
    }
}
