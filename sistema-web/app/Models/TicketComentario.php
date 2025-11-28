<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TicketComentario extends Model
{
    protected $table = 'ticket_comentarios';
    protected $primaryKey = 'id_comentario';

    protected $fillable = [
        'ticket_id',
        'usuario_id',
        'comentario',
        'tipo',
        'es_interno',
        'es_resolucion',
        'adjuntos',
        'estado_anterior',
        'estado_nuevo',
        'fecha_lectura',
        'tiempo_respuesta_minutos'
    ];

    protected $casts = [
        'adjuntos' => 'array',
        'es_interno' => 'boolean',
        'es_resolucion' => 'boolean',
        'fecha_lectura' => 'datetime'
    ];

    // Relaciones
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id_ticket');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id_usuario');
    }

    // Métodos de utilidad
    public function marcarComoLeido(): void
    {
        if (!$this->fecha_lectura) {
            $this->update(['fecha_lectura' => now()]);
        }
    }

    public function esCambioEstado(): bool
    {
        return $this->tipo === 'cambio_estado';
    }

    public function tieneAdjuntos(): bool
    {
        return !empty($this->adjuntos);
    }

    public function getTextoTipo(): string
    {
        return match($this->tipo) {
            'comentario' => 'Comentario',
            'cambio_estado' => 'Cambio de Estado',
            'asignacion' => 'Asignación',
            'escalamiento' => 'Escalamiento',
            default => ucfirst(str_replace('_', ' ', $this->tipo))
        };
    }

    public function getIconoTipo(): string
    {
        return match($this->tipo) {
            'comentario' => 'bi bi-chat',
            'cambio_estado' => 'bi bi-arrow-repeat',
            'asignacion' => 'bi bi-person-check',
            'escalamiento' => 'bi bi-arrow-up',
            default => 'bi bi-info-circle'
        };
    }

    // Scopes
    public function scopePublicos($query)
    {
        return $query->where('es_interno', false);
    }

    public function scopeInternos($query)
    {
        return $query->where('es_interno', true);
    }

    public function scopeNoLeidos($query)
    {
        return $query->whereNull('fecha_lectura');
    }

    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}
