<?php

namespace App\Models\Honorario;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Hospitalizaciones\Hospitalizaciones;
use App\Models\Mascota\Mascota;

class Honorario extends Model
{
    //

    protected $table = 'honorario';

    protected $primaryKey = 'id_honorario';
    public $timestamps = true;

    protected $fillable = [
        'id_honorario',
        'id_mascota',
        'id_hospitalizacion',
        'fecha_ingreso',
        'fecha_corte',
        'subtotal',
        'total_pagado',
        'saldo_pendiente',
        'estado'
    ];

    public function hospitalizacion()
    {
        return $this->belongsTo(Hospitalizaciones::class, 'id_hospitalizacion');
    }

    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'id_mascota');
    }

}
