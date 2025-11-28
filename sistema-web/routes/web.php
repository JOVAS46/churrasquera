<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnidadMedidaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\MovimientoInventarioController;
use App\Http\Controllers\RecetaController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\PrediccionesController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\MenuNavegacionController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\BusquedaController;
use App\Http\Controllers\ProductoController;

// Controladores específicos por rol
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Cajero\DashboardController as CajeroDashboard;
use App\Http\Controllers\Mesero\DashboardController as MeseroDashboard;
use App\Http\Controllers\Cocinero\DashboardController as CocineroDashboard;
use App\Http\Controllers\Cliente\DashboardController as ClienteDashboard;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\VentaModernaController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\MesaController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\PagoFacilController;

use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('contar.visitas');

// Rutas para recursos con middleware de contador de visitas
Route::middleware('contar.visitas')->group(function () {
    Route::resources([
        'categorias' => CategoriaController::class,
        'productos' => ProductoController::class,
        'insumos' => InsumoController::class,
        'unidades' => UnidadMedidaController::class,
        'proveedores' => ProveedorController::class,
        'movimientos' => MovimientoInventarioController::class,
        'recetas' => RecetaController::class,
        'ventas' => VentaController::class,
    ]);

    // Ruta adicional para productos
    Route::post('productos/{producto}/toggle-disponibilidad', [ProductoController::class, 'toggleDisponibilidad'])->name('productos.toggle-disponibilidad');
});

// Rutas para el sistema de pedidos
Route::middleware(['auth', 'contar.visitas'])->group(function () {
    Route::resource('pedidos', PedidoController::class);
    Route::post('pedidos/{pedido}/estado', [PedidoController::class, 'cambiarEstado'])->name('pedidos.cambiar-estado');
    Route::get('api/pedidos/estado/{estado}', [PedidoController::class, 'porEstado'])->name('api.pedidos.estado');

    // Endpoints de prueba
    Route::get('api/pedidos/test/crear', [PedidoController::class, 'crearPedidoPrueba'])->name('api.pedidos.test.crear');
    Route::get('api/pedidos/test/estadisticas', [PedidoController::class, 'estadisticas'])->name('api.pedidos.test.estadisticas');
});

// Rutas para el sistema de ventas modernas
Route::middleware(['auth', 'contar.visitas'])->group(function () {
    Route::resource('ventas-modernas', VentaModernaController::class)->names([
        'index' => 'ventas-modernas.index',
        'create' => 'ventas-modernas.create',
        'store' => 'ventas-modernas.store',
        'show' => 'ventas-modernas.show',
        'edit' => 'ventas-modernas.edit',
        'update' => 'ventas-modernas.update',
        'destroy' => 'ventas-modernas.destroy'
    ]);
    Route::get('ventas-modernas/{venta}/ticket', [VentaModernaController::class, 'ticket'])->name('ventas-modernas.ticket');
    Route::get('api/ventas/cajero', [VentaModernaController::class, 'ventasCajero'])->name('api.ventas.cajero');
});

// Rutas para el sistema de reservas
Route::middleware(['auth', 'contar.visitas'])->group(function () {
    Route::resource('reservas', ReservaController::class);
    Route::post('reservas/{reserva}/estado', [ReservaController::class, 'cambiarEstado'])->name('reservas.cambiar-estado');
    Route::get('api/reservas/disponibilidad', [ReservaController::class, 'disponibilidad'])->name('api.reservas.disponibilidad');
});

