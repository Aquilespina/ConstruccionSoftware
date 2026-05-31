<?php

namespace App\Models\Mascota;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Propietario\Propietario;
use App\Models\Receta\Receta;

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

public function propietario(): BelongsTo
    {
        return $this->belongsTo(Propietario::class, 'id_propietario');
    }

        public function recetas()
    {
        return $this->hasMany(Receta::class, 'id_mascota');
    }





    public function getPropietarios()
{
    $propietarios = Propietario::select('id', 'nombre')->get();
    return response()->json($propietarios);
}
}
