<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProvinciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verifica si la provincia ya existe para no duplicar
        $exists = DB::table('provincia')
            ->where('nombre_provincia', 'Chimborazo')
            ->exists();

        if (!$exists) {
            DB::table('provincia')->insert([
                'nombre_provincia' => 'Chimborazo', // Asegúrate que coincida con el nombre de columna real
            ]);
        }
    }
}
