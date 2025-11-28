<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reserva';
    protected $primaryKey = 'id_reserva';
    public $timestamps = false;

    protected $fillable = [
        'fecha_reserva',
        'hora_inicio',
        'hora_fin',
        'numero_personas',
        'estado',
        'observaciones',
        'id_cliente',
        'id_mesa'
    ];

    protected $casts = [
        'fecha_reserva' => 'date',
        'hora_inicio' => 'datetime:H:i:s',
        'hora_fin' => 'datetime:H:i:s',
        'created_at' => 'datetime',
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(User::class, 'id_cliente', 'id_usuario');
    }

    public function mesa()
    {
        return $this->belongsTo(Mesa::class, 'id_mesa', 'id_mesa');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeConfirmadas($query)
    {
        return $query->where('estado', 'confirmada');
    }

    public function scopeCanceladas($query)
    {
        return $query->where('estado', 'cancelada');
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_reserva', Carbon::today());
    }

    public function scopeProximas($query)
    {
        return $query->where('fecha_reserva', '>=', now());
    }

    // Estados disponibles
    const PENDIENTE = 'pendiente';
    const CONFIRMADA = 'confirmada';
    const CANCELADA = 'cancelada';
    const COMPLETADA = 'completada';
}