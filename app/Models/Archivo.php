<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    protected $table = 'archivo';
    protected $primaryKey = 'id_archivo';
    public $timestamps = false;

    protected $fillable = [
        'id_proyecto',
        'id_obligacion',  // ← NUEVA columna
        'nombre_archivo',
        'url',
        'tipo',
        'fecha_creacion',
        'fecha_archivo'
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_archivo' => 'datetime',
    ];

    // Relación con Proyecto
    public function proyecto()
    {
        return $this->belongsTo(\App\Models\Proyecto::class, 'id_proyecto', 'id_proyecto');
    }

    // Relación con Obligación
    public function obligacion()
    {
        return $this->belongsTo(\App\Models\Obligacion::class, 'id_obligacion', 'id_obligacion');
    }
}