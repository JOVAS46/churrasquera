<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'producto';
    protected $primaryKey = 'id_producto';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'tiempo_preparacion',
        'disponible',
        'imagen',
        'id_categoria',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'disponible' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Relación con categoría
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    /**
     * Relación muchos a muchos con insumos
     */
    public function insumos()
    {
        return $this->belongsToMany(
            Insumo::class,
            'producto_insumo',
            'id_producto',
            'id_insumo',
            'id_producto',
            'id_insumo'
        )->withPivot('cantidad_necesaria');
    }

    /**
     * Relación con detalles de pedido
     */
    public function detallesPedido()
    {
        return $this->hasMany(DetallePedido::class, 'id_producto', 'id_producto');
    }
}
