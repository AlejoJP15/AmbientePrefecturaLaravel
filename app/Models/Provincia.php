<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Provincia extends Model
{
    use HasFactory;

    protected $table = 'provincia';
    protected $primaryKey = 'id_provincia';
    public $timestamps = false;

    protected $fillable = [
        'nombre_provincia',
    ];

    public function canton()
    {
        return $this->hasMany(Canton::class, 'id_provincia');
    }
    public function getRouteKeyName(): string
    {
        return 'id_provincia';
    }
}
