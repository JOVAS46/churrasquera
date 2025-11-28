<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuNavegacion extends Model
{
    protected $table = 'menu_navegacion';
    protected $primaryKey = 'id_menu';

    protected $fillable = [
        'nombre',
        'icono',
        'url',
        'orden',
        'activo',
        'id_padre',
        'mostrar_en',
        'id_rol'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    /**
     * Relación con el padre (menú padre)
     */
    public function padre(): BelongsTo
    {
        return $this->belongsTo(MenuNavegacion::class, 'id_padre', 'id_menu');
    }

    /**
     * Relación con los hijos (submenús)
     */
    public function hijos(): HasMany
    {
        return $this->hasMany(MenuNavegacion::class, 'id_padre', 'id_menu')
                    ->where('activo', true)
                    ->orderBy('orden');
    }

    /**
     * Relación con rol
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    /**
     * Scope para obtener solo menús activos
     */
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para obtener menús principales (sin padre)
     */
    public function scopePrincipales($query)
    {
        return $query->whereNull('id_padre');
    }

    /**
     * Scope para filtrar por ubicación (header, sidebar, ambos)
     */
    public function scopeMostrarEn($query, $ubicacion)
    {
        return $query->where(function($q) use ($ubicacion) {
            $q->where('mostrar_en', $ubicacion)
              ->orWhere('mostrar_en', 'ambos');
        });
    }

    /**
     * Scope para filtrar por rol del usuario
     */
    public function scopeParaRol($query, $idRol = null)
    {
        return $query->where(function($q) use ($idRol) {
            $q->whereNull('id_rol');
            if ($idRol) {
                $q->orWhere('id_rol', $idRol);
            }
        });
    }

    /**
     * Obtener menú completo jerárquico
     */
    public static function getMenuJerarquico($ubicacion = 'sidebar', $idRol = null)
    {
        return self::activo()
                   ->principales()
                   ->mostrarEn($ubicacion)
                   ->paraRol($idRol)
                   ->with(['hijos' => function($query) use ($ubicacion, $idRol) {
                       $query->mostrarEn($ubicacion)->paraRol($idRol);
                   }])
                   ->orderBy('orden')
                   ->get();
    }
}
