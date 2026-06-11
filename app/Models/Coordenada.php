<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coordenada extends Model
{
    protected $table = 'coordenadas';
    protected $primaryKey = 'id_coordenadas'; // Tu PK tal cual está en la tabla
    public $timestamps = false;

    protected $fillable = [
        'id_proyecto',
        'tipo',
        'coordenada_x',
        'coordenada_y',
        // 'geom' se inserta con DB::raw (PostGIS), no lo metas en fillable
        'coordenada_x1',
        'coordenada_y1',
    ];
}
