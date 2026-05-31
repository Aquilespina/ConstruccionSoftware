<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profesional extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'profesional';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_profesional';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'especialidad',
        'numero_licencia',
        'horario_entrada',
        'horario_salida',
        'estado'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'horario_entrada' => 'datetime:H:i',
        'horario_salida' => 'datetime:H:i',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the professional's full name.
     *
     * @return string
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellido}";
    }

    /**
     * Get the professional's title and full name.
     *
     * @return string
     */
    public function getTituloNombreCompletoAttribute()
    {
        // Asumimos que todos son doctores veterinarios
        return "Dr. {$this->nombre} {$this->apellido}";
    }

    /**
     * Scope a query to only include active professionals.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivo($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope a query to only include professionals of a specific specialty.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $especialidad
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEspecialidad($query, $especialidad)
    {
        return $query->where('especialidad', $especialidad);
    }

    /**
     * Check if the professional is available at a given time.
     *
     * @param  string  $time
     * @return bool
     */
    public function isAvailableAt($time)
    {
        if (!$this->horario_entrada || !$this->horario_salida) {
            return false;
        }

        $time = \Carbon\Carbon::parse($time);
        $entrada = \Carbon\Carbon::parse($this->horario_entrada);
        $salida = \Carbon\Carbon::parse($this->horario_salida);

        return $time->between($entrada, $salida);
    }

    /**
     * Get the professional's schedule as a formatted string.
     *
     * @return string
     */
    public function getHorarioFormateadoAttribute()
    {
        if (!$this->horario_entrada || !$this->horario_salida) {
            return 'No definido';
        }

        $entrada = \Carbon\Carbon::parse($this->horario_entrada)->format('H:i');
        $salida = \Carbon\Carbon::parse($this->horario_salida)->format('H:i');

        return "{$entrada} - {$salida}";
    }
}