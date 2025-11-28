<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\MenuNavegacion;
use App\Models\ConfiguracionUsuario;
use App\Models\ContadorVisitas;
use App\Models\Busqueda;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     */
    public function share(Request $request): array
    {
        // Obtener el rol del usuario autenticado
        $idRol = $request->user()->id_rol ?? null;

        // Obtener el menú jerárquico para sidebar filtrado por rol
        $menus = MenuNavegacion::getMenuJerarquico('sidebar', $idRol);

        // Obtener configuración del usuario
        $configuracion = $request->user() ? 
            ConfiguracionUsuario::obtenerConfiguracion($request->user()->id_usuario) :
            ConfiguracionUsuario::getConfiguracionDefault();

        // Obtener visitas de la página actual
        $paginaActual = $request->path() === '/' ? '/home' : $request->path();
        $visitasHoy = ContadorVisitas::obtenerVisitasHoy($paginaActual);
        $visitasTotal = ContadorVisitas::obtenerVisitasTotal($paginaActual);

        // Obtener búsquedas recientes del usuario
        $busquedasRecientes = $request->user() ? 
            Busqueda::obtenerBusquedasRecientes($request->user()->id_usuario) : [];

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id_usuario,
                    'nombre' => $request->user()->nombre,
                    'apellido' => $request->user()->apellido,
                    'email' => $request->user()->email,
                    'id_rol' => $request->user()->id_rol,
                    'rol' => $request->user()->rol ? [
                        'id' => $request->user()->rol->id_rol,
                        'nombre' => $request->user()->rol->nombre_rol,
                    ] : null,
                ] : null,
            ],
            'menus' => $menus,
            'configuracion' => $configuracion,
            'contador' => [
                'visitas_hoy' => $visitasHoy,
                'visitas_total' => $visitasTotal
            ],
            'busquedas_recientes' => $busquedasRecientes,
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'info' => $request->session()->get('info'),
            ],
        ]);
    }
}
