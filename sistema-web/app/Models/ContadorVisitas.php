<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ContadorVisitas extends Model
{
    protected $table = 'contador_visitas';

    protected $fillable = [
        'pagina',
        'visitas',
        'fecha'
    ];

    protected $casts = [
        'fecha' => 'date',
        'visitas' => 'integer'
    ];

    public static function incrementar($pagina)
    {
        $fecha = Carbon::today();
        
        $contador = static::where('pagina', $pagina)
                          ->where('fecha', $fecha)
                          ->first();

        if ($contador) {
            $contador->increment('visitas');
        } else {
            static::create([
                'pagina' => $pagina,
                'visitas' => 1,
                'fecha' => $fecha
            ]);
        }
    }

    public static function obtenerVisitasHoy($pagina)
    {
        return static::where('pagina', $pagina)
                     ->where('fecha', Carbon::today())
                     ->value('visitas') ?? 0;
    }

    public static function obtenerVisitasTotal($pagina)
    {
        return static::where('pagina', $pagina)
                     ->sum('visitas');
    }
}