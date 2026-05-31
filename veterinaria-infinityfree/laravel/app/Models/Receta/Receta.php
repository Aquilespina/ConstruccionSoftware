<?php

namespace App\Models\Receta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Mascota\Mascota;
use App\Models\Cita\Cita;

class Receta extends Model
{
    use HasFactory;

    protected $table = 'receta';
    protected $primaryKey = 'id_receta';
    public $timestamps = false;

    protected $fillable = [
        'id_mascota',
        'id_cita',
        'medicamento',
        'tipo_medicamento',
        'dosis',
        'indicaciones',
        'proxima_cita',
        'fecha',
    ];

    protected $casts = [
        'proxima_cita' => 'date',
        'fecha' => 'datetime',
    ];

    /**
     * Relación con Mascota
     */
    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'id_mascota', 'id_mascota');
    }

    /**
     * Relación con Cita
     */
    public function cita()
    {
        return $this->belongsTo(Cita::class, 'id_cita', 'id_cita');
    }
}