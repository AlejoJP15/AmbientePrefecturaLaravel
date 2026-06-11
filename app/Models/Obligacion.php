<?php

// app/Models/Obligacion.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Obligacion extends Model
{
    protected $table = 'obligaciones';
    protected $primaryKey = 'id_obligacion';

    // Activa timestamps y mapea created_at a tu columna
    public $timestamps = true;
    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = null; // si tu tabla no tiene updated_at

    protected $fillable = [
        'id_proyecto',
        'id_tipo',
        'id_item',
        'descripcion',
        'tipo_obligacion',
        'periodo',
        'estado',
        'resumen',
        'fecha_envio',     // <- se llenará al enviar
        // 'fecha_registro' // <- NO hace falta en fillable; Eloquent la setea como created_at
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
        'fecha_envio'    => 'datetime',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id_proyecto');
    }
    public function archivos()
    {
        return $this->hasMany(\App\Models\Archivo::class, 'id_obligacion', 'id_obligacion');
    }
    // Códigos calculados
    public function getCodigoDocumentoAttribute() { return 'HGADPCH-DOC-' . $this->id_obligacion; }
    public function getCodigoProyectoAttribute() { return 'HGADPCH-PRO-' . $this->id_proyecto; }
}
