<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use App\Models\User;
use App\Models\Pedido;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MesaController extends Controller
{
    /**
     * Display a listing of mesas
     */
    public function index(Request $request)
    {
        $mesas = Mesa::with(['mesero', 'pedidoActual.cliente', 'reservas' => function($query) {
            $query->whereDate('fecha_reserva', today())->where('estado', 'confirmada');
        }])->get();

        // Agrupar mesas por ubicación si existe
        $mesasPorUbicacion = $mesas->groupBy('ubicacion');

        // Estadísticas
        $estadisticas = [
            'total' => $mesas->count(),
            'disponibles' => $mesas->where('estado', 'disponible')->count(),
            'ocupadas' => $mesas->where('estado', 'ocupada')->count(),
            'reservadas' => $mesas->where('estado', 'reservada')->count(),
            'mantenimiento' => $mesas->where('estado', 'mantenimiento')->count(),
        ];

        // Meseros disponibles para asignación
        $meseros = User::where('id_rol', 3)->where('estado', true)->get();

        return Inertia::render('Mesas/Index', [
            'mesas' => $mesas,
            'mesasPorUbicacion' => $mesasPorUbicacion,
            'estadisticas' => $estadisticas,
            'meseros' => $meseros,
            'userRole' => Auth::user()->id_rol
        ]);
    }

    /**
     * Show the form for creating a new mesa
     */
    public function create()
    {
        // Solo gerentes pueden crear mesas
        if (Auth::user()->id_rol !== 1) {
            abort(403, 'No tienes permiso para crear mesas');
        }

        $meseros = User::where('id_rol', 3)->where('estado', true)->get();
        
        return Inertia::render('Mesas/Create', [
            'meseros' => $meseros
        ]);
    }

    /**
     * Store a newly created mesa
     */
    public function store(Request $request)
    {
        // Solo gerentes pueden crear mesas
        if (Auth::user()->id_rol !== 1) {
            abort(403, 'No tienes permiso para crear mesas');
        }

        $request->validate([
            'numero_mesa' => 'required|integer|unique:mesa,numero_mesa',
            'capacidad' => 'required|integer|min:1|max:20',
            'ubicacion' => 'nullable|string|max:50',
            'id_mesero' => 'nullable|exists:usuario,id_usuario',
        ]);

        DB::beginTransaction();
        try {
            $mesa = Mesa::create([
                'numero_mesa' => $request->numero_mesa,
                'capacidad' => $request->capacidad,
                'ubicacion' => $request->ubicacion,
                'estado' => 'disponible',
                'id_mesero' => $request->id_mesero,
            ]);

            DB::commit();

            return redirect()->route('mesas.index')
                           ->with('success', 'Mesa creada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la mesa: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified mesa
     */
    public function show(Mesa $mesa)
    {
        $mesa->load([
            'mesero',
            'pedidos' => function($query) {
                $query->with(['cliente', 'detalles.producto'])
                      ->orderBy('fecha_pedido', 'desc')
                      ->take(10);
            },
            'reservas' => function($query) {
                $query->with('cliente')
                      ->whereDate('fecha_reserva', '>=', today())
                      ->orderBy('fecha_reserva', 'asc')
                      ->take(5);
            }
        ]);

        return Inertia::render('Mesas/Show', [
            'mesa' => $mesa
        ]);
    }

    /**
     * Show the form for editing the specified mesa
     */
    public function edit(Mesa $mesa)
    {
        // Solo gerentes pueden editar mesas
        if (Auth::user()->id_rol !== 1) {
            abort(403, 'No tienes permiso para editar mesas');
        }

        $meseros = User::where('id_rol', 3)->where('estado', true)->get();

        return Inertia::render('Mesas/Edit', [
            'mesa' => $mesa,
            'meseros' => $meseros
        ]);
    }

    /**
     * Update the specified mesa
     */
    public function update(Request $request, Mesa $mesa)
    {
        // Solo gerentes pueden editar mesas
        if (Auth::user()->id_rol !== 1) {
            abort(403, 'No tienes permiso para editar mesas');
        }

        $request->validate([
            'numero_mesa' => 'required|integer|unique:mesa,numero_mesa,' . $mesa->id_mesa . ',id_mesa',
            'capacidad' => 'required|integer|min:1|max:20',
            'ubicacion' => 'nullable|string|max:50',
            'estado' => 'required|in:disponible,ocupada,reservada,mantenimiento',
            'id_mesero' => 'nullable|exists:usuario,id_usuario',
        ]);

        DB::beginTransaction();
        try {
            $mesa->update($request->all());

            DB::commit();

            return redirect()->route('mesas.index')
                           ->with('success', 'Mesa actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar la mesa: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified mesa
     */
    public function destroy(Mesa $mesa)
    {
        // Solo gerentes pueden eliminar mesas
        if (Auth::user()->id_rol !== 1) {
            abort(403, 'No tienes permiso para eliminar mesas');
        }

        // Verificar que no tenga pedidos activos o reservas pendientes
        $pedidosActivos = $mesa->pedidos()->whereIn('estado', ['pendiente', 'en_preparacion', 'listo'])->count();
        $reservasActivas = $mesa->reservas()->whereIn('estado', ['pendiente', 'confirmada'])->count();

        if ($pedidosActivos > 0 || $reservasActivas > 0) {
            return back()->with('error', 'No se puede eliminar la mesa porque tiene pedidos o reservas activas');
        }

        DB::beginTransaction();
        try {
            $mesa->delete();

            DB::commit();

            return redirect()->route('mesas.index')
                           ->with('success', 'Mesa eliminada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar la mesa: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado de la mesa
     */
    public function cambiarEstado(Request $request, Mesa $mesa)
    {
        $request->validate([
            'estado' => 'required|in:disponible,ocupada,reservada,mantenimiento'
        ]);

        // Solo meseros pueden cambiar entre disponible/ocupada, gerentes todo
        $user = Auth::user();
        if ($user->id_rol === 3) { // Mesero
            if (!in_array($request->estado, ['disponible', 'ocupada']) || $mesa->id_mesero !== $user->id_usuario) {
                abort(403, 'No tienes permiso para cambiar este estado de mesa');
            }
        } elseif (!in_array($user->id_rol, [1, 2])) { // Solo gerentes y cajeros pueden cambiar otros estados
            abort(403, 'No tienes permiso para cambiar el estado de mesas');
        }

        DB::beginTransaction();
        try {
            $estadoAnterior = $mesa->estado;
            $mesa->update(['estado' => $request->estado]);

            DB::commit();

            return back()->with('success', "Estado de mesa cambiado de {$estadoAnterior} a {$request->estado}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cambiar estado: ' . $e->getMessage());
        }
    }

    /**
     * Asignar mesero a la mesa
     */
    public function asignarMesero(Request $request, Mesa $mesa)
    {
        $request->validate([
            'id_mesero' => 'nullable|exists:usuario,id_usuario'
        ]);

        // Solo gerentes pueden asignar meseros
        if (Auth::user()->id_rol !== 1) {
            abort(403, 'No tienes permiso para asignar meseros');
        }

        // Verificar que el usuario sea mesero
        if ($request->id_mesero) {
            $mesero = User::find($request->id_mesero);
            if ($mesero->id_rol !== 3) {
                return back()->with('error', 'El usuario seleccionado no es un mesero');
            }
        }

        DB::beginTransaction();
        try {
            $mesa->update(['id_mesero' => $request->id_mesero]);

            $mensaje = $request->id_mesero 
                ? 'Mesero asignado exitosamente'
                : 'Mesero removido exitosamente';

            DB::commit();

            return back()->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al asignar mesero: ' . $e->getMessage());
        }
    }

    /**
     * Ocupar mesa (crear pedido)
     */
    public function ocupar(Request $request, Mesa $mesa)
    {
        $request->validate([
            'id_cliente' => 'nullable|exists:usuario,id_usuario'
        ]);

        // Verificar que la mesa esté disponible
        if ($mesa->estado !== 'disponible') {
            return back()->with('error', 'La mesa no está disponible');
        }

        // Solo meseros asignados o gerentes pueden ocupar mesas
        $user = Auth::user();
        if ($user->id_rol === 3 && $mesa->id_mesero !== $user->id_usuario) {
            abort(403, 'No tienes permiso para ocupar esta mesa');
        } elseif (!in_array($user->id_rol, [1, 2, 3])) {
            abort(403, 'No tienes permiso para ocupar mesas');
        }

        DB::beginTransaction();
        try {
            // Cambiar estado de la mesa
            $mesa->update(['estado' => 'ocupada']);

            // Redirigir a crear pedido con la mesa preseleccionada
            DB::commit();

            return redirect()->route('pedidos.create', ['mesa_id' => $mesa->id_mesa])
                           ->with('success', 'Mesa ocupada. Crear pedido...');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al ocupar la mesa: ' . $e->getMessage());
        }
    }

    /**
     * Liberar mesa
     */
    public function liberar(Mesa $mesa)
    {
        // Verificar que la mesa esté ocupada
        if ($mesa->estado !== 'ocupada') {
            return back()->with('error', 'La mesa no está ocupada');
        }

        // Verificar que no tenga pedidos activos
        $pedidosActivos = $mesa->pedidos()->whereIn('estado', ['pendiente', 'en_preparacion', 'listo'])->count();
        if ($pedidosActivos > 0) {
            return back()->with('error', 'No se puede liberar la mesa porque tiene pedidos activos');
        }

        // Solo meseros asignados o gerentes pueden liberar mesas
        $user = Auth::user();
        if ($user->id_rol === 3 && $mesa->id_mesero !== $user->id_usuario) {
            abort(403, 'No tienes permiso para liberar esta mesa');
        } elseif (!in_array($user->id_rol, [1, 2, 3])) {
            abort(403, 'No tienes permiso para liberar mesas');
        }

        DB::beginTransaction();
        try {
            $mesa->update(['estado' => 'disponible']);

            DB::commit();

            return back()->with('success', 'Mesa liberada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al liberar la mesa: ' . $e->getMessage());
        }
    }

    /**
     * Vista específica para meseros
     */
    public function misMesas()
    {
        $user = Auth::user();
        
        // Solo para meseros
        if ($user->id_rol !== 3) {
            abort(403, 'Esta vista es solo para meseros');
        }

        $mesas = Mesa::where('id_mesero', $user->id_usuario)
                    ->with([
                        'pedidoActual.cliente',
                        'reservas' => function($query) {
                            $query->whereDate('fecha_reserva', today())
                                  ->where('estado', 'confirmada')
                                  ->with('cliente');
                        }
                    ])
                    ->get();

        return Inertia::render('Mesas/MisMesas', [
            'mesas' => $mesas
        ]);
    }
}