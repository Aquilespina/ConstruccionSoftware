<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'correo_electronico',
        'nombre_usuario',
        'password',
        'tipo_permiso',
        'estado',
    ];

    /**
     * The table associated with the model.
     */
    protected $table = 'usuario';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id_usuario';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

        // Accessor para estado como string
    public function getEstadoAttribute($value)
    {
        return $value == 1 ? 'activo' : 'inactivo';
    }

    // Mutator para estado como integer
    public function setEstadoAttribute($value)
    {
        $this->attributes['estado'] = $value === 'activo' ? 1 : 0;
    }
    
}
