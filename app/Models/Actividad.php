<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividad';
    protected $primaryKey = 'id_actividad';
    public $timestamps = false;

    protected $fillable = [
        'codigo_actividad',
        'descripcion_actividad',
        'nivel',
    ];

    /** Orden exacto de tus niveles */
    public const NIVEL_ORDER = [
        'PRINCIPAL',
        'SUBCATEGORIA',
        'ACTIVIDAD',
    ];

    public static function nivelRanks(): array
    {
        $r = [];
        foreach (self::NIVEL_ORDER as $i => $name) $r[$name] = $i + 1;
        return $r;
    }

    public static function rankOf(?string $nivel): ?int
    {
        if (!$nivel) return null;
        $upper = mb_strtoupper($nivel, 'UTF-8');
        return self::nivelRanks()[$upper] ?? null;
    }

    public static function nextLevel(?string $nivel): ?string
    {
        $upper = $nivel ? mb_strtoupper($nivel, 'UTF-8') : null;
        $order = self::NIVEL_ORDER;
        if ($upper === null) return $order[0] ?? null;
        for ($i = 0; $i < count($order); $i++) {
            if ($order[$i] === $upper) return $order[$i + 1] ?? null;
        }
        return null;
    }

    /** Relaciones */
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'id_actividad', 'id_actividad');
    }
}
