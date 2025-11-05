<?php

namespace App\Models\detalle_honorario;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Honorario\Honorario;

class detalle_honorario extends Model
{
    //

    protected $table = 'detalle_honorario';

    protected $primaryKey = 'id_detalle';
    public $timestamps = true;

    protected $fillable = [
        'id_detalle',
        'id_honorario',
        'concepto',
        'cantidad',
        'precio_unitario',
        'importe',
        'fecha_pago',
        'monto_pagado',
        'tipo_pago'
    ];

    public function honorario()
    {
        return $this->belongsTo(Honorario::class, 'id_honorario');
    }
}
