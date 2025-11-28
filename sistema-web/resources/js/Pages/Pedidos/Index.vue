<template>
    <AppLayout>
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2 class="text-primary mb-0">
                        <i class="bi bi-receipt me-2"></i>
                        Gestión de Pedidos
                    </h2>
                    <p class="text-muted">Administra todos los pedidos del restaurante</p>
                </div>
                <div class="col-md-4 text-end">
                    <Link :href="route('pedidos.create')" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Nuevo Pedido
                    </Link>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select v-model="filtros.estado" class="form-select" @change="aplicarFiltros">
                                <option value="">Todos los estados</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="en_preparacion">En Preparación</option>
                                <option value="listo">Listo</option>
                                <option value="entregado">Entregado</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha</label>
                            <input v-model="filtros.fecha" type="date" class="form-control" @change="aplicarFiltros">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Mesa</label>
                            <select v-model="filtros.mesa_id" class="form-select" @change="aplicarFiltros">
                                <option value="">Todas las mesas</option>
                                <option v-for="mesa in mesas" :key="mesa.id_mesa" :value="mesa.id_mesa">
                                    Mesa #{{ mesa.numero_mesa }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button @click="limpiarFiltros" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>
                                Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas rápidas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-warning bg-gradient text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">Pendientes</h6>
                                    <h3 class="mb-0">{{ estadisticas.pendientes || 0 }}</h3>
                                </div>
                                <i class="bi bi-clock fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info bg-gradient text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">En Preparación</h6>
                                    <h3 class="mb-0">{{ estadisticas.en_preparacion || 0 }}</h3>
                                </div>
                                <i class="bi bi-fire fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success bg-gradient text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">Listos</h6>
                                    <h3 class="mb-0">{{ estadisticas.listo || 0 }}</h3>
                                </div>
                                <i class="bi bi-check-circle fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-primary bg-gradient text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">Hoy</h6>
                                    <h3 class="mb-0">{{ estadisticas.hoy || 0 }}</h3>
                                </div>
                                <i class="bi bi-calendar-day fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de pedidos -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Mesa</th>
                                    <th>Cliente</th>
                                    <th>Mesero</th>
                                    <th>Estado</th>
                                    <th>Total</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="pedido in pedidos.data" :key="pedido.id_pedido">
                                    <td>
                                        <strong>#{{ pedido.id_pedido }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            Mesa #{{ pedido.mesa?.numero_mesa }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ pedido.cliente?.nombre || 'Sin cliente' }}
                                        {{ pedido.cliente?.apellido || '' }}
                                    </td>
                                    <td>
                                        {{ pedido.mesero?.nombre }}
                                        {{ pedido.mesero?.apellido }}
                                    </td>
                                    <td>
                                        <span :class="getBadgeClass(pedido.estado)">
                                            {{ getEstadoTexto(pedido.estado) }}
                                        </span>
                                    </td>
                                    <td class="fw-bold">
                                        S/ {{ pedido.total }}
                                    </td>
                                    <td>
                                        {{ formatFecha(pedido.fecha_pedido) }}
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <Link :href="route('pedidos.show', pedido.id_pedido)" 
                                                  class="btn btn-outline-info">
                                                <i class="bi bi-eye"></i>
                                            </Link>
                                            <button v-if="pedido.estado === 'pendiente'"
                                                    @click="editarPedido(pedido)"
                                                    class="btn btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-outline-secondary dropdown-toggle" 
                                                        type="button" 
                                                        data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li v-if="pedido.estado === 'pendiente'">
                                                        <a @click="cambiarEstado(pedido, 'en_preparacion')" 
                                                           class="dropdown-item" href="#">
                                                            <i class="bi bi-fire text-info"></i>
                                                            Iniciar Preparación
                                                        </a>
                                                    </li>
                                                    <li v-if="pedido.estado === 'en_preparacion'">
                                                        <a @click="cambiarEstado(pedido, 'listo')" 
                                                           class="dropdown-item" href="#">
                                                            <i class="bi bi-check-circle text-success"></i>
                                                            Marcar Listo
                                                        </a>
                                                    </li>
                                                    <li v-if="pedido.estado === 'listo'">
                                                        <a @click="cambiarEstado(pedido, 'entregado')" 
                                                           class="dropdown-item" href="#">
                                                            <i class="bi bi-box-arrow-right text-primary"></i>
                                                            Entregar
                                                        </a>
                                                    </li>
                                                    <li v-if="['pendiente', 'en_preparacion'].includes(pedido.estado)">
                                                        <hr class="dropdown-divider">
                                                        <a @click="cancelarPedido(pedido)" 
                                                           class="dropdown-item text-danger" href="#">
                                                            <i class="bi bi-x-circle"></i>
                                                            Cancelar
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <nav v-if="pedidos.links.length > 3" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li v-for="link in pedidos.links" :key="link.label"
                                class="page-item" :class="{ active: link.active, disabled: !link.url }">
                                <Link v-if="link.url" :href="link.url" class="page-link" v-html="link.label"></Link>
                                <span v-else class="page-link" v-html="link.label"></span>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Props
