<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Canton extends Model
{
    use HasFactory;

    protected $table = 'canton';
    protected $primaryKey = 'id_canton';
    public $timestamps = false;

    protected $fillable = [
        'nombre_canton',
        'id_provincia',
    ];

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'id_provincia');
    }

    public function parroquia()
    {
        return $this->hasMany(Parroquia::class, 'id_canton');
    }
    public function getRouteKeyName(): string
    {
        return 'id_canton';
    }
}
