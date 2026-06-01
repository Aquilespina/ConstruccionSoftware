<?php

namespace App\Models\Mascota;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Propietario\Propietario;
use App\Models\Receta\Receta;
use App\Models\Cita\Cita;

class Mascota extends Model
{
    //
    use HasFactory;

    protected $table = 'mascota';
    protected $primaryKey = 'id_mascota';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    public function getEstadoAttribute($value)
    {
        if (is_bool($value)) {
            return $value ? 'activo' : 'inactivo';
        }

        $valor = strtolower(trim((string) $value));

        if (in_array($valor, ['1', 'true', 'activo', 'active'], true)) {
            return 'activo';
        }

        if (in_array($valor, ['0', 'false', 'inactivo', 'inactive'], true)) {
            return 'inactivo';
        }

        return $valor;
    }

    public function setEstadoAttribute($value): void
    {
  $valor = strtolower(trim((string) $value));

    if (in_array($valor, ['1', 'true', 'activo', 'active'], true)) {
        $this->attributes['estado'] = 1;
        return;
    }

    if (in_array($valor, ['0', 'false', 'inactivo', 'inactive'], true)) {
        $this->attributes['estado'] = 0;
        return;
    }

    $this->attributes['estado'] = (int) $value;
    }

public function propietario(): BelongsTo
    {
        return $this->belongsTo(Propietario::class, 'id_propietario');
    }

    public function getEdadAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }

        return $this->attributes['años'] ?? null;
    }

    public function setEdadAttribute($value): void
    {
        if (array_key_exists('edad', $this->attributes)) {
            $this->attributes['edad'] = $value;
        }

        if (array_key_exists('años', $this->attributes)) {
            $this->attributes['años'] = $value;
        }

        if (!array_key_exists('edad', $this->attributes) && !array_key_exists('años', $this->attributes)) {
            $this->attributes['edad'] = $value;
        }
    }

    public function getAniosAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }

        return $this->attributes['edad'] ?? null;
    }

    public function setAniosAttribute($value): void
    {
        if (array_key_exists('años', $this->attributes)) {
            $this->attributes['años'] = $value;
        }

        if (array_key_exists('edad', $this->attributes)) {
            $this->attributes['edad'] = $value;
        }

        if (!array_key_exists('años', $this->attributes) && !array_key_exists('edad', $this->attributes)) {
            $this->attributes['años'] = $value;
        }
    }

        public function recetas()
    {
        return $this->hasMany(Receta::class, 'id_mascota');
    }





        /**
     * Getter estático para obtener todas las mascotas con propietarios
     */
    public static function getMascotasConPropietarios()
    {
        return self::with('propietario')
            ->select('id_mascota', 'nombre', 'especie', 'raza', 'id_propietario')
            ->orderBy('nombre')
            ->get()
            ->map(function ($mascota) {
                return [
                    'id' => $mascota->id_mascota,
                    'nombre' => $mascota->nombre,
                    'especie' => $mascota->especie,
                    'raza' => $mascota->raza ?? '',
                    'propietario_nombre' => $mascota->propietario->nombre ?? 'Sin propietario',
                    'display_name' => $mascota->nombre . ' - ' . ($mascota->propietario->nombre ?? 'Sin propietario')
                ];
            });
    }

    public function getPropietarios()
    {
        $propietarios = Propietario::select('id', 'nombre')->get();
        return response()->json($propietarios);
    }

   public function citas()
{
    return $this->hasMany(
        Cita::class,
        'id_mascota',
        'id_mascota'
    );
}
}
