<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketComentario;
use App\Models\User;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class TicketController extends Controller
{
    /**
     * Display a listing of tickets
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['usuarioCreador', 'usuarioAsignado', 'mesa', 'pedido', 'reserva'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('asignado_a')) {
            $query->where('usuario_asignado_id', $request->asignado_a);
        }

        if ($request->filled('creado_por')) {
            $query->where('usuario_creador_id', $request->creado_por);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        // Filtro por rol del usuario
        $user = Auth::user();
        if ($user->id_rol === 5) { // Cliente
            $query->where('usuario_creador_id', $user->id_usuario)
                  ->where('es_interno', false);
        } elseif ($user->id_rol !== 1) { // No es admin
            // Mostrar tickets asignados o públicos
            $query->where(function($q) use ($user) {
                $q->where('usuario_asignado_id', $user->id_usuario)
                  ->orWhere('es_interno', false);
            });
        }

        $tickets = $query->paginate(15);

        // Estadísticas
        $estadisticas = [
            'total' => Ticket::count(),
            'abiertos' => Ticket::abiertos()->count(),
            'vencidos' => Ticket::vencidos()->count(),
            'resueltos_hoy' => Ticket::where('estado', 'resuelto')
                ->whereDate('fecha_resolucion', today())
                ->count(),
            'por_prioridad' => [
                'critica' => Ticket::where('prioridad', 'critica')->abiertos()->count(),
                'alta' => Ticket::where('prioridad', 'alta')->abiertos()->count(),
                'media' => Ticket::where('prioridad', 'media')->abiertos()->count(),
                'baja' => Ticket::where('prioridad', 'baja')->abiertos()->count(),
            ]
        ];

        // Usuarios para filtros
        $usuarios = User::select('id_usuario', 'nombre', 'apellido', 'id_rol')
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Tickets/Index', [
            'tickets' => $tickets,
            'estadisticas' => $estadisticas,
            'usuarios' => $usuarios,
            'filtros' => $request->all(),
            'canCreate' => $this->canCreateTicket(),
            'canAssign' => $this->canAssignTickets()
        ]);
    }

    /**
     * Show the form for creating a new ticket
     */
    public function create(Request $request)
    {
        // Datos para pre-llenar el formulario
        $preData = [];
        
        if ($request->filled('mesa_id')) {
            $mesa = Mesa::find($request->mesa_id);
            $preData['mesa'] = $mesa;
        }

        if ($request->filled('pedido_id')) {
            $pedido = Pedido::with(['mesa', 'cliente'])->find($request->pedido_id);
            $preData['pedido'] = $pedido;
        }

        if ($request->filled('reserva_id')) {
            $reserva = Reserva::with(['mesa', 'cliente'])->find($request->reserva_id);
            $preData['reserva'] = $reserva;
        }

        // Usuarios para asignación (solo staff)
        $usuariosAsignacion = User::whereIn('id_rol', [1, 2, 3, 4])
            ->select('id_usuario', 'nombre', 'apellido', 'id_rol')
            ->orderBy('nombre')
            ->get();

        // Mesas, pedidos y reservas para selección
        $mesas = Mesa::select('id_mesa', 'numero_mesa', 'ubicacion')->orderBy('numero_mesa')->get();
        $pedidos = Pedido::with('cliente')->latest()->take(50)->get();
        $reservas = Reserva::with('cliente')->latest()->take(50)->get();

        return Inertia::render('Tickets/Create', [
            'usuarios' => $usuariosAsignacion,
            'mesas' => $mesas,
            'pedidos' => $pedidos,
            'reservas' => $reservas,
            'preData' => $preData
        ]);
    }

    /**
     * Store a newly created ticket
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'categoria' => 'required|in:soporte_tecnico,queja_cliente,sugerencia,problema_servicio,incidente_operativo,solicitud_mantenimiento,consulta_general',
            'prioridad' => 'required|in:baja,media,alta,critica',
            'usuario_asignado_id' => 'nullable|exists:usuario,id_usuario',
            'mesa_id' => 'nullable|exists:mesa,id_mesa',
            'pedido_id' => 'nullable|exists:pedido,id_pedido',
            'reserva_id' => 'nullable|exists:reserva,id_reserva',
            'fecha_limite' => 'nullable|date|after:now',
            'etiquetas' => 'nullable|array',
            'es_interno' => 'boolean',
            'canal_origen' => 'string|max:50'
        ]);

        try {
            DB::beginTransaction();

            $ticket = new Ticket();
            $ticket->codigo_ticket = $ticket->generarCodigoTicket();
            $ticket->titulo = $request->titulo;
            $ticket->descripcion = $request->descripcion;
            $ticket->categoria = $request->categoria;
            $ticket->prioridad = $request->prioridad;
            $ticket->estado = 'abierto';
            $ticket->usuario_creador_id = Auth::id();
            $ticket->usuario_asignado_id = $request->usuario_asignado_id;
            $ticket->mesa_id = $request->mesa_id;
            $ticket->pedido_id = $request->pedido_id;
            $ticket->reserva_id = $request->reserva_id;
            $ticket->fecha_limite = $request->fecha_limite;
            $ticket->etiquetas = $request->etiquetas;
            $ticket->es_interno = $request->boolean('es_interno');
            $ticket->canal_origen = $request->canal_origen ?? 'sistema';
            
            // Calcular tiempo de respuesta según prioridad
            $ticket->tiempo_respuesta_minutos = $this->calcularTiempoRespuesta($request->prioridad);
            
            $ticket->save();

            // Comentario inicial
            TicketComentario::create([
                'ticket_id' => $ticket->id_ticket,
                'usuario_id' => Auth::id(),
                'comentario' => 'Ticket creado: ' . $request->descripcion,
                'tipo' => 'comentario'
            ]);

            // Si se asigna a alguien, crear comentario de asignación
            if ($request->usuario_asignado_id) {
                TicketComentario::create([
                    'ticket_id' => $ticket->id_ticket,
                    'usuario_id' => Auth::id(),
                    'comentario' => 'Ticket asignado',
                    'tipo' => 'asignacion',
                    'es_interno' => true
                ]);
            }

            DB::commit();

            return redirect()->route('tickets.show', $ticket->id_ticket)
                ->with('success', 'Ticket creado exitosamente con código: ' . $ticket->codigo_ticket);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el ticket: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified ticket
     */
    public function show(Ticket $ticket)
    {
        // Verificar permisos
        if (!$this->canViewTicket($ticket)) {
            abort(403, 'No tienes permisos para ver este ticket');
        }

        $ticket->load([
            'usuarioCreador', 
            'usuarioAsignado', 
            'mesa', 
            'pedido.cliente', 
            'reserva.cliente'
        ]);

        // Comentarios con paginación
        $comentarios = $ticket->comentarios()
            ->with('usuario')
            ->when(!$this->canViewInternalComments(), function($query) {
                return $query->where('es_interno', false);
            })
            ->paginate(20);

        // Usuarios para asignación
        $usuariosAsignacion = User::whereIn('id_rol', [1, 2, 3, 4])
            ->select('id_usuario', 'nombre', 'apellido', 'id_rol')
            ->orderBy('nombre')
            ->get();

        // Tickets relacionados
        $ticketsRelacionados = collect();
        
        if ($ticket->mesa_id) {
            $ticketsRelacionados = $ticketsRelacionados->merge(
                Ticket::where('mesa_id', $ticket->mesa_id)
                    ->where('id_ticket', '!=', $ticket->id_ticket)
                    ->latest()
                    ->take(5)
                    ->get()
            );
        }

        return Inertia::render('Tickets/Show', [
            'ticket' => $ticket,
            'comentarios' => $comentarios,
            'usuarios' => $usuariosAsignacion,
            'ticketsRelacionados' => $ticketsRelacionados,
            'canEdit' => $this->canEditTicket($ticket),
            'canComment' => $this->canCommentTicket($ticket),
            'canAssign' => $this->canAssignTickets(),
            'canResolve' => $this->canResolveTicket($ticket)
        ]);
    }

    /**
     * Show the form for editing the specified ticket
     */
    public function edit(Ticket $ticket)
    {
        if (!$this->canEditTicket($ticket)) {
            abort(403, 'No tienes permisos para editar este ticket');
        }

        $ticket->load(['usuarioCreador', 'usuarioAsignado', 'mesa', 'pedido', 'reserva']);

        $usuariosAsignacion = User::whereIn('id_rol', [1, 2, 3, 4])
            ->select('id_usuario', 'nombre', 'apellido', 'id_rol')
            ->orderBy('nombre')
            ->get();

        $mesas = Mesa::select('id_mesa', 'numero_mesa', 'ubicacion')->orderBy('numero_mesa')->get();
        $pedidos = Pedido::with('cliente')->latest()->take(50)->get();
        $reservas = Reserva::with('cliente')->latest()->take(50)->get();

        return Inertia::render('Tickets/Edit', [
            'ticket' => $ticket,
            'usuarios' => $usuariosAsignacion,
            'mesas' => $mesas,
            'pedidos' => $pedidos,
            'reservas' => $reservas
        ]);
    }

    /**
     * Update the specified ticket
     */
    public function update(Request $request, Ticket $ticket)
    {
        if (!$this->canEditTicket($ticket)) {
            abort(403, 'No tienes permisos para editar este ticket');
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'categoria' => 'required|in:soporte_tecnico,queja_cliente,sugerencia,problema_servicio,incidente_operativo,solicitud_mantenimiento,consulta_general',
            'prioridad' => 'required|in:baja,media,alta,critica',
            'estado' => 'required|in:abierto,en_proceso,esperando_respuesta,resuelto,cerrado,escalado',
            'usuario_asignado_id' => 'nullable|exists:usuario,id_usuario',
            'fecha_limite' => 'nullable|date',
            'etiquetas' => 'nullable|array'
        ]);

        try {
            DB::beginTransaction();

            $cambios = [];
            $estadoAnterior = $ticket->estado;
            $asignadoAnterior = $ticket->usuario_asignado_id;

            $ticket->update($request->only([
                'titulo', 'descripcion', 'categoria', 'prioridad', 
                'estado', 'usuario_asignado_id', 'fecha_limite', 'etiquetas'
            ]));

            // Registrar cambios importantes
            if ($estadoAnterior !== $request->estado) {
                TicketComentario::create([
                    'ticket_id' => $ticket->id_ticket,
                    'usuario_id' => Auth::id(),
                    'comentario' => "Estado cambiado de {$estadoAnterior} a {$request->estado}",
                    'tipo' => 'cambio_estado',
                    'estado_anterior' => $estadoAnterior,
                    'estado_nuevo' => $request->estado,
                    'es_interno' => true
                ]);
            }

            if ($asignadoAnterior !== $request->usuario_asignado_id) {
                $usuarioAnterior = $asignadoAnterior ? User::find($asignadoAnterior)?->nombre : 'Nadie';
                $usuarioNuevo = $request->usuario_asignado_id ? User::find($request->usuario_asignado_id)?->nombre : 'Nadie';
                
                TicketComentario::create([
                    'ticket_id' => $ticket->id_ticket,
                    'usuario_id' => Auth::id(),
                    'comentario' => "Asignación cambiada de {$usuarioAnterior} a {$usuarioNuevo}",
                    'tipo' => 'asignacion',
                    'es_interno' => true
                ]);
            }

            DB::commit();

            return redirect()->route('tickets.show', $ticket->id_ticket)
                ->with('success', 'Ticket actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el ticket: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified ticket from storage
     */
    public function destroy(Ticket $ticket)
    {
        if (!$this->canDeleteTicket($ticket)) {
            abort(403, 'No tienes permisos para eliminar este ticket');
        }

        try {
            DB::beginTransaction();
            
            $ticket->comentarios()->delete();
            $ticket->delete();
            
            DB::commit();

            return redirect()->route('tickets.index')
                ->with('success', 'Ticket eliminado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar el ticket: ' . $e->getMessage()]);
        }
    }

    /**
     * Show user's tickets
     */
    public function misTickets(Request $request)
    {
        $user = Auth::user();
        
        $query = Ticket::with(['usuarioCreador', 'usuarioAsignado', 'mesa', 'pedido', 'reserva'])
            ->where(function($q) use ($user) {
                if ($user->id_rol === 5) { // Cliente
                    $q->where('usuario_creador_id', $user->id_usuario)
                      ->where('es_interno', false);
                } else { // Staff
                    $q->where('usuario_asignado_id', $user->id_usuario)
                      ->orWhere('usuario_creador_id', $user->id_usuario);
                }
            })
            ->orderBy('created_at', 'desc');

        // Aplicar filtros básicos
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        $tickets = $query->paginate(15);

        // Estadísticas personales
        $estadisticas = [
            'total' => $query->count(),
            'abiertos' => $query->clone()->where('estado', 'abierto')->count(),
            'asignados' => Ticket::where('usuario_asignado_id', $user->id_usuario)->count(),
            'creados' => Ticket::where('usuario_creador_id', $user->id_usuario)->count(),
        ];

        return Inertia::render('Tickets/MisTickets', [
            'tickets' => $tickets,
            'estadisticas' => $estadisticas,
            'filtros' => $request->all(),
        ]);
    }

    /**
     * Add a comment to the ticket
     */
    public function agregarComentario(Request $request, Ticket $ticket)
    {
        if (!$this->canCommentTicket($ticket)) {
            abort(403, 'No tienes permisos para comentar en este ticket');
        }

        $request->validate([
            'comentario' => 'required|string',
            'es_interno' => 'boolean',
            'adjuntos' => 'nullable|array'
        ]);

        $comentario = TicketComentario::create([
            'ticket_id' => $ticket->id_ticket,
            'usuario_id' => Auth::id(),
            'comentario' => $request->comentario,
            'tipo' => 'comentario',
            'es_interno' => $request->boolean('es_interno'),
            'adjuntos' => $request->adjuntos
        ]);

        return back()->with('success', 'Comentario agregado exitosamente');
    }

    /**
     * Resolve ticket
     */
    public function resolver(Request $request, Ticket $ticket)
    {
        if (!$this->canResolveTicket($ticket)) {
            abort(403, 'No tienes permisos para resolver este ticket');
        }

        $request->validate([
            'resolucion' => 'required|string'
        ]);

        $ticket->marcarComoResuelto($request->resolucion, Auth::id());

        return back()->with('success', 'Ticket marcado como resuelto');
    }

    /**
     * Close ticket
     */
    public function cerrar(Ticket $ticket)
    {
        if (!$this->canResolveTicket($ticket)) {
            abort(403, 'No tienes permisos para cerrar este ticket');
        }

        $ticket->cerrarTicket(Auth::id());

        return back()->with('success', 'Ticket cerrado exitosamente');
    }

    /**
     * Assign ticket
     */
    public function asignar(Request $request, Ticket $ticket)
    {
        if (!$this->canAssignTickets()) {
            abort(403, 'No tienes permisos para asignar tickets');
        }

        $request->validate([
            'usuario_asignado_id' => 'required|exists:usuario,id_usuario'
        ]);

        $usuarioAnterior = $ticket->usuarioAsignado?->nombre ?? 'Nadie';
        $usuarioNuevo = User::find($request->usuario_asignado_id)->nombre;

        $ticket->update([
            'usuario_asignado_id' => $request->usuario_asignado_id,
            'estado' => $ticket->estado === 'abierto' ? 'en_proceso' : $ticket->estado
        ]);

        TicketComentario::create([
            'ticket_id' => $ticket->id_ticket,
            'usuario_id' => Auth::id(),
            'comentario' => "Ticket reasignado de {$usuarioAnterior} a {$usuarioNuevo}",
            'tipo' => 'asignacion',
            'es_interno' => true
        ]);

        return back()->with('success', 'Ticket asignado exitosamente');
    }

    /**
     * Utility methods for permissions
     */
    private function canCreateTicket(): bool
    {
        return true; // Todos pueden crear tickets
    }

    private function canViewTicket(Ticket $ticket): bool
    {
        $user = Auth::user();
        
        // Admin puede ver todos
        if ($user->id_rol === 1) return true;
        
        // Cliente solo puede ver sus propios tickets no internos
        if ($user->id_rol === 5) {
            return $ticket->usuario_creador_id === $user->id_usuario && !$ticket->es_interno;
        }
        
        // Staff puede ver tickets asignados o no internos
        return $ticket->usuario_asignado_id === $user->id_usuario || !$ticket->es_interno;
    }

    private function canEditTicket(Ticket $ticket): bool
    {
        $user = Auth::user();
        
        // Admin puede editar todos
        if ($user->id_rol === 1) return true;
        
        // Cliente puede editar sus tickets abiertos
        if ($user->id_rol === 5) {
            return $ticket->usuario_creador_id === $user->id_usuario && 
                   $ticket->estado === 'abierto';
        }
        
        // Staff puede editar tickets asignados
        return $ticket->usuario_asignado_id === $user->id_usuario;
    }

    private function canDeleteTicket(Ticket $ticket): bool
    {
        return Auth::user()->id_rol === 1; // Solo admin
    }

    private function canCommentTicket(Ticket $ticket): bool
    {
        return $this->canViewTicket($ticket) && $ticket->estado !== 'cerrado';
    }

    private function canAssignTickets(): bool
    {
        return in_array(Auth::user()->id_rol, [1, 2]); // Admin y Cajero
    }

    private function canResolveTicket(Ticket $ticket): bool
    {
        $user = Auth::user();
        return in_array($user->id_rol, [1, 2, 3, 4]) && // Staff
               ($ticket->usuario_asignado_id === $user->id_usuario || $user->id_rol === 1);
    }

    private function canViewInternalComments(): bool
    {
        return Auth::user()->id_rol !== 5; // No cliente
    }

    private function calcularTiempoRespuesta(string $prioridad): int
    {
        return match($prioridad) {
            'critica' => 30,    // 30 minutos
            'alta' => 120,      // 2 horas
            'media' => 480,     // 8 horas
            'baja' => 1440,     // 24 horas
            default => 480
        };
    }
}
