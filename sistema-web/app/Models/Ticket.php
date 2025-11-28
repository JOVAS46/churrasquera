<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Ticket extends Model
{
    protected $table = 'tickets';
    protected $primaryKey = 'id_ticket';

    protected $fillable = [
        'codigo_ticket',
        'titulo',
        'descripcion',
        'categoria',
        'prioridad',
        'estado',
        'usuario_creador_id',
        'usuario_asignado_id',
        'mesa_id',
        'pedido_id',
        'reserva_id',
        'fecha_limite',
        'fecha_resolucion',
        'fecha_cerrado',
        'etiquetas',
        'resolucion',
        'canal_origen',
        'es_interno',
        'tiempo_respuesta_minutos',
        'costo_resolucion'
    ];

    protected $casts = [
        'etiquetas' => 'array',
        'fecha_limite' => 'datetime',
        'fecha_resolucion' => 'datetime',
        'fecha_cerrado' => 'datetime',
        'es_interno' => 'boolean',
        'costo_resolucion' => 'decimal:2'
    ];

    // Relaciones
    public function usuarioCreador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_creador_id', 'id_usuario');
    }

    public function usuarioAsignado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_asignado_id', 'id_usuario');
    }

    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class, 'mesa_id', 'id_mesa');
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id', 'id_pedido');
    }

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'reserva_id', 'id_reserva');
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(TicketComentario::class, 'ticket_id', 'id_ticket')
                    ->orderBy('created_at', 'desc');
    }

    // Métodos de utilidad
    public function generarCodigoTicket(): string
    {
        $year = date('Y');
        $lastTicket = self::whereYear('created_at', $year)
            ->orderBy('id_ticket', 'desc')
            ->first();
        
        $numero = $lastTicket ? 
            intval(substr($lastTicket->codigo_ticket, -3)) + 1 : 1;
        
        return 'TKT-' . $year . '-' . str_pad($numero, 3, '0', STR_PAD_LEFT);
    }

    public function calcularTiempoVida(): string
    {
        $inicio = $this->created_at;
        $fin = $this->fecha_cerrado ?? now();
        
        $diferencia = $inicio->diffInHours($fin);
        
        if ($diferencia < 1) {
            return $inicio->diffInMinutes($fin) . ' minutos';
        } elseif ($diferencia < 24) {
            return $diferencia . ' horas';
        } else {
            return $inicio->diffInDays($fin) . ' días';
        }
    }

    public function estaVencido(): bool
    {
        return $this->fecha_limite && 
               $this->fecha_limite->isPast() && 
               !in_array($this->estado, ['resuelto', 'cerrado']);
    }

    public function tiempoRestante(): ?string
    {
        if (!$this->fecha_limite || in_array($this->estado, ['resuelto', 'cerrado'])) {
            return null;
        }

        if ($this->fecha_limite->isPast()) {
            return 'Vencido hace ' . $this->fecha_limite->diffForHumans();
        }

        return 'Vence ' . $this->fecha_limite->diffForHumans();
    }

    public function getColorPrioridad(): string
    {
        return match($this->prioridad) {
            'critica' => 'danger',
            'alta' => 'warning',
            'media' => 'info',
            'baja' => 'secondary',
            default => 'secondary'
        };
    }

    public function getColorEstado(): string
    {
        return match($this->estado) {
            'abierto' => 'primary',
            'en_proceso' => 'warning',
            'esperando_respuesta' => 'info',
            'resuelto' => 'success',
            'cerrado' => 'secondary',
            'escalado' => 'danger',
            default => 'secondary'
        };
    }

    public function getTextoCategoria(): string
    {
        return match($this->categoria) {
            'soporte_tecnico' => 'Soporte Técnico',
            'queja_cliente' => 'Queja de Cliente',
            'sugerencia' => 'Sugerencia',
            'problema_servicio' => 'Problema de Servicio',
            'incidente_operativo' => 'Incidente Operativo',
            'solicitud_mantenimiento' => 'Solicitud de Mantenimiento',
            'consulta_general' => 'Consulta General',
            default => ucfirst(str_replace('_', ' ', $this->categoria))
        };
    }

    public function marcarComoResuelto(string $resolucion, ?int $usuarioId = null): void
    {
        $this->update([
            'estado' => 'resuelto',
            'resolucion' => $resolucion,
            'fecha_resolucion' => now(),
            'usuario_asignado_id' => $usuarioId ?? $this->usuario_asignado_id
        ]);

        // Crear comentario de resolución
        $this->comentarios()->create([
            'usuario_id' => $usuarioId ?? Auth::id(),
            'comentario' => $resolucion,
            'tipo' => 'cambio_estado',
            'es_resolucion' => true,
            'estado_anterior' => $this->getOriginal('estado'),
            'estado_nuevo' => 'resuelto'
        ]);
    }

    public function cerrarTicket(?int $usuarioId = null): void
    {
        $this->update([
            'estado' => 'cerrado',
            'fecha_cerrado' => now()
        ]);

        $this->comentarios()->create([
            'usuario_id' => $usuarioId ?? Auth::id(),
            'comentario' => 'Ticket cerrado',
            'tipo' => 'cambio_estado',
            'estado_anterior' => $this->getOriginal('estado'),
            'estado_nuevo' => 'cerrado'
        ]);
    }

    // Scopes
    public function scopePorEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopePorPrioridad($query, string $prioridad)
    {
        return $query->where('prioridad', $prioridad);
    }

    public function scopePorCategoria($query, string $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopeAsignadosA($query, int $usuarioId)
    {
        return $query->where('usuario_asignado_id', $usuarioId);
    }

    public function scopeCreadosPor($query, int $usuarioId)
    {
        return $query->where('usuario_creador_id', $usuarioId);
    }

    public function scopeVencidos($query)
    {
        return $query->where('fecha_limite', '<', now())
                    ->whereNotIn('estado', ['resuelto', 'cerrado']);
    }

    public function scopeAbiertos($query)
    {
        return $query->whereIn('estado', ['abierto', 'en_proceso', 'esperando_respuesta', 'escalado']);
    }
}
