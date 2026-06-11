<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstadoPersona extends Model
{
    use HasFactory;

    protected $table = 'estado_persona';
    protected $primaryKey = 'id_estado_persona';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
    ];
}
