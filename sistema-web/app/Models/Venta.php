<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';
    
    protected $fillable = [
        'cantidad', 
        'precio', 
        'total', 
        'receta_id', 
        'id_usuario',
        'id_mesa',
        'id_metodo_pago',
        'fecha_venta',
        'monto_recibido',
        'vuelto'
    ];

    protected $casts = [
        'fecha_venta' => 'datetime',
        'total' => 'decimal:2',
        'precio' => 'decimal:2',
        'monto_recibido' => 'decimal:2',
        'vuelto' => 'decimal:2'
    ];

    // Relaciones existentes
    public function receta()
    {
        return $this->belongsTo(Receta::class);
    }
    
    public function movimientos_inventario()
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    // Nuevas relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function mesa()
    {
        return $this->belongsTo(Mesa::class, 'id_mesa', 'id_mesa');
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'id_metodo_pago', 'id_metodo_pago');
    }

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class, 'id_venta', 'id');
    }

    // Scopes
    public function scopeHoy($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeDelCajero($query, $cajeroId)
    {
        return $query->where('id_usuario', $cajeroId);
    }
}
