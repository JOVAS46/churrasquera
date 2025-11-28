<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    use HasFactory;

    protected $table = 'mesa';
    protected $primaryKey = 'id_mesa';
    public $timestamps = false;

    protected $fillable = [
        'numero_mesa',
        'capacidad',
        'ubicacion',
        'estado',
        'id_mesero'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relaciones
    public function mesero()
    {
        return $this->belongsTo(User::class, 'id_mesero', 'id_usuario');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_mesa', 'id_mesa');
    }

    public function pedidoActual()
    {
        return $this->hasOne(Pedido::class, 'id_mesa', 'id_mesa')
                    ->whereIn('estado', ['pendiente', 'en_preparacion', 'listo']);
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_mesa', 'id_mesa');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_mesa', 'id_mesa');
    }

    // Scopes
    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'disponible');
    }

    public function scopeOcupadas($query)
    {
        return $query->where('estado', 'ocupada');
    }

    public function scopeReservadas($query)
    {
        return $query->where('estado', 'reservada');
    }

    public function scopeMantenimiento($query)
    {
        return $query->where('estado', 'mantenimiento');
    }

    // Estados disponibles
    const DISPONIBLE = 'disponible';
    const OCUPADA = 'ocupada';
    const RESERVADA = 'reservada';
    const MANTENIMIENTO = 'mantenimiento';

    // Métodos de utilidad
    public function isDisponible()
    {
        return $this->estado === self::DISPONIBLE;
    }

    public function isOcupada()
    {
        return $this->estado === self::OCUPADA;
    }

    public function marcarComoOcupada()
    {
        $this->update(['estado' => self::OCUPADA]);
    }

    public function marcarComoDisponible()
    {
        $this->update(['estado' => self::DISPONIBLE]);
    }

    public function liberarSiNoTienePedidosActivos()
    {
        // Verificar si tiene pedidos activos (pendiente, en_preparacion, listo)
        $pedidosActivos = $this->pedidos()
                               ->whereIn('estado', ['pendiente', 'en_preparacion', 'listo'])
                               ->count();

        // Si no tiene pedidos activos, liberar la mesa
        if ($pedidosActivos === 0 && $this->estado === self::OCUPADA) {
            $this->marcarComoDisponible();
            return true;
        }

        return false;
    }
}