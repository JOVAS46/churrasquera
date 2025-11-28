<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ConfiguracionUsuario;
use App\Models\User;

class ConfiguracionInicialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todos los usuarios existentes
        $usuarios = User::all();

        foreach ($usuarios as $usuario) {
            // Crear configuración inicial basada en algunos factores
            $temaBase = $this->determinarTemaInicial($usuario);
            
            ConfiguracionUsuario::updateOrCreate(
                ['id_usuario' => $usuario->id_usuario],
                [
                    'tema' => $temaBase,
                    'modo' => 'dia', // Por defecto modo día
                    'tamano_letra' => 'normal',
                    'contraste' => 'normal',
                ]
            );
        }

        $this->command->info('✅ Configuraciones iniciales creadas correctamente');
    }

    /**
     * Determinar el tema inicial basado en características del usuario
     */
    private function determinarTemaInicial($usuario): string
    {
        // Lógica para determinar tema basado en edad, rol, etc.
        
        // Si el usuario es administrador o gerente (verificar por id_rol)
        if ($usuario->id_rol == 1) { // Asumiendo que 1 es admin
            return 'adultos';
        }

        // Si el usuario es joven (nombre contiene ciertos patrones o edad inferida)
        $nombreMinuscula = strtolower($usuario->nombre);
        $patronesJovenes = ['alex', 'sam', 'jordan', 'taylor', 'morgan', 'casey'];
        
        foreach ($patronesJovenes as $patron) {
            if (str_contains($nombreMinuscula, $patron)) {
                return 'jovenes';
            }
        }

        // Si el email contiene dominios populares entre jóvenes
        $emailDomain = substr(strrchr($usuario->email, "@"), 1);
        $dominiosJovenes = ['gmail.com', 'hotmail.com', 'yahoo.com', 'outlook.com'];
        
        if (in_array($emailDomain, $dominiosJovenes)) {
            return 'jovenes';
        }

        // Por defecto, tema adultos (más profesional)
        return 'adultos';
    }
}
