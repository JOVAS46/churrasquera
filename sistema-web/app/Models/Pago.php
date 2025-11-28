<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pago extends Model
{
    protected $table = 'pago';
    protected $primaryKey = 'id_pago';
    public $timestamps = false;
    
    protected $fillable = [
        'fecha_pago',
        'monto',
        'estado',
        'id_pedido',
        'id_metodo_pago'
    ];

    protected $casts = [
        'fecha_pago' => 'datetime',
        'monto' => 'decimal:2'
    ];

    // Relaciones
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'id_metodo_pago', 'id_metodo_pago');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeCompletados($query)
    {
        return $query->where('estado', 'completado');
    }

    public function scopeCancelados($query)
    {
        return $query->where('estado', 'cancelado');
    }

    public function scopeVencidos($query)
    {
        // Sin expiración, retorna query vacío
        return $query->where('estado', 'vencido_nunca_usado');
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_pago', today());
    }

    // Métodos de utilidad
    public function isVencido(): bool
    {
        // Sin expiración, nunca vence
        return false;
    }

    public function isPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    public function isCompletado(): bool
    {
        return $this->estado === 'completado';
    }

    public function isCancelado(): bool
    {
        return $this->estado === 'cancelado';
    }

    public function getTiempoRestante()
    {
        // Sin expiración, retorna null
        return null;
    }

    public function marcarComoCompletado($callbackData = null)
    {
        $this->update([
            'estado' => 'completado',
            'fecha_pago' => now()
        ]);

        // Actualizar estado del pedido
        if ($this->pedido) {
            $this->pedido->update(['estado_pago' => 'pagado']);
        }
    }

    public function marcarComoCancelado()
    {
        $this->update(['estado' => 'cancelado']);
        
        // Actualizar estado del pedido
        if ($this->pedido) {
            $this->pedido->update(['estado_pago' => 'cancelado']);
        }
    }


    // Estados posibles
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_COMPLETADO = 'completado';
    const ESTADO_CANCELADO = 'cancelado';

    // Métodos de pago PagoFácil
    const METODO_QR = 4; // Según la documentación de PagoFácil
    
    // Monedas
    const MONEDA_BOB = 2; // Bolivianos según PagoFácil
    
    // Tipos de documento
    const DOC_CI = 1;
    const DOC_PASAPORTE = 2;
}
