<?php

namespace App\Models\Propietario;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Mascota\Mascota;

class Propietario extends Model
{
    use HasFactory;

    protected $table = 'propietario';
    protected $primaryKey = 'id_propietario';
    // id_propietario es autoincrement BIGINT según la migración
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'telefono',
        'direccion',
        'correo_electronico',
        'fecha_registro',
    ];

    public function mascotas(): HasMany
    {
        return $this->hasMany(Mascota::class, 'id_propietario', 'id_propietario');
    }

    // Accesor para nombre completo; por ahora devuelve 'nombre'
    public function getNombreCompletoAttribute(): string
    {
        return (string) ($this->nombre ?? '');
    }
}