// Rutas para gestión de mesas
Route::middleware(['auth', 'contar.visitas'])->group(function () {
    // Rutas específicas ANTES del resource para evitar conflictos
    Route::get('mis-mesas', [MesaController::class, 'misMesas'])->name('mesas.mis-mesas');
    Route::post('mesas/{mesa}/cambiar-estado', [MesaController::class, 'cambiarEstado'])->name('mesas.cambiar-estado');
    Route::post('mesas/{mesa}/asignar-mesero', [MesaController::class, 'asignarMesero'])->name('mesas.asignar-mesero');
    Route::post('mesas/{mesa}/ocupar', [MesaController::class, 'ocupar'])->name('mesas.ocupar');
    Route::post('mesas/{mesa}/liberar', [MesaController::class, 'liberar'])->name('mesas.liberar');

    // Resource routes (debe ir al final)
    Route::resource('mesas', MesaController::class);
});

// Rutas para el sistema de tickets
Route::middleware(['auth', 'contar.visitas'])->group(function () {
    Route::resource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/comentario', [TicketController::class, 'agregarComentario'])->name('tickets.comentario');
    Route::post('tickets/{ticket}/resolver', [TicketController::class, 'resolver'])->name('tickets.resolver');
    Route::post('tickets/{ticket}/cerrar', [TicketController::class, 'cerrar'])->name('tickets.cerrar');
    Route::post('tickets/{ticket}/asignar', [TicketController::class, 'asignar'])->name('tickets.asignar');
    Route::get('mis-tickets', [TicketController::class, 'misTickets'])->name('tickets.mis-tickets');

    // Rutas para el sistema de pagos PagoFácil
    Route::get('pagos', [PagoFacilController::class, 'index'])->name('pagos.index');
    Route::get('pagos/{pago}', [PagoFacilController::class, 'show'])->name('pagos.show');
    Route::get('pago/pedido/{pedido}', [PagoFacilController::class, 'mostrarPago'])->name('pagofacil.mostrar');
    Route::post('pago/generar-qr/{pedido}', [PagoFacilController::class, 'generarQR'])->name('pagofacil.generar-qr');
    Route::get('pago/consultar/{pago}', [PagoFacilController::class, 'consultarEstado'])->name('pagofacil.consultar');
    Route::post('pago/cancelar/{pago}', [PagoFacilController::class, 'cancelarPago'])->name('pagofacil.cancelar');
    Route::post('pagos/marcar-pagado', [PagoFacilController::class, 'marcarPagado'])->name('pagos.marcar-pagado');
    Route::get('pago/servicios', [PagoFacilController::class, 'serviciosHabilitados'])->name('pagofacil.servicios');

    // Callback público de PagoFácil (sin middleware de autenticación)
    Route::post('pagofacil/callback', [PagoFacilController::class, 'callback'])->name('pagofacil.callback')->withoutMiddleware(['auth']);
});

// 🧪 Endpoints de prueba para pagos (SIN autenticación)
Route::get('api/pagos/test/auth', [PagoFacilController::class, 'testAutenticacion'])->name('api.pagos.test.auth');
Route::get('api/pagos/test/crear/{pedido}', [PagoFacilController::class, 'crearPagoPrueba'])->name('api.pagos.test.crear');
Route::get('api/pagos/test/completar/{pago}', [PagoFacilController::class, 'simularPagoCompletado'])->name('api.pagos.test.completar');
Route::get('api/pagos/test/callback/{pago}', [PagoFacilController::class, 'simularCallback'])->name('api.pagos.test.callback');

// Rutas para predicciones y reportes
//Route::get('predicciones', [PrediccionesController::class, 'index'])->name('predicciones.index');
Route::get('predicciones', [PrediccionesController::class, 'index'])
    ->name('predicciones.index')
    ->middleware('role:admin'); // Solo usuarios con rol "admin"

Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
Route::get('/reportes/dashboard', [ReporteController::class, 'dashboard'])->name('reportes.dashboard');
Route::get('/reportes/ventas-data', [ReporteController::class, 'getVentasData'])->name('reportes.ventas-data');
Route::get('/reportes/parcial/semanal', [ReporteController::class, 'parcialSemanal']);
Route::get('/reportes/parcial/mensual', [ReporteController::class, 'parcialMensual']);
Route::get('/reportes/parcial/anual', [ReporteController::class, 'parcialAnual']);

