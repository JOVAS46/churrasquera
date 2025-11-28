export default {
    template: `
        <div class="min-h-screen bg-gray-100">
            <!-- Navigation -->
            <nav class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="shrink-0 flex items-center">
                                <a href="/home" class="text-xl font-bold text-gray-800">
                                    Churrascuteria Roberto
                                </a>
                            </div>
                        </div>
                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <div class="ml-3 relative" v-if="$page.props.auth.user">
                                <span class="text-sm text-gray-700">{{ $page.props.auth.user.nombre }} {{ $page.props.auth.user.apellido }}</span>
                                <span v-if="$page.props.auth.user.rol" class="ml-2 text-xs text-gray-500">({{ $page.props.auth.user.rol.nombre }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Header -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h2 class="font-semibold text-xl text-gray-800">Dashboard</h2>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <h3 class="text-lg font-semibold mb-4">Bienvenido a Churrascuteria Roberto</h3>
                                
                                <div v-if="$page.props.auth.user" class="mt-4">
                                    <p class="text-gray-700">
                                        Usuario: <strong>{{ $page.props.auth.user.nombre }} {{ $page.props.auth.user.apellido }}</strong>
                                    </p>
                                    <p class="text-gray-700" v-if="$page.props.auth.user.rol">
                                        Rol: <strong>{{ $page.props.auth.user.rol.nombre }}</strong>
                                    </p>
                                    <p class="text-gray-700">
                                        Email: <strong>{{ $page.props.auth.user.email }}</strong>
                                    </p>
                                </div>

                                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-blue-50 p-4 rounded-lg">
                                        <h4 class="font-semibold text-blue-900">Productos</h4>
                                        <p class="text-2xl font-bold text-blue-600">{{ stats.productos || 0 }}</p>
                                    </div>
                                    <div class="bg-green-50 p-4 rounded-lg">
                                        <h4 class="font-semibold text-green-900">Categorías</h4>
                                        <p class="text-2xl font-bold text-green-600">{{ stats.categorias || 0 }}</p>
                                    </div>
                                    <div class="bg-purple-50 p-4 rounded-lg">
                                        <h4 class="font-semibold text-purple-900">Mesas</h4>
                                        <p class="text-2xl font-bold text-purple-600">{{ stats.mesas || 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    `,
    props: {
        stats: {
            type: Object,
            default: () => ({})
        }
    }
};
