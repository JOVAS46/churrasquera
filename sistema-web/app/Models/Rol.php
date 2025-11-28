<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rol extends Model
{
    protected $table = 'rol';
    protected $primaryKey = 'id_rol';

    protected $fillable = [
        'nombre_rol',
        'descripcion'
    ];

    const UPDATED_AT = null; // Esta tabla solo tiene created_at

    /**
     * Relación con usuarios
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'id_rol', 'id_rol');
    }

    /**
     * Relación con menús
     */
    public function menus(): HasMany
    {
        return $this->hasMany(MenuNavegacion::class, 'id_rol', 'id_rol');
    }

    /**
     * Roles de negocio para la churrascuteria
     */
    const GERENTE = 1;
    const CAJERO = 2;
    const MESERO = 3;
    const COCINERO = 4;
    const CLIENTE = 5;
}
