<?php

namespace App\Models\Propietario;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Propietario extends Model
{
    //
 use Hasfactory;    

protected $table = 'propietario';
protected $primaryKey= 'id_propietario';
protected $keyType = 'string';
public $incrementing = false;
public $timestamps = false;

protected $guarded = [];


public static function boot()
{
    parent::boot();

    static::creating(function ($propietario){
        $propietario->id_propietario= sha1(microtime());
    });
}

}
