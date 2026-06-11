<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantillaFormato extends Model
{
    use HasFactory;

    protected $table = 'plantilla_formato';
    protected $primaryKey = 'id_pformato';
    public $timestamps = true;

    protected $fillable = [
        'descripcion',
        'url_plantilla',
        'asunto',
        'para',
        'cuerpo',
        'antecedentes',
        'analisis',
        'objetivos',
        'observaciones',
        'detalle',
        'conclusiones',
        'tipo_documento'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}