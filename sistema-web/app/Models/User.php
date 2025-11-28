<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Usar la tabla 'usuario' de PostgreSQL
    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';

    // Deshabilitar timestamps automáticos de Laravel (created_at, updated_at)
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'password',
        'estado',
        'id_rol',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'fecha_registro' => 'datetime',
        'password' => 'hashed',
        'estado' => 'boolean',
    ];

    /**
     * Relación con el rol
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    /**
     * Relación con reservas (como cliente)
     */
    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_cliente', 'id_usuario');
    }

    /**
     * Relación con pedidos (como cliente)
     */
    public function pedidosComoCliente()
    {
        return $this->hasMany(Pedido::class, 'id_cliente', 'id_usuario');
    }

    /**
     * Relación con pedidos (como mesero)
     */
    public function pedidosComoMesero()
    {
        return $this->hasMany(Pedido::class, 'id_mesero', 'id_usuario');
    }

    /**
     * Relación con pedidos (como cocinero)
     */
    public function pedidosComoCocinero()
    {
        return $this->hasMany(Pedido::class, 'id_cocinero', 'id_usuario');
    }

    /**
     * Relación con ventas
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relación con mesas asignadas (como mesero)
     */
    public function mesasAsignadas()
    {
        return $this->hasMany(Mesa::class, 'id_mesero', 'id_usuario');
    }

    /**
     * Relación con movimientos de insumo
     */
    public function movimientosInsumo()
    {
        return $this->hasMany(MovimientoInsumo::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relación con cajas
     */
    public function cajas()
    {
        return $this->hasMany(Caja::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relación con bitácora
     */
    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole($roleName): bool
    {
        return optional($this->rol)->nombre_rol === $roleName;
    }

    /**
     * Verificar si el usuario es gerente
     */
    public function isGerente(): bool
    {
        return $this->id_rol === Rol::GERENTE;
    }

    /**
     * Verificar si el usuario es cajero
     */
    public function isCajero(): bool
    {
        return $this->id_rol === Rol::CAJERO;
    }

    /**
     * Verificar si el usuario es mesero
     */
    public function isMesero(): bool
    {
        return $this->id_rol === Rol::MESERO;
    }

    /**
     * Verificar si el usuario es cocinero
     */
    public function isCocinero(): bool
    {
        return $this->id_rol === Rol::COCINERO;
    }

    /**
     * Accessor para obtener el nombre completo
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    /**
     * Accessor para compatibilidad con código antiguo
     */
    public function getNameAttribute(): string
    {
        return $this->nombre_completo;
    }
}
