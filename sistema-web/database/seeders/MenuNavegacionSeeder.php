<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuNavegacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            // 1. Dashboard (todos)
            [
                'id_menu' => 1,
                'nombre' => 'Dashboard',
                'icono' => 'fas fa-home',
                'url' => '/home',
                'orden' => 1,
                'activo' => true,
                'id_padre' => null,
                'mostrar_en' => 'sidebar',
                'id_rol' => null, // Todos los roles
            ],

            // 2. Gestión de Mesas (Mesero, Gerente)
            [
                'id_menu' => 2,
                'nombre' => 'Gestión de Mesas',
                'icono' => 'fas fa-chair',
                'url' => null,
                'orden' => 2,
                'activo' => true,
                'id_padre' => null,
                'mostrar_en' => 'sidebar',
                'id_rol' => null,
            ],
            [
                'id_menu' => 3,
                'nombre' => 'Mesas',
                'icono' => 'fas fa-table',
                'url' => '/mesas',
                'orden' => 1,
                'activo' => true,
                'id_padre' => 2,
                'mostrar_en' => 'sidebar',
                'id_rol' => null,
            ],
            [
                'id_menu' => 4,
                'nombre' => 'Reservas',
                'icono' => 'fas fa-calendar-check',
                'url' => '/reservas',
                'orden' => 2,
                'activo' => true,
                'id_padre' => 2,
                'mostrar_en' => 'sidebar',
                'id_rol' => null,
            ],

            // 3. Pedidos (Mesero, Cocinero, Gerente)
            [
                'id_menu' => 5,
                'nombre' => 'Pedidos',
                'icono' => 'fas fa-shopping-cart',
                'url' => null,
                'orden' => 3,
                'activo' => true,
                'id_padre' => null,
                'mostrar_en' => 'sidebar',
                'id_rol' => null, // Todos los roles
            ],
            [
                'id_menu' => 6,
                'nombre' => 'Nuevo Pedido',
                'icono' => 'fas fa-plus-circle',
                'url' => '/pedidos/create',
                'orden' => 1,
                'activo' => true,
                'id_padre' => 5,
                'mostrar_en' => 'sidebar',
                'id_rol' => null, // Todos los roles (incluye admin)
            ],
            [
                'id_menu' => 7,
                'nombre' => 'Lista de Pedidos',
                'icono' => 'fas fa-list',
                'url' => '/pedidos',
                'orden' => 2,
                'activo' => true,
                'id_padre' => 5,
                'mostrar_en' => 'sidebar',
                'id_rol' => null, // Todos los roles (incluye admin)
            ],

            // 4. Productos/Menú (Gerente)
            [
                'id_menu' => 8,
                'nombre' => 'Productos',
                'icono' => 'fas fa-utensils',
                'url' => null,
                'orden' => 4,
                'activo' => true,
                'id_padre' => null,
                'mostrar_en' => 'sidebar',
                'id_rol' => 1, // Solo gerente
            ],
            [
                'id_menu' => 9,
                'nombre' => 'Lista de Productos',
                'icono' => 'fas fa-hamburger',
                'url' => '/productos',
                'orden' => 1,
                'activo' => true,
                'id_padre' => 8,
                'mostrar_en' => 'sidebar',
                'id_rol' => 1,
            ],
            [
                'id_menu' => 10,
                'nombre' => 'Categorías',
                'icono' => 'fas fa-tags',
                'url' => '/categorias',
                'orden' => 2,
                'activo' => true,
                'id_padre' => 8,
                'mostrar_en' => 'sidebar',
                'id_rol' => 1,
            ],

            // 5. Inventario (Gerente, Cocinero)
            [
                'id_menu' => 11,
                'nombre' => 'Inventario',
                'icono' => 'fas fa-boxes',
                'url' => null,
                'orden' => 5,
                'activo' => true,
                'id_padre' => null,
                'mostrar_en' => 'sidebar',
                'id_rol' => null,
            ],
            [
                'id_menu' => 12,
                'nombre' => 'Insumos',
                'icono' => 'fas fa-box',
                'url' => '/insumos',
                'orden' => 1,
                'activo' => true,
                'id_padre' => 11,
                'mostrar_en' => 'sidebar',
                'id_rol' => null,
            ],
            [
                'id_menu' => 13,
                'nombre' => 'Movimientos',
                'icono' => 'fas fa-exchange-alt',
                'url' => '/movimientos',
                'orden' => 2,
                'activo' => true,
                'id_padre' => 11,
                'mostrar_en' => 'sidebar',
                'id_rol' => null,
            ],
            [
                'id_menu' => 14,
                'nombre' => 'Proveedores',
                'icono' => 'fas fa-truck',
                'url' => '/proveedores',
                'orden' => 3,
                'activo' => true,
                'id_padre' => 11,
                'mostrar_en' => 'sidebar',
                'id_rol' => 1, // Solo gerente
            ],

            // 6. Caja (Cajero, Gerente)
            [
                'id_menu' => 15,
                'nombre' => 'Caja',
                'icono' => 'fas fa-cash-register',
                'url' => null,
                'orden' => 6,
                'activo' => true,
                'id_padre' => null,
                'mostrar_en' => 'sidebar',
                'id_rol' => null,
            ],
            [
                'id_menu' => 16,
                'nombre' => 'Pagos',
                'icono' => 'fas fa-money-bill-wave',
                'url' => '/pagos',
                'orden' => 1,
                'activo' => true,
                'id_padre' => 15,
                'mostrar_en' => 'sidebar',
                'id_rol' => null,
            ],
            [
                'id_menu' => 17,
                'nombre' => 'Facturas',
                'icono' => 'fas fa-file-invoice',
                'url' => '/facturas',
                'orden' => 2,
                'activo' => true,
                'id_padre' => 15,
                'mostrar_en' => 'sidebar',
                'id_rol' => null,
            ],
            [
                'id_menu' => 18,
                'nombre' => 'Gestión de Caja',
                'icono' => 'fas fa-cash-register',
                'url' => '/cajas',
                'orden' => 3,
                'activo' => true,
                'id_padre' => 15,
                'mostrar_en' => 'sidebar',
                'id_rol' => 2, // Solo cajero
            ],

            // 7. Reportes (Gerente)
            [
                'id_menu' => 19,
                'nombre' => 'Reportes',
                'icono' => 'fas fa-chart-bar',
                'url' => null,
                'orden' => 7,
                'activo' => true,
                'id_padre' => null,
                'mostrar_en' => 'sidebar',
                'id_rol' => 1, // Solo gerente
            ],
            [
                'id_menu' => 20,
                'nombre' => 'Ventas',
                'icono' => 'fas fa-chart-line',
                'url' => '/reportes/ventas',
                'orden' => 1,
                'activo' => true,
                'id_padre' => 19,
                'mostrar_en' => 'sidebar',
                'id_rol' => 1,
            ],
            [
                'id_menu' => 21,
                'nombre' => 'Inventario',
                'icono' => 'fas fa-warehouse',
                'url' => '/reportes/inventario',
                'orden' => 2,
                'activo' => true,
                'id_padre' => 19,
                'mostrar_en' => 'sidebar',
                'id_rol' => 1,
            ],
            [
                'id_menu' => 22,
                'nombre' => 'Bitácora',
                'icono' => 'fas fa-history',
                'url' => '/reportes/bitacora',
                'orden' => 3,
                'activo' => true,
                'id_padre' => 19,
                'mostrar_en' => 'sidebar',
                'id_rol' => 1,
            ],

            // 8. Configuración (Solo Gerente)
            [
                'id_menu' => 23,
                'nombre' => 'Configuración',
                'icono' => 'fas fa-cog',
                'url' => null,
                'orden' => 8,
                'activo' => true,
                'id_padre' => null,
                'mostrar_en' => 'sidebar',
                'id_rol' => 1, // Solo gerente
            ],
            [
                'id_menu' => 24,
                'nombre' => 'Usuarios',
                'icono' => 'fas fa-users',
                'url' => '/users',
                'orden' => 1,
                'activo' => true,
                'id_padre' => 23,
                'mostrar_en' => 'sidebar',
                'id_rol' => 1,
            ],
            [
                'id_menu' => 25,
                'nombre' => 'Roles',
                'icono' => 'fas fa-user-tag',
                'url' => '/roles',
                'orden' => 2,
                'activo' => true,
                'id_padre' => 23,
                'mostrar_en' => 'sidebar',
                'id_rol' => 1,
            ],
            [
                'id_menu' => 26,
                'nombre' => 'Menú de Navegación',
                'icono' => 'fas fa-bars',
                'url' => '/menus',
                'orden' => 3,
                'activo' => true,
                'id_padre' => 23,
                'mostrar_en' => 'sidebar',
                'id_rol' => 1,
            ],
        ];

        foreach ($menus as $menu) {
            DB::table('menu_navegacion')->insert([
                'id_menu' => $menu['id_menu'],
                'nombre' => $menu['nombre'],
                'icono' => $menu['icono'],
                'url' => $menu['url'],
                'orden' => $menu['orden'],
                'activo' => $menu['activo'],
                'id_padre' => $menu['id_padre'],
                'mostrar_en' => $menu['mostrar_en'],
                'id_rol' => $menu['id_rol'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
