<?php

namespace App\Models\Cita;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Mascota\Mascota;
use App\Models\Profesional;

class Cita extends Model
{
    // Tabla y clave primaria según el esquema
    protected $table = 'cita';
    protected $primaryKey = 'id_cita';
    public $timestamps = false;

    protected $fillable = [
        'id_mascota',
        'rfc_profesional',
        'tipo_servicio',
        'tipo_cita',
        'tarifa',
        'peso_mascota',
        'fecha',
        'horario',
        'diagnostico',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
        // mantener horario como string (TIME) y montos como decimal/string
        'tarifa' => 'decimal:2',
        'peso_mascota' => 'decimal:2',
    ];

    // Relaciones
    public function mascota(): BelongsTo
    {
        return $this->belongsTo(Mascota::class, 'id_mascota', 'id_mascota');
    }

    public function profesional(): BelongsTo
    {
        return $this->belongsTo(Profesional::class, 'rfc_profesional', 'rfc');
    }

    // Accesores prácticos
    public function getNombreMascotaAttribute(): ?string
    {
        return $this->mascota->nombre ?? null;
    }

    public function getNombreMedicoAttribute(): ?string
    {
        return $this->profesional->nombre ?? null;
    }
}
