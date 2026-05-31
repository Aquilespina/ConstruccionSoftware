<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profesional extends Model
{
    use HasFactory;

    // Tabla y clave primaria según el esquema proporcionado
    protected $table = 'profesional';
    protected $primaryKey = 'rfc';
    protected $keyType = 'string';
    public $incrementing = false;

    // La tabla no tiene timestamps (created_at/updated_at)
    public $timestamps = false;

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'rfc',
        'nombre',
        'correo',
        'especialidad',
        'turno',
        'activo',
    ];

    // Casts útiles
    protected $casts = [
        'activo' => 'boolean',
    ];

    // Scopes de ayuda
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function scopeEspecialidad($query, string $especialidad)
    {
        return $query->where('especialidad', $especialidad);
    }
}
