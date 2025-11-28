<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConfiguracionUsuario extends Model
{
    protected $table = 'configuracion_usuario';

    protected $fillable = [
        'id_usuario',
        'tema',
        'modo',
        'tamano_letra',
        'contraste'
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public static function obtenerConfiguracion($idUsuario)
    {
        return static::where('id_usuario', $idUsuario)->first() ?: static::getConfiguracionDefault();
    }

    public static function getConfiguracionDefault()
    {
        return (object) [
            'tema' => 'adultos',
            'modo' => 'auto',
            'tamano_letra' => 'normal',
            'contraste' => 'normal'
        ];
    }
}