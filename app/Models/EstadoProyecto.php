<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoProyecto extends Model
{
    protected $table = 'estado_proyecto';
    protected $primaryKey = 'id_estado_proyecto';
    public $timestamps = false;

    protected $fillable = ['descripcion'];

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'id_estado_proyecto', 'id_estado_proyecto');
    }
}
