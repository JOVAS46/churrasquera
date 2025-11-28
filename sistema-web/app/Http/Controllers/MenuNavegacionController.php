<?php

namespace App\Http\Controllers;

use App\Models\MenuNavegacion;
use App\Models\Rol;
use Illuminate\Http\Request;

class MenuNavegacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = MenuNavegacion::with(['padre', 'hijos', 'rol'])
                               ->orderBy('orden')
                               ->get();

        return view('menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $menusPadre = MenuNavegacion::whereNull('id_padre')
                                    ->orderBy('orden')
                                    ->get();
        $roles = Rol::all();

        return view('menus.create', compact('menusPadre', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'icono' => 'nullable|string|max:50',
            'url' => 'nullable|string|max:255',
            'orden' => 'required|integer|min:0',
            'activo' => 'boolean',
            'id_padre' => 'nullable|exists:menu_navegacion,id_menu',
            'mostrar_en' => 'required|in:ambos,header,sidebar',
            'id_rol' => 'nullable|exists:rol,id_rol',
        ], [
            'nombre.required' => 'El nombre del menú es obligatorio',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres',
            'orden.required' => 'El orden es obligatorio',
            'orden.integer' => 'El orden debe ser un número entero',
            'orden.min' => 'El orden debe ser mayor o igual a 0',
            'mostrar_en.required' => 'Debe seleccionar dónde mostrar el menú',
            'mostrar_en.in' => 'La ubicación seleccionada no es válida',
            'id_padre.exists' => 'El menú padre seleccionado no existe',
            'id_rol.exists' => 'El rol seleccionado no existe',
        ]);

        MenuNavegacion::create($validated);

        return redirect()->route('menus.index')
                        ->with('success', 'Menú creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(MenuNavegacion $menu)
    {
        $menu->load(['padre', 'hijos', 'rol']);
        return view('menus.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $menu = MenuNavegacion::findOrFail($id);
        $menusPadre = MenuNavegacion::whereNull('id_padre')
                                    ->where('id_menu', '!=', $id)
                                    ->orderBy('orden')
                                    ->get();
        $roles = Rol::all();

        return view('menus.edit', compact('menu', 'menusPadre', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $menu = MenuNavegacion::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'icono' => 'nullable|string|max:50',
            'url' => 'nullable|string|max:255',
            'orden' => 'required|integer|min:0',
            'activo' => 'boolean',
            'id_padre' => 'nullable|exists:menu_navegacion,id_menu',
            'mostrar_en' => 'required|in:ambos,header,sidebar',
            'id_rol' => 'nullable|exists:rol,id_rol',
        ], [
            'nombre.required' => 'El nombre del menú es obligatorio',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres',
            'orden.required' => 'El orden es obligatorio',
            'orden.integer' => 'El orden debe ser un número entero',
            'orden.min' => 'El orden debe ser mayor o igual a 0',
            'mostrar_en.required' => 'Debe seleccionar dónde mostrar el menú',
            'mostrar_en.in' => 'La ubicación seleccionada no es válida',
            'id_padre.exists' => 'El menú padre seleccionado no existe',
            'id_rol.exists' => 'El rol seleccionado no existe',
        ]);

        // Evitar que un menú sea su propio padre
        if ($validated['id_padre'] == $id) {
            return back()->withErrors(['id_padre' => 'Un menú no puede ser su propio padre'])
                        ->withInput();
        }

        $menu->update($validated);

        return redirect()->route('menus.index')
                        ->with('success', 'Menú actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $menu = MenuNavegacion::findOrFail($id);

        // Verificar si tiene submenús
        if ($menu->hijos()->count() > 0) {
            return back()->withErrors(['delete' => 'No se puede eliminar este menú porque tiene submenús asociados']);
        }

        $menu->delete();

        return redirect()->route('menus.index')
                        ->with('success', 'Menú eliminado exitosamente');
    }

    /**
     * Toggle menu active status
     */
    public function toggleActive($id)
    {
        $menu = MenuNavegacion::findOrFail($id);
        $menu->activo = !$menu->activo;
        $menu->save();

        return back()->with('success', 'Estado del menú actualizado');
    }
}
