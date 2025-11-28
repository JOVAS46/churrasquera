<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedido';
    protected $primaryKey = 'id_pedido';
    public $timestamps = false;

    protected $fillable = [
        'fecha_pedido',
        'estado',
        'total',
        'observaciones',
        'id_cliente',
        'id_mesero',
        'id_cocinero',
        'id_mesa'
    ];

    protected $casts = [
        'fecha_pedido' => 'datetime',
        'total' => 'decimal:2',
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(User::class, 'id_cliente', 'id_usuario');
    }

    public function mesero()
    {
        return $this->belongsTo(User::class, 'id_mesero', 'id_usuario');
    }

    public function mesa()
    {
        return $this->belongsTo(Mesa::class, 'id_mesa', 'id_mesa');
    }

    public function cocinero()
    {
        return $this->belongsTo(User::class, 'id_cocinero', 'id_usuario');
    }

    public function detalles()
    {
        return $this->hasMany(PedidoDetalle::class, 'id_pedido', 'id_pedido');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeEnPreparacion($query)
    {
        return $query->where('estado', 'en_preparacion');
    }

    public function scopeListos($query)
    {
        return $query->where('estado', 'listo');
    }

    public function scopeEntregados($query)
    {
        return $query->where('estado', 'entregado');
    }

    public function scopeCancelados($query)
    {
        return $query->where('estado', 'cancelado');
    }

    // Métodos de utilidad
    public function isPendiente()
    {
        return $this->estado === 'pendiente';
    }

    public function isEnPreparacion()
    {
        return $this->estado === 'en_preparacion';
    }

    public function isListo()
    {
        return $this->estado === 'listo';
    }

    public function isEntregado()
    {
        return $this->estado === 'entregado';
    }

    public function isCancelado()
    {
        return $this->estado === 'cancelado';
    }

    public function isActivo()
    {
        return in_array($this->estado, ['pendiente', 'en_preparacion', 'listo']);
    }

    public function isTerminado()
    {
        return in_array($this->estado, ['entregado', 'cancelado']);
    }
}