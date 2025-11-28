<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Busqueda extends Model
{
    protected $table = 'busquedas';

    protected $fillable = [
        'termino',
        'tipo',
        'resultados',
        'id_usuario',
        'fecha_busqueda'
    ];

    protected $casts = [
        'fecha_busqueda' => 'datetime',
        'resultados' => 'integer'
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public static function registrar($termino, $tipo, $resultados, $idUsuario = null)
    {
        return static::create([
            'termino' => $termino,
            'tipo' => $tipo,
            'resultados' => $resultados,
            'id_usuario' => $idUsuario,
            'fecha_busqueda' => Carbon::now()
        ]);
    }

    public static function obtenerTerminosPopulares($limite = 10)
    {
        return static::selectRaw('termino, COUNT(*) as cantidad')
                     ->groupBy('termino')
                     ->orderByDesc('cantidad')
                     ->limit($limite)
                     ->get();
    }

    public static function obtenerBusquedasRecientes($idUsuario, $limite = 5)
    {
        return static::where('id_usuario', $idUsuario)
                     ->orderByDesc('fecha_busqueda')
                     ->limit($limite)
                     ->get();
    }
}