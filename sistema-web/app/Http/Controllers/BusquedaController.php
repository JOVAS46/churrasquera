<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Busqueda;
use App\Models\Producto;
use App\Models\Receta;
use App\Models\Categoria;
use App\Models\Insumo;
use Inertia\Inertia;

class BusquedaController extends Controller
{
    public function buscar(Request $request)
    {
        $termino = $request->get('q', '');
        $tipo = $request->get('tipo', 'general');

        if (empty($termino)) {
            return redirect()->back();
        }

        $resultados = [];
        $totalResultados = 0;

        switch ($tipo) {
            case 'productos':
                $resultados = Producto::where('nombre', 'ILIKE', "%{$termino}%")
                                    ->orWhere('descripcion', 'ILIKE', "%{$termino}%")
                                    ->with('categoria')
                                    ->paginate(10);
                $totalResultados = $resultados->total();
                break;

            case 'recetas':
                $resultados = Receta::where('nombre', 'ILIKE', "%{$termino}%")
                                  ->orWhere('indicaciones', 'ILIKE', "%{$termino}%")
                                  ->with('categoria')
                                  ->paginate(10);
                $totalResultados = $resultados->total();
                break;

            case 'categorias':
                $resultados = Categoria::where('nombre', 'ILIKE', "%{$termino}%")
                                     ->orWhere('descripcion', 'ILIKE', "%{$termino}%")
                                     ->paginate(10);
                $totalResultados = $resultados->total();
                break;

            case 'insumos':
                $resultados = Insumo::where('nombre', 'ILIKE', "%{$termino}%")
                                  ->orWhere('descripcion', 'ILIKE', "%{$termino}%")
                                  ->with(['categoria', 'unidadMedida'])
                                  ->paginate(10);
                $totalResultados = $resultados->total();
                break;

            default: // búsqueda general
                $productos = Producto::where('nombre', 'ILIKE', "%{$termino}%")->limit(5)->get();
                $recetas = Receta::where('nombre', 'ILIKE', "%{$termino}%")->limit(5)->get();
                $categorias = Categoria::where('nombre', 'ILIKE', "%{$termino}%")->limit(5)->get();
                $insumos = Insumo::where('nombre', 'ILIKE', "%{$termino}%")->limit(5)->get();
                
                $resultados = [
                    'productos' => $productos,
                    'recetas' => $recetas,
                    'categorias' => $categorias,
                    'insumos' => $insumos
                ];
                
                $totalResultados = $productos->count() + $recetas->count() + 
                                 $categorias->count() + $insumos->count();
                break;
        }

        // Registrar la búsqueda
        Busqueda::registrar(
            $termino, 
            $tipo, 
            $totalResultados, 
            auth()->id()
        );

        return Inertia::render('Busqueda/Resultados', [
            'termino' => $termino,
            'tipo' => $tipo,
            'resultados' => $resultados,
            'totalResultados' => $totalResultados,
            'terminosPopulares' => Busqueda::obtenerTerminosPopulares(5)
        ]);
    }

    public function sugerencias(Request $request)
    {
        $termino = $request->get('q', '');
        
        if (strlen($termino) < 2) {
            return response()->json([]);
        }

        $sugerencias = [];

        // Buscar en productos
        $productos = Producto::where('nombre', 'ILIKE', "%{$termino}%")
                           ->select('nombre', 'id_producto')
                           ->limit(3)
                           ->get()
                           ->map(fn($p) => ['nombre' => $p->nombre, 'tipo' => 'producto', 'url' => "/productos/{$p->id_producto}"]);

        // Buscar en recetas
        $recetas = Receta::where('nombre', 'ILIKE', "%{$termino}%")
                        ->select('nombre', 'id_receta')
                        ->limit(3)
                        ->get()
                        ->map(fn($r) => ['nombre' => $r->nombre, 'tipo' => 'receta', 'url' => "/recetas/{$r->id_receta}"]);

        $sugerencias = array_merge($productos->toArray(), $recetas->toArray());

        return response()->json($sugerencias);
    }
}