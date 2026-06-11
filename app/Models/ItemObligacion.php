<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ItemObligacion extends Model {
    protected $table = 'items_obligacion';
    protected $primaryKey = 'id_item';
    public $timestamps = true;
    protected $fillable = ['id_tipo','descripcion','activo'];

    public function tipo() {
        return $this->belongsTo(TipoObligacion::class, 'id_tipo', 'id_tipo');
    }
}