<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $table = 'proyecto';
    protected $primaryKey = 'id_proyecto';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'resumen',
        'fecha_creacion',
        'id_usuario',
        'id_actividad',
        'direccion',
        'tipo_permiso',
        'tipo_estudio',
        'id_provincia',
        'id_canton',
        'id_parroquia',
        'codigo_mae',
        'cod_ambiental',
        'ficha_ambiental',
        'codigo_suia',
        'tipo_proyecto',
        'id_estado_proyecto',
        'estado_revision',
        'descripcion_revision',
        'motivos_devolucion',
        'descripcion_devolucion',
        'fecha_obtencion_permiso',
        'tdr_cumplimiento',
        'auditoria_ambiental_cumplimiento',
        'informe_ambiental_cumplimiento',
        'informe_monitoreo_ambiental',
        'plan_accion',
        'informe_plan_accion',
        'plan_emergente',
        'informe_plan_emergente',
        'plan_cierre',
        'informe_plan_cierre',
        'informe_gestion',
        'periodo_aprobacion_desde',
        'periodo_aprobacion_hasta',
        'sector',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_obtencion_permiso' => 'date',
        'periodo_aprobacion_desde' => 'date',
        'periodo_aprobacion_hasta' => 'date',
        // Quita los casts erróneos, o:
        'tipo_permiso' => 'string',
        'tipo_estudio' => 'string',
        // ids quedan como integer
        'id_usuario' => 'integer',
        'id_actividad' => 'integer',
        'id_provincia' => 'integer',
        'id_canton' => 'integer',
        'id_parroquia' => 'integer',
        'id_estado_proyecto' => 'integer',
    ];

    /** Relaciones */
    // Relación con archivos
    public function archivos()
    {
        return $this->hasMany(\App\Models\Archivo::class, 'id_proyecto', 'id_proyecto');
    }
    public function actividad()
    {
        return $this->belongsTo(Actividad::class, 'id_actividad', 'id_actividad');
    }
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_usuario', 'id_persona');
    }
    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'id_provincia', 'id_provincia');
    }
    public function canton()
    {
        return $this->belongsTo(Canton::class, 'id_canton', 'id_canton');
    }
    public function parroquia()
    {
        return $this->belongsTo(Parroquia::class, 'id_parroquia', 'id_parroquia');
    }
    public function estadoProyecto()
    {
        return $this->belongsTo(EstadoProyecto::class, 'id_estado_proyecto', 'id_estado_proyecto');
    }
    public function obligaciones()
    {
        return $this->hasMany(\App\Models\Obligacion::class, 'id_proyecto', 'id_proyecto');
    }
    public function comentarios()
    {
        return $this->hasMany(\App\Models\Comentario::class, 'id_proyecto', 'id_proyecto');
    }


    /** Scopes útiles */
    public function scopeNombreLike($q, string $term)
    {
        return $q->where('nombre', 'ILIKE', "%{$term}%");
    }
    public function scopeDeActividadCodigo($q, string $cod)
    {
        return $q->whereHas('actividad', fn($qq) => $qq->where('codigo_actividad', $cod));
    }
}
