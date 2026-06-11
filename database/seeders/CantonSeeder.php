<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CantonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        {
            // Obtener ID de Chimborazo
            $provincia = DB::table('provincia')
                ->where('nombre_provincia', 'Chimborazo')
                ->first();

            if ($provincia) {
                $cantones = [
                    ['nombre_canton' => 'Riobamba'],
                    ['nombre_canton' => 'Alausí'],
                    ['nombre_canton' => 'Colta'],
                    ['nombre_canton' => 'Chambo'],
                    ['nombre_canton' => 'Chunchi'],
                    ['nombre_canton' => 'Guamote'],
                    ['nombre_canton' => 'Pallatanga'],
                    ['nombre_canton' => 'Penipe'],
                    ['nombre_canton' => 'Cumandá'],
                    ['nombre_canton' => 'Guano']
                ];

                foreach ($cantones as $canton) {
                    // Verificar si el cantón ya existe
                    $exists = DB::table('canton')
                        ->where('nombre_canton', $canton['nombre_canton'])
                        ->where('id_provincia', $provincia->id_provincia)
                        ->exists();

                    if (!$exists) {
                        DB::table('canton')->insert([
                            'nombre_canton' => $canton['nombre_canton'],
                            'id_provincia' => $provincia->id_provincia,
                        ]);
                    }
                }
            }
        }
    }
}
