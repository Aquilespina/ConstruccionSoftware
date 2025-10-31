<?php

namespace App\Models\Propietario;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Mascota\Mascota;

class Propietario extends Model
{
    //
 use Hasfactory;    

protected $table = 'propietario';
protected $primaryKey= 'id_propietario';
protected $keyType = 'string';
public $incrementing = false;
public $timestamps = false;

protected $fillable = [
    'nombre',
    'telefono',
    'direccion',
    'correo_electronico',
    'fecha_registro',
];

    public function mascotas(): HasMany
    {
        return $this->hasMany(Mascota::class, 'id_propietario');
    }


}