// Rutas de reportes específicos
Route::get('/reportes/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
Route::get('/reportes/inventario', [ReporteController::class, 'inventario'])->name('reportes.inventario');
Route::get('/reportes/bitacora', [ReporteController::class, 'bitacora'])->name('reportes.bitacora');

// Rutas de perfil
Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

// Rutas de usuarios (admin)
//Route::resource('users', UserController::class);

Route::resource('users', UserController::class)->middleware('role:admin'); // Solo usuarios con rol "admin"

// Rutas de menú de navegación (solo admin/gerente)
Route::resource('menus', MenuNavegacionController::class);
Route::patch('menus/{id}/toggle', [MenuNavegacionController::class, 'toggleActive'])->name('menus.toggle');

// Rutas de configuración de temas
Route::post('/configuracion/guardar', [ConfiguracionController::class, 'guardar'])->name('configuracion.guardar');
Route::get('/configuracion', [ConfiguracionController::class, 'obtener'])->name('configuracion.obtener');

// API para búsqueda
Route::prefix('api')->group(function () {
    Route::get('/busqueda/sugerencias', [BusquedaController::class, 'sugerencias'])->name('api.busqueda.sugerencias');
    Route::get('/busqueda/resultados', [BusquedaController::class, 'buscar'])->name('api.busqueda.resultados');
    Route::post('/busqueda/guardar', [BusquedaController::class, 'guardar'])->name('api.busqueda.guardar');

    // API para contador de visitas
    Route::get('/contador-visitas', [ReporteController::class, 'apiContadorVisitas'])->name('api.contador-visitas');

    // 🧪 API de prueba para mesas (SIN CSRF)
    Route::post('/mesas/{mesa}/cambiar-estado', [MesaController::class, 'cambiarEstado'])->name('api.mesas.cambiar-estado')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/mesas/{mesa}/asignar-mesero', [MesaController::class, 'asignarMesero'])->name('api.mesas.asignar-mesero')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/mesas/{mesa}/ocupar', [MesaController::class, 'ocupar'])->name('api.mesas.ocupar')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/mesas/{mesa}/liberar', [MesaController::class, 'liberar'])->name('api.mesas.liberar')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::get('/mesas', [MesaController::class, 'index'])->name('api.mesas.index');
    Route::get('/mesas/{mesa}', [MesaController::class, 'show'])->name('api.mesas.show');

    // 🧪 API de prueba para pagos (SIN CSRF)
    Route::get('/pagos/{pago}/consultar', [PagoFacilController::class, 'consultarEstado'])->name('api.pagos.consultar');
    Route::post('/pagos/{pago}/cancelar', [PagoFacilController::class, 'cancelarPago'])->name('api.pagos.cancelar')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
});

// ========================================
// RUTAS ESPECÍFICAS POR ROL
// ========================================

// Rutas de Administrador/Gerente (id_rol = 1)
Route::prefix('admin')->middleware(['auth', 'contar.visitas'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('admin.dashboard');
    Route::get('/reportes', [AdminDashboard::class, 'generarReporte'])->name('admin.reportes');
    Route::post('/reabastecer', [AdminDashboard::class, 'reabastecer'])->name('admin.reabastecer');
});

// Rutas de Cajero (id_rol = 2)
Route::prefix('cajero')->middleware(['auth', 'contar.visitas'])->group(function () {
    Route::get('/dashboard', [CajeroDashboard::class, 'index'])->name('cajero.dashboard');
    Route::post('/procesar-venta', [CajeroDashboard::class, 'procesarVenta'])->name('cajero.procesar-venta');
    Route::post('/abrir-caja', [CajeroDashboard::class, 'abrirCaja'])->name('cajero.abrir-caja');
    Route::get('/ventas/{id}', [CajeroDashboard::class, 'verVenta'])->name('cajero.ver-venta');
    Route::get('/ventas/{id}/imprimir', [CajeroDashboard::class, 'imprimirRecibo'])->name('cajero.imprimir-recibo');
});

