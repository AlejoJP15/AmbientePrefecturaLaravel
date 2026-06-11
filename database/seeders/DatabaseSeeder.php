<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Ejecutar este archivo para llenar las tablas
        $this->call([
            ProvinciaSeeder::class,
            CantonSeeder::class,
            ParroquiaSeeder::class,
            PerfilSeeder::class,
            EstadoPersonaSeeder::class,
            ActividadSeeder::class,
        ]);
    }
}
