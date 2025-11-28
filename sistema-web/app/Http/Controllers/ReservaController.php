<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Mesa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Carbon\Carbon;

class ReservaController extends Controller
{
    /**
     * Display a listing of reservas
     */
    public function index(Request $request)
    {
        $query = Reserva::with(['cliente', 'mesa']);
        
        // Filtros
        if ($request->estado) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->fecha) {
            $query->whereDate('fecha_reserva', $request->fecha);
        }
        
        if ($request->mesa_id) {
            $query->where('id_mesa', $request->mesa_id);
        }
        
        // Filtrar según el rol del usuario
        $user = Auth::user();
        if ($user->id_rol === 5) { // Cliente
            $query->where('id_cliente', $user->id_usuario);
        }
        
        $reservas = $query->orderBy('fecha_reserva', 'desc')->paginate(10);
        
        // Estadísticas
        $estadisticas = $this->obtenerEstadisticas($user);
        
        return Inertia::render('Reservas/Index', [
            'reservas' => $reservas,
            'mesas' => Mesa::all(),
            'estadisticas' => $estadisticas,
            'filtros' => $request->only(['estado', 'fecha', 'mesa_id'])
        ]);
    }

    /**
     * Show the form for creating a new reserva
     */
    public function create()
    {
        $mesasDisponibles = Mesa::disponibles()->get();
        $horas = $this->obtenerHorasDisponibles();
        // Traer todos los usuarios con rol CLIENTE (id_rol = 5)
        $clientes = User::where('id_rol', 5)->where('estado', true)->get();
        return Inertia::render('Reservas/Create', [
            'mesas' => $mesasDisponibles,
            'horas' => $horas,
            'clientes' => $clientes
        ]);
    }

    /**
     * Store a newly created reserva
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_cliente' => 'required|exists:usuario,id_usuario',
            'fecha_reserva' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'numero_personas' => 'required|integer|min:1|max:20',
            'id_mesa' => 'required|exists:mesa,id_mesa',
            'observaciones' => 'nullable|string|max:500',
        ]);

        // Verificar disponibilidad de la mesa
        $conflicto = Reserva::where('id_mesa', $request->id_mesa)
                           ->where('fecha_reserva', $request->fecha_reserva)
                           ->where('estado', '!=', 'cancelada')
                           ->where(function ($query) use ($request) {
                               $query->whereBetween('hora_inicio', [$request->hora_inicio, $request->hora_fin])
                                     ->orWhereBetween('hora_fin', [$request->hora_inicio, $request->hora_fin])
                                     ->orWhere(function ($q) use ($request) {
                                         $q->where('hora_inicio', '<=', $request->hora_inicio)
                                           ->where('hora_fin', '>=', $request->hora_fin);
                                     });
                           })->exists();

        if ($conflicto) {
            return back()->with('error', 'La mesa no está disponible en el horario solicitado');
        }

        // Verificar capacidad de la mesa
        $mesa = Mesa::find($request->id_mesa);
        if ($request->numero_personas > $mesa->capacidad) {
            return back()->with('error', "La mesa seleccionada tiene capacidad para {$mesa->capacidad} personas máximo");
        }

        DB::beginTransaction();
        try {
            $reserva = Reserva::create([
                'fecha_reserva' => $request->fecha_reserva,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'numero_personas' => $request->numero_personas,
                'estado' => 'pendiente',
                'observaciones' => $request->observaciones,
                'id_cliente' => $request->id_cliente,
                'id_mesa' => $request->id_mesa,
            ]);

            DB::commit();

            return redirect()->route('reservas.show', $reserva->id_reserva)
                           ->with('success', 'Reserva creada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la reserva: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified reserva
     */
    public function show(Reserva $reserva)
    {
        // Verificar permisos
        $user = Auth::user();
        if ($user->id_rol === 5 && $reserva->id_cliente !== $user->id_usuario) {
            abort(403, 'No tienes permiso para ver esta reserva');
        }

        $reserva->load(['cliente', 'mesa']);

        return Inertia::render('Reservas/Show', [
            'reserva' => $reserva
        ]);
    }

    /**
     * Show the form for editing the specified reserva
     */
    public function edit(Reserva $reserva)
    {
        // Solo permitir editar reservas pendientes
        if ($reserva->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden editar reservas pendientes');
        }

        // Verificar permisos
        $user = Auth::user();
        if ($user->id_rol === 5 && $reserva->id_cliente !== $user->id_usuario) {
            abort(403, 'No tienes permiso para editar esta reserva');
        }

        $mesasDisponibles = Mesa::disponibles()->get();
        $horas = $this->obtenerHorasDisponibles();

        return Inertia::render('Reservas/Edit', [
            'reserva' => $reserva,
            'mesas' => $mesasDisponibles,
            'horas' => $horas
        ]);
    }

    /**
     * Update the specified reserva
     */
    public function update(Request $request, Reserva $reserva)
    {
        if ($reserva->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden editar reservas pendientes');
        }

        // Verificar permisos
        $user = Auth::user();
        if ($user->id_rol === 5 && $reserva->id_cliente !== $user->id_usuario) {
            abort(403, 'No tienes permiso para editar esta reserva');
        }

        $request->validate([
            'fecha_reserva' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'numero_personas' => 'required|integer|min:1|max:20',
            'id_mesa' => 'required|exists:mesa,id_mesa',
            'observaciones' => 'nullable|string|max:500',
        ]);

        // Verificar disponibilidad (excluyendo la reserva actual)
        $conflicto = Reserva::where('id_mesa', $request->id_mesa)
                           ->where('fecha_reserva', $request->fecha_reserva)
                           ->where('estado', '!=', 'cancelada')
                           ->where('id_reserva', '!=', $reserva->id_reserva)
                           ->where(function ($query) use ($request) {
                               $query->whereBetween('hora_inicio', [$request->hora_inicio, $request->hora_fin])
                                     ->orWhereBetween('hora_fin', [$request->hora_inicio, $request->hora_fin])
                                     ->orWhere(function ($q) use ($request) {
                                         $q->where('hora_inicio', '<=', $request->hora_inicio)
                                           ->where('hora_fin', '>=', $request->hora_fin);
                                     });
                           })->exists();

        if ($conflicto) {
            return back()->with('error', 'La mesa no está disponible en el horario solicitado');
        }

        DB::beginTransaction();
        try {
            $reserva->update([
                'fecha_reserva' => $request->fecha_reserva,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'numero_personas' => $request->numero_personas,
                'observaciones' => $request->observaciones,
                'id_mesa' => $request->id_mesa,
            ]);

            DB::commit();

            return redirect()->route('reservas.show', $reserva->id_reserva)
                           ->with('success', 'Reserva actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar la reserva: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified reserva
     */
    public function destroy(Reserva $reserva)
    {
        // Verificar permisos
        $user = Auth::user();
        if ($user->id_rol === 5 && $reserva->id_cliente !== $user->id_usuario) {
            abort(403, 'No tienes permiso para eliminar esta reserva');
        }

        // Solo permitir cancelar si no está completada
        if ($reserva->estado === 'completada') {
            return back()->with('error', 'No se puede cancelar una reserva completada');
        }

        DB::beginTransaction();
        try {
            $reserva->update(['estado' => 'cancelada']);
            
            // Si la mesa estaba reservada, liberarla
            if ($reserva->mesa->estado === 'reservada') {
                $reserva->mesa->update(['estado' => 'disponible']);
            }

            DB::commit();

            return redirect()->route('reservas.index')
                           ->with('success', 'Reserva cancelada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cancelar la reserva: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado de la reserva
     */
    public function cambiarEstado(Request $request, Reserva $reserva)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,confirmada,cancelada,completada'
        ]);

        // Solo staff puede cambiar estados (no clientes)
        if (Auth::user()->id_rol === 5) {
            abort(403, 'No tienes permiso para cambiar el estado de reservas');
        }

        DB::beginTransaction();
        try {
            $estadoAnterior = $reserva->estado;
            $reserva->update(['estado' => $request->estado]);

            // Actualizar estado de la mesa según el nuevo estado
            if ($request->estado === 'confirmada') {
                $reserva->mesa->update(['estado' => 'reservada']);
            } elseif (in_array($request->estado, ['cancelada', 'completada']) && $reserva->mesa->estado === 'reservada') {
                $reserva->mesa->update(['estado' => 'disponible']);
            }

            DB::commit();

            return back()->with('success', "Estado cambiado de {$estadoAnterior} a {$request->estado}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cambiar estado: ' . $e->getMessage());
        }
    }

    /**
     * Obtener disponibilidad de mesas para una fecha específica
     */
    public function disponibilidad(Request $request)
    {
        $fecha = $request->fecha;
        $hora_inicio = $request->hora_inicio;
        $hora_fin = $request->hora_fin;

        if (!$fecha || !$hora_inicio || !$hora_fin) {
            return response()->json(['disponibles' => [], 'ocupadas' => []]);
        }

        try {
            // Obtener todas las mesas disponibles
            $mesasDisponibles = Mesa::where('estado', 'disponible')->get();

            // Obtener reservas activas para la fecha y hora dadas
            $fecha = $request->fecha;
            $hora_inicio = $request->hora_inicio;
            $hora_fin = $request->hora_fin;

            if (!$fecha || !$hora_inicio || !$hora_fin) {
                return response()->json([
                    'disponibles' => $mesasDisponibles,
                    'ocupadas' => []
                ]);
            }

            $mesasOcupadasIds = Reserva::where('fecha_reserva', $fecha)
                ->where('estado', '!=', 'cancelada')
                ->where(function ($query) use ($hora_inicio, $hora_fin) {
                    $query->whereBetween('hora_inicio', [$hora_inicio, $hora_fin])
                        ->orWhereBetween('hora_fin', [$hora_inicio, $hora_fin])
                        ->orWhere(function ($q) use ($hora_inicio, $hora_fin) {
                            $q->where('hora_inicio', '<=', $hora_inicio)
                                ->where('hora_fin', '>=', $hora_fin);
                        });
                })
                ->pluck('id_mesa')
                ->toArray();

            // Solo considerar mesas ocupadas que existan en la base de datos y estén en estado disponible
            $mesasOcupadas = Mesa::whereIn('id_mesa', $mesasOcupadasIds)
                ->where('estado', 'disponible')
                ->get();

            // Filtrar mesas disponibles para que no estén en ocupadas
            $mesasDisponiblesFiltradas = $mesasDisponibles->filter(function($mesa) use ($mesasOcupadas) {
                return !$mesasOcupadas->contains('id_mesa', $mesa->id_mesa);
            })->values();

            return response()->json([
                'disponibles' => $mesasDisponiblesFiltradas,
                'ocupadas' => $mesasOcupadas
            ]);
        } catch (\Throwable $e) {
            Log::error('Error en disponibilidad de mesas: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'disponibles' => [],
                'ocupadas' => [],
                'error' => 'Error interno al verificar disponibilidad de mesas.'
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de reservas
     */
    private function obtenerEstadisticas($user)
    {
        $query = Reserva::query();
        
        if ($user->id_rol === 5) { // Cliente
            $query->where('id_cliente', $user->id_usuario);
        }
        
        return [
            'total' => $query->count(),
            'pendientes' => $query->where('estado', 'pendiente')->count(),
            'confirmadas' => $query->where('estado', 'confirmada')->count(),
            'hoy' => $query->whereDate('fecha_reserva', today())->count(),
            'proximasSemanaa' => $query->whereBetween('fecha_reserva', [today(), today()->addDays(7)])->count()
        ];
    }

    /**
     * Obtener horas disponibles para reservas
     */
    private function obtenerHorasDisponibles()
    {
        $horas = [];
        for ($h = 11; $h <= 22; $h++) {
            for ($m = 0; $m < 60; $m += 30) {
                $hora = sprintf('%02d:%02d', $h, $m);
                $horas[] = $hora;
            }
        }
        return $horas;
    }
}