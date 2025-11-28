<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categoria';
    protected $primaryKey = 'id_categoria';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Relación con productos
     */
    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_categoria', 'id_categoria');
    }

    /**
     * Relación con insumos (si aplica)
     */
    public function insumos()
    {
        return $this->hasMany(Insumo::class, 'id_categoria', 'id_categoria');
    }
}
