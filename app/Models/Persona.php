<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;                
use Illuminate\Support\Facades\Hash;                    
class Persona extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $table = 'persona';
    protected $primaryKey = 'id_persona';
    public $timestamps = false;
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $dates = ['deleted_at','fecha_creacion'];

    protected $fillable = [
        'usuario',
        'nombres',
        'apellidos',
        'contraseña',
        'email',
        'fecha_creacion',
        'id_perfil',
        'id_estado_persona',
        'token',
        'tratamiento',
        'genero',
        'telefono',
        'celular',
        'direccion',
        'id_provincia',
        'id_canton',
        'id_parroquia',
        'firma_electronica',
        'correo_alternativo',
        'nivel_instruccion',
        'consentimiento',
        'tipo_persona',
        'tipo_documento'
    ];

    protected $hidden = [
        'contraseña',
        'token'
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
    ];

    // Relaciones
    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'id_perfil');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoPersona::class, 'id_estado_persona');
    }

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'id_provincia');
    }

    public function canton()
    {
        return $this->belongsTo(Canton::class, 'id_canton');
    }

    public function parroquia()
    {
        return $this->belongsTo(Parroquia::class, 'id_parroquia');
    }

    // Mutator para la contraseña
    public function setContraseñaAttribute($value)
    {
        if ($value === null || $value === '') {
            $this->attributes['contraseña'] = $value;
            return;
        }
        $this->attributes['contraseña'] = Hash::needsRehash($value)
            ? Hash::make($value)
            : $value;
    }
    public function getAuthPassword()
    {
        return $this->contraseña;
    }
    public function usuarioExterno()
    {
        return $this->hasOne(\App\Models\UsuarioExterno::class, 'id_persona', 'id_persona');
    }
    public function proyectos()
    {
        
        return $this->hasMany(\App\Models\Proyecto::class, 'id_usuario', 'id_persona');
    }
    public function getNombreListadoAttribute(): string
    {
        if ($this->usuarioExterno) {
            return $this->usuarioExterno->nombre_representante
                ?: ($this->usuarioExterno->organizacion ?? '—');
        }
        return trim(($this->nombres ?? '').' '.($this->apellidos ?? '')) ?: '—';
    }
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
    
}
