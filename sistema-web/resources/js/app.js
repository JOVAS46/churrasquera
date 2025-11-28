import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

// Helper simple para route() - genera URLs de Laravel
window.route = function(name, params = {}) {
    const routes = {
        // Rutas de pedidos
        'pedidos.index': '/pedidos',
        'pedidos.create': '/pedidos/create',
        'pedidos.store': '/pedidos',
        'pedidos.show': (id) => `/pedidos/${id}`,
        'pedidos.edit': (id) => `/pedidos/${id}/edit`,
        'pedidos.update': (id) => `/pedidos/${id}`,
        'pedidos.destroy': (id) => `/pedidos/${id}`,
        'pedidos.cambiar-estado': (id) => `/pedidos/${id}/estado`,
        'api.pedidos.estado': (estado) => `/api/pedidos/estado/${estado}`,

        // Rutas de mesas
        'mesas.index': '/mesas',
        'mesas.create': '/mesas/create',
        'mesas.store': '/mesas',
        'mesas.show': (id) => `/mesas/${id}`,
        'mesas.edit': (id) => `/mesas/${id}/edit`,
        'mesas.update': (id) => `/mesas/${id}`,
        'mesas.destroy': (id) => `/mesas/${id}`,
        'mesas.cambiar-estado': (id) => `/mesas/${id}/cambiar-estado`,
        'mesas.asignar-mesero': (id) => `/mesas/${id}/asignar-mesero`,
        'mesas.ocupar': (id) => `/mesas/${id}/ocupar`,
        'mesas.liberar': (id) => `/mesas/${id}/liberar`,
        'mesas.mis-mesas': '/mis-mesas',

        // Rutas de reservas
        'reservas.index': '/reservas',
        'reservas.create': '/reservas/create',
        'reservas.store': '/reservas',
        'reservas.show': (id) => `/reservas/${id}`,
        'reservas.edit': (id) => `/reservas/${id}/edit`,
        'reservas.update': (id) => `/reservas/${id}`,
        'reservas.destroy': (id) => `/reservas/${id}`,
        'reservas.cambiar-estado': (id) => `/reservas/${id}/estado`,

        // Rutas generales
        'home': '/home',
        'login': '/login',
    };

    const route = routes[name];
    if (!route) {
        console.warn(`⚠️ Route "${name}" no encontrada, usando fallback`);
        return `/${name.replace(/\./g, '/')}`;
    }
    if (typeof route === 'function') return route(params);
    return route;
};

createInertiaApp({
    resolve: name => {
        // Usar require.context para cargar dinámicamente las páginas
        const pages = require.context('./Pages', true, /\.vue$/i);

        // Intentar diferentes rutas posibles
        const possiblePaths = [
            `./${name}.vue`,
            `./${name}/Index.vue`,
        ];

        for (const path of possiblePaths) {
            try {
                return pages(path).default || pages(path);
            } catch (e) {
                // Continuar con la siguiente ruta
                continue;
            }
        }

        // Si no se encuentra, lanzar error descriptivo
        throw new Error(`Página no encontrada: ${name}. Rutas buscadas: ${possiblePaths.join(', ')}`);
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.config.globalProperties.route = window.route;
        app.use(plugin).mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});