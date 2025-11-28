<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Insumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductoController extends Controller
{
    /**
     * Display a listing of productos
     */
    public function index(Request $request)
    {
        $query = Producto::with('categoria');

        // Filtros
        if ($request->buscar) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', "%{$request->buscar}%")
                  ->orWhere('descripcion', 'like', "%{$request->buscar}%");
            });
        }

        if ($request->id_categoria) {
            $query->where('id_categoria', $request->id_categoria);
        }

        if ($request->has('disponible')) {
            $query->where('disponible', $request->disponible);
        }

        $productos = $query->orderBy('nombre')->paginate(12);

        return Inertia::render('Productos/Index', [
            'productos' => $productos,
            'categorias' => Categoria::where('tipo', 'plato')->orderBy('nombre')->get(),
            'filtros' => $request->only(['buscar', 'id_categoria', 'disponible'])
        ]);
    }

    /**
     * Show the form for creating a new producto
     */
    public function create()
    {
        return Inertia::render('Productos/Create', [
            'categorias' => Categoria::where('tipo', 'plato')->orderBy('nombre')->get(),
            'insumos' => Insumo::orderBy('nombre')->get()
        ]);
    }

    /**
     * Store a newly created producto
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'precio' => 'required|numeric|min:0',
            'tiempo_preparacion' => 'nullable|integer|min:0',
            'id_categoria' => 'required|exists:categoria,id_categoria',
            'disponible' => 'boolean',
            'imagen' => 'nullable|image|max:2048',
            'insumos' => 'nullable|array',
            'insumos.*.id_insumo' => 'required|exists:insumo,id_insumo',
            'insumos.*.cantidad_necesaria' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except('imagen', 'insumos');

            // Manejar imagen si existe
            if ($request->hasFile('imagen')) {
                $path = $request->file('imagen')->store('productos', 'public');
                $data['imagen'] = $path;
            }

            $producto = Producto::create($data);

            // Asociar insumos si existen
            if ($request->has('insumos') && is_array($request->insumos)) {
                foreach ($request->insumos as $insumo) {
                    $producto->insumos()->attach($insumo['id_insumo'], [
                        'cantidad_necesaria' => $insumo['cantidad_necesaria']
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('productos.index')
                           ->with('success', 'Producto creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified producto
     */
    public function show(Producto $producto)
    {
        $producto->load(['categoria', 'insumos']);

        return Inertia::render('Productos/Show', [
            'producto' => $producto
        ]);
    }

    /**
     * Show the form for editing the specified producto
     */
    public function edit(Producto $producto)
    {
        $producto->load('insumos');

        return Inertia::render('Productos/Edit', [
            'producto' => $producto,
            'categorias' => Categoria::where('tipo', 'plato')->orderBy('nombre')->get(),
            'insumos' => Insumo::orderBy('nombre')->get()
        ]);
    }

    /**
     * Update the specified producto
     */
    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'precio' => 'required|numeric|min:0',
            'tiempo_preparacion' => 'nullable|integer|min:0',
            'id_categoria' => 'required|exists:categoria,id_categoria',
            'disponible' => 'boolean',
            'imagen' => 'nullable|image|max:2048',
            'insumos' => 'nullable|array',
            'insumos.*.id_insumo' => 'required|exists:insumo,id_insumo',
            'insumos.*.cantidad_necesaria' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except('imagen', 'insumos');

            // Manejar imagen si existe
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior si existe
                if ($producto->imagen) {
                    Storage::disk('public')->delete($producto->imagen);
                }
                $path = $request->file('imagen')->store('productos', 'public');
                $data['imagen'] = $path;
            }

            $producto->update($data);

            // Actualizar insumos
            $producto->insumos()->detach();
            if ($request->has('insumos') && is_array($request->insumos)) {
                foreach ($request->insumos as $insumo) {
                    $producto->insumos()->attach($insumo['id_insumo'], [
                        'cantidad_necesaria' => $insumo['cantidad_necesaria']
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('productos.index')
                           ->with('success', 'Producto actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified producto
     */
    public function destroy(Producto $producto)
    {
        DB::beginTransaction();
        try {
            // Eliminar imagen si existe
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }

            // Eliminar relaciones con insumos
            $producto->insumos()->detach();

            $producto->delete();

            DB::commit();

            return redirect()->route('productos.index')
                           ->with('success', 'Producto eliminado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Toggle disponibilidad del producto
     */
    public function toggleDisponibilidad(Producto $producto)
    {
        try {
            $producto->update(['disponible' => !$producto->disponible]);

            return back()->with('success', 'Disponibilidad actualizada exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar disponibilidad: ' . $e->getMessage());
        }
    }
}
