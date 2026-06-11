<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ActividadSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/ciiu.csv');
        if (!file_exists($path)) {
            $this->command?->error("No se encontró el archivo: $path");
            return;
        }

        DB::transaction(function () use ($path) {
            // DB::statement('TRUNCATE TABLE public.actividad RESTART IDENTITY CASCADE');

            $handle = fopen($path, 'r');
            if ($handle === false) {
                throw new \RuntimeException("No se pudo abrir $path");
            }

            // 1) Detectar delimitador usando varias líneas
            $delimiter = $this->detectDelimiter($handle);

            // 2) Leer/ubicar la cabecera real (saltando títulos o líneas vacías)
            [$header, $idxCodigo, $idxDesc, $idxNivel] = $this->readRealHeader($handle, $delimiter);

            if ($idxCodigo === null || $idxDesc === null) {
                fclose($handle);
                throw new \InvalidArgumentException(
                    "El CSV debe tener columnas de 'codigo' y 'descripcion'. Cabeceras detectadas: ".implode(', ', $header)
                );
            }

            // 3) Procesar filas (SIN límites de tamaño)
            $batch = [];
            $seen = [];
            $batchSize = 1000;

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                // saltar filas totalmente vacías
                if (!$this->rowHasData($row)) continue;

                $codigo = isset($row[$idxCodigo]) ? trim((string)$row[$idxCodigo]) : '';
                $desc   = isset($row[$idxDesc])   ? trim((string)$row[$idxDesc])   : '';
                $nivel  = ($idxNivel !== null && isset($row[$idxNivel])) ? trim((string)$row[$idxNivel]) : null;

                if ($codigo === '' || $desc === '') continue;

                // SIN recortes ni cambios de mayúsculas
                if (isset($seen[$codigo])) continue;
                $seen[$codigo] = true;

                $batch[] = [
                    'codigo_actividad'      => $codigo, // atención: si tu columna tiene límite, ajusta en BD
                    'descripcion_actividad' => $desc,   // TEXT en BD -> se guarda completo
                    'nivel'                 => $nivel,  // si tiene límite en BD, ajusta el tipo/tamaño
                ];

                if (count($batch) >= $batchSize) {
                    DB::table('public.actividad')->insert($batch);
                    // Alternativa idempotente:
                    // DB::table('public.actividad')->upsert($batch, ['codigo_actividad'], ['descripcion_actividad','nivel']);
                    $batch = [];
                }
            }

            if (!empty($batch)) {
                DB::table('public.actividad')->insert($batch);
                // Alternativa idempotente:
                // DB::table('public.actividad')->upsert($batch, ['codigo_actividad'], ['descripcion_actividad','nivel']);
            }

            fclose($handle);
            $this->command?->info("Actividades cargadas correctamente (sin límites de tamaño en el seeder).");
        });
    }

    private function detectDelimiter($handle): string
    {
        $candidates = [',',';',"\\t",'|'];
        $scores = array_fill_keys($candidates, 0);

        for ($i=0; $i<10; $i++) {
            $line = fgets($handle);
            if ($line === false) break;
            foreach ($candidates as $d) {
                $char = $d === "\\t" ? "\t" : $d;
                $scores[$d] += substr_count($line, $char);
            }
        }
        // volver al inicio
        rewind($handle);

        // elegir el delimitador con mayor conteo (por defecto ;)
        arsort($scores);
        $best = array_key_first($scores);
        return $best === "\\t" ? "\t" : ($best ?: ';');
    }

    private function readRealHeader($handle, string $delimiter): array
    {
        // alias posibles (normalizados a ascii+lower)
        $aliasesCodigo = [
            'codigo','codigo_actividad','ciiu','code','cod',
            'codigo actividad economica','codigo de la actividad economica',
            'código actividad económica','código de la actividad económica'
        ];
        $aliasesDesc = [
            'descripcion','descripcion_actividad','detalle','actividad','nombre',
            'descripcion actividad economica','descripcion de la actividad economica',
            'descripción actividad económica','descripción de la actividad económica'
        ];
        $aliasesNivel = ['nivel','categoria','categoria_nivel','tipo','clase','subclase','subnivel actividad'];

        $norm = fn($s) => Str::of($s ?? '')->trim()->lower()->ascii()->toString();

        // leer hasta encontrar una fila que parezca cabecera real
        for ($i=0; $i<30; $i++) {
            $row = fgetcsv($handle, 0, $delimiter);
            if ($row === false) {
                break;
            }
            // ignorar filas vacías o títulos (una celda con texto y el resto vacías)
            $nonEmpty = array_filter(array_map(fn($x) => trim((string)$x) !== '' ? 1 : 0, $row));
            if (count($nonEmpty) <= 1) {
                // probablemente título como "CLASIFICADOR INDUSTRIAL..." o línea en blanco
                continue;
            }

            $header = array_map($norm, $row);

            $idxCodigo = $this->findIndex($header, $aliasesCodigo);
            $idxDesc   = $this->findIndex($header, $aliasesDesc);
            $idxNivel  = $this->findIndex($header, $aliasesNivel);

            // ¿ya parece cabecera?
            if ($idxCodigo !== null && $idxDesc !== null) {
                return [$header, $idxCodigo, $idxDesc, $idxNivel];
            }
        }

        // Si no se halló cabecera clara, asumir primera fila leída como cabecera
        rewind($handle);
        $header = fgetcsv($handle, 0, $delimiter) ?: [];
        $header = array_map($norm, $header);
        return [$header, $this->findIndex($header, $aliasesCodigo), $this->findIndex($header, $aliasesDesc), $this->findIndex($header, $aliasesNivel)];
    }

    private function findIndex(array $header, array $aliases): ?int
    {
        foreach ($aliases as $alias) {
            $idx = array_search($alias, $header, true);
            if ($idx !== false) return $idx;
        }
        return null;
    }

    private function rowHasData(array $row): bool
    {
        foreach ($row as $cell) {
            if (trim((string)$cell) !== '') return true;
        }
        return false;
    }
}