const props = defineProps({
    pedidos: Object,
    mesas: Array,
    filtros: Object
})

// Estado reactivo
const filtros = ref({
    estado: props.filtros.estado || '',
    fecha: props.filtros.fecha || '',
    mesa_id: props.filtros.mesa_id || ''
})

// Estadísticas computadas
const estadisticas = computed(() => {
    const stats = {
        pendientes: 0,
        en_preparacion: 0,
        listo: 0,
        hoy: 0
    }
    
    props.pedidos.data.forEach(pedido => {
        if (pedido.estado === 'pendiente') stats.pendientes++
        if (pedido.estado === 'en_preparacion') stats.en_preparacion++
        if (pedido.estado === 'listo') stats.listo++
        
        const hoy = new Date().toISOString().split('T')[0]
        const fechaPedido = pedido.fecha_pedido.split('T')[0]
        if (fechaPedido === hoy) stats.hoy++
    })
    
    return stats
})

// Métodos
function aplicarFiltros() {
    router.get(route('pedidos.index'), filtros.value, {
        preserveState: true,
        preserveScroll: true
    })
}

function limpiarFiltros() {
    filtros.value = { estado: '', fecha: '', mesa_id: '' }
    aplicarFiltros()
}

function editarPedido(pedido) {
    router.visit(route('pedidos.edit', pedido.id_pedido))
}

function cambiarEstado(pedido, nuevoEstado) {
    if (confirm(`¿Cambiar estado del pedido #${pedido.id_pedido} a "${getEstadoTexto(nuevoEstado)}"?`)) {
        router.post(route('pedidos.cambiar-estado', pedido.id_pedido), {
            estado: nuevoEstado
        }, {
            onSuccess: () => {
                // La página se recargará automáticamente
            }
        })
    }
}

function cancelarPedido(pedido) {
    if (confirm(`¿Está seguro de cancelar el pedido #${pedido.id_pedido}? Esta acción no se puede deshacer.`)) {
        cambiarEstado(pedido, 'cancelado')
    }
}

function getBadgeClass(estado) {
    const classes = {
        'pendiente': 'badge bg-warning text-dark',
        'en_preparacion': 'badge bg-info text-white',
        'listo': 'badge bg-success',
        'entregado': 'badge bg-primary',
        'cancelado': 'badge bg-danger'
    }
    return classes[estado] || 'badge bg-secondary'
}

function getEstadoTexto(estado) {
    const textos = {
        'pendiente': 'Pendiente',
        'en_preparacion': 'En Preparación',
        'listo': 'Listo',
        'entregado': 'Entregado',
        'cancelado': 'Cancelado'
    }
    return textos[estado] || estado
}

function formatFecha(fecha) {
    return new Date(fecha).toLocaleDateString('es-PE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>

<style scoped>
.table th {
    border-top: none;
}

.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
    border: none;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.badge {
    font-size: 0.75em;
}

.bg-gradient {
    background: linear-gradient(45deg, var(--bs-bg-opacity, 1), rgba(255,255,255,0.15)) !important;
}
</style>