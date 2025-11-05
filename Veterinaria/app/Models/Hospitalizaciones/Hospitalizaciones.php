<?php

namespace App\Models\Hospitalizaciones;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Mascota\Mascota;
use App\Models\Honorario\Honorario;

class Hospitalizaciones extends Model
{
    //

    protected $table = 'hospitalizaciones';

    protected $primaryKey = 'id_hospitalizacion';
    public $timestamps = true;

    protected $fillable = [
        'id_mascota',
        'id_hospitalizacion',
        'fecha_ingreso',
        'fecha_egreso',
        'estado',
        'observaciones',
        
    ];


    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'id_mascota');
    }

    public function honorarios()
    {
        return $this->hasMany(Honorario::class, 'id_hospitalizacion');
    }
}
