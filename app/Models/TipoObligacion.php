<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TipoObligacion extends Model {
    protected $table = 'tipos_obligacion';
    protected $primaryKey = 'id_tipo';
    public $timestamps = true;
    protected $fillable = ['nombre','requiere_periodo','activo'];

    public function items() {
        return $this->hasMany(ItemObligacion::class, 'id_tipo', 'id_tipo');
    }
}
