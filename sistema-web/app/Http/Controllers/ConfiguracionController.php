<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfiguracionUsuario;
use Inertia\Inertia;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $configuracion = ConfiguracionUsuario::obtenerConfiguracion(auth()->id());

        return Inertia::render('Configuracion/Index', [
            'configuracion' => $configuracion
        ]);
    }

    public function actualizar(Request $request)
    {
        $request->validate([
            'tema' => 'required|in:ninos,jovenes,adultos',
            'modo' => 'required|in:dia,noche,auto',
            'tamano_letra' => 'required|in:pequeno,normal,grande',
            'contraste' => 'required|in:normal,alto'
        ], [
            'tema.required' => 'El tema es obligatorio.',
            'tema.in' => 'El tema debe ser niños, jóvenes o adultos.',
            'modo.required' => 'El modo es obligatorio.',
            'modo.in' => 'El modo debe ser día, noche o automático.',
            'tamano_letra.required' => 'El tamaño de letra es obligatorio.',
            'tamano_letra.in' => 'El tamaño de letra debe ser pequeño, normal o grande.',
            'contraste.required' => 'El contraste es obligatorio.',
            'contraste.in' => 'El contraste debe ser normal o alto.',
        ]);

        ConfiguracionUsuario::updateOrCreate(
            ['id_usuario' => auth()->id()],
            $request->only(['tema', 'modo', 'tamano_letra', 'contraste'])
        );

        return redirect()->back()->with('success', 'Configuración actualizada correctamente.');
    }

    public function cambiarTema(Request $request)
    {
        $request->validate([
            'tema' => 'required|in:ninos,jovenes,adultos'
        ]);

        ConfiguracionUsuario::updateOrCreate(
            ['id_usuario' => auth()->id()],
            ['tema' => $request->tema]
        );

        return response()->json(['success' => true]);
    }

    public function cambiarModo(Request $request)
    {
        $request->validate([
            'modo' => 'required|in:dia,noche,auto'
        ]);

        ConfiguracionUsuario::updateOrCreate(
            ['id_usuario' => auth()->id()],
            ['modo' => $request->modo]
        );

        return response()->json(['success' => true]);
    }
}