// Rutas de Mesero (id_rol = 3)
Route::prefix('mesero')->middleware(['auth', 'contar.visitas'])->group(function () {
    Route::get('/dashboard', [MeseroDashboard::class, 'index'])->name('mesero.dashboard');
    Route::post('/ocupar-mesa', [MeseroDashboard::class, 'ocuparMesa'])->name('mesero.ocupar-mesa');
    Route::post('/liberar-mesa', [MeseroDashboard::class, 'liberarMesa'])->name('mesero.liberar-mesa');
    Route::post('/marcar-limpia', [MeseroDashboard::class, 'marcarLimpia'])->name('mesero.marcar-limpia');
    Route::get('/nuevo-pedido', [MeseroDashboard::class, 'nuevoPedido'])->name('mesero.nuevo-pedido');
    Route::get('/pedidos/{id}', [MeseroDashboard::class, 'verPedido'])->name('mesero.ver-pedido');
    Route::post('/marcar-listo', [MeseroDashboard::class, 'marcarListo'])->name('mesero.marcar-listo');
});

// Rutas de Cocinero (id_rol = 4)
Route::prefix('cocinero')->middleware(['auth', 'contar.visitas'])->group(function () {
    Route::get('/dashboard', [CocineroDashboard::class, 'index'])->name('cocinero.dashboard');
    Route::post('/iniciar-preparacion', [CocineroDashboard::class, 'iniciarPreparacion'])->name('cocinero.iniciar-preparacion');
    Route::post('/marcar-item-listo', [CocineroDashboard::class, 'marcarItemListo'])->name('cocinero.marcar-item-listo');
    Route::post('/completar-pedido', [CocineroDashboard::class, 'completarPedido'])->name('cocinero.completar-pedido');
    Route::post('/solicitar-reposicion', [CocineroDashboard::class, 'solicitarReposicion'])->name('cocinero.solicitar-reposicion');
    Route::get('/pedidos/{id}', [CocineroDashboard::class, 'verPedido'])->name('cocinero.ver-pedido');
});

// Rutas de Cliente (id_rol = 5)
Route::prefix('cliente')->middleware(['auth', 'contar.visitas'])->group(function () {
    Route::get('/dashboard', [ClienteDashboard::class, 'index'])->name('cliente.dashboard');
    Route::post('/enviar-pedido', [ClienteDashboard::class, 'enviarPedido'])->name('cliente.enviar-pedido');
    Route::get('/nueva-reserva', [ClienteDashboard::class, 'nuevaReserva'])->name('cliente.nueva-reserva');
    Route::post('/guardar-reserva', [ClienteDashboard::class, 'guardarReserva'])->name('cliente.guardar-reserva');
    Route::delete('/reservas/{id}', [ClienteDashboard::class, 'cancelarReserva'])->name('cliente.cancelar-reserva');
    Route::post('/repetir-pedido', [ClienteDashboard::class, 'repetirPedido'])->name('cliente.repetir-pedido');
    Route::get('/calificar-pedido/{id}', [ClienteDashboard::class, 'calificarPedido'])->name('cliente.calificar-pedido');
});

// Ruta de redirección según rol después del login
Route::get('/redirect-dashboard', function() {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    
    $rol = auth()->user()->id_rol;
    
    switch ($rol) {
        case 1: // Gerente/Admin
            return redirect()->route('admin.dashboard');
        case 2: // Cajero
            return redirect()->route('cajero.dashboard');
        case 3: // Mesero
            return redirect()->route('mesero.dashboard');
        case 4: // Cocinero
            return redirect()->route('cocinero.dashboard');
        case 5: // Cliente
            return redirect()->route('cliente.dashboard');
        default:
            return redirect()->route('home');
    }
})->name('redirect.dashboard')->middleware('auth');