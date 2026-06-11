<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Parroquia extends Model
{
    use HasFactory;

    protected $table = 'parroquia';
    protected $primaryKey = 'id_parroquia';
    public $timestamps = false;

    protected $fillable = [
        'nombre_parroquia',
        'id_canton',
    ];

    public function canton()
    {
        return $this->belongsTo(Canton::class, 'id_canton');
    }
    public function getRouteKeyName(): string
    {
        return 'id_parroquia';
    }
}
