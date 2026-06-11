<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    protected $table = 'comentarios';
    protected $primaryKey = 'id_comentario';
    public $timestamps = false; // la tabla ya tiene fecha_comentario con default

    protected $fillable = [
        'id_proyecto',
        'id_persona',
        'descripcion',
        // 'fecha_comentario' // se llena en BD por DEFAULT CURRENT_TIMESTAMP
    ];

 
    public function proyecto()
    {
        return $this->belongsTo(\App\Models\Proyecto::class, 'id_proyecto', 'id_proyecto');
    }

    public function persona()
    {
        return $this->belongsTo(\App\Models\Persona::class, 'id_persona', 'id_persona');
    }
}