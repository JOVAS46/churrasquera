<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Insumo;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $stats = [
            'insumos' => Insumo::count(),
            'categorias' => Categoria::count(),
            'mesas' => 0,
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    }
}
