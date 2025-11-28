<template>
    <AppLayout>
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2 class="text-primary mb-0">
                        <i class="bi bi-table me-2"></i>
                        Gestión de Mesas
                    </h2>
                    <p class="text-muted">Administra las mesas del restaurante</p>
                </div>
                <div class="col-md-4 text-end">
                    <Link :href="route('mesas.create')" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Nueva Mesa
                    </Link>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-success bg-gradient text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">Disponibles</h6>
                                    <h3 class="mb-0">{{ estadisticas.disponibles || 0 }}</h3>
                                </div>
                                <i class="bi bi-check-circle fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger bg-gradient text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">Ocupadas</h6>
                                    <h3 class="mb-0">{{ estadisticas.ocupadas || 0 }}</h3>
                                </div>
                                <i class="bi bi-people fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning bg-gradient text-dark">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">Reservadas</h6>
                                    <h3 class="mb-0">{{ estadisticas.reservadas || 0 }}</h3>
                                </div>
                                <i class="bi bi-bookmark fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-secondary bg-gradient text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">Mantenimiento</h6>
                                    <h3 class="mb-0">{{ estadisticas.mantenimiento || 0 }}</h3>
                                </div>
                                <i class="bi bi-tools fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
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
                                <option value="disponible">Disponible</option>
                                <option value="ocupada">Ocupada</option>
                                <option value="reservada">Reservada</option>
                                <option value="mantenimiento">Mantenimiento</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Ubicación</label>
                            <select v-model="filtros.ubicacion" class="form-select" @change="aplicarFiltros">
                                <option value="">Todas las ubicaciones</option>
                                <option value="Salón principal">Salón principal</option>
                                <option value="Terraza">Terraza</option>
                                <option value="VIP">Área VIP</option>
                                <option value="Bar">Bar</option>
                                <option value="Jardín">Jardín</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Mesero Asignado</label>
                            <select v-model="filtros.mesero_id" class="form-select" @change="aplicarFiltros">
                                <option value="">Todos los meseros</option>
                                <option v-for="mesero in meseros" :key="mesero.id" :value="mesero.id">
                                    {{ mesero.name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button @click="limpiarFiltros" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-x-circle me-1"></i>
                                Limpiar
                            </button>
                            <button @click="refreshMesas" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vista de Mesas -->
            <div class="row mb-3">
                <div class="col">
                    <div class="btn-group" role="group">
                        <button @click="vistaActual = 'grid'" 
                                class="btn" 
                                :class="vistaActual === 'grid' ? 'btn-primary' : 'btn-outline-primary'">
                            <i class="bi bi-grid me-1"></i>
                            Vista Cuadrícula
                        </button>
                        <button @click="vistaActual = 'list'" 
                                class="btn" 
                                :class="vistaActual === 'list' ? 'btn-primary' : 'btn-outline-primary'">
                            <i class="bi bi-list me-1"></i>
                            Vista Lista
                        </button>
                    </div>
                </div>
            </div>

            <!-- Vista Cuadrícula -->
            <div v-if="vistaActual === 'grid'" class="row">
                <div v-for="mesa in mesasFiltradas" :key="mesa.id_mesa" class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card mesa-card" :class="getMesaCardClass(mesa.estado)">
                        <div class="card-body position-relative">
                            <!-- Badge de Estado -->
                            <span :class="getBadgeClass(mesa.estado)" class="position-absolute top-0 end-0 m-2">
                                {{ getEstadoTexto(mesa.estado) }}
                            </span>

                            <!-- Número de Mesa -->
                            <div class="text-center mb-3">
                                <div class="mesa-numero">
                                    <i class="bi bi-table fs-1 mb-2"></i>
                                    <h4 class="mb-0">Mesa #{{ mesa.numero_mesa }}</h4>
                                </div>
                            </div>

                            <!-- Información -->
                            <div class="mesa-info">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Capacidad:</span>
                                    <span><strong>{{ mesa.capacidad }}</strong> personas</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Ubicación:</span>
                                    <span>{{ mesa.ubicacion || 'No especificada' }}</span>
                                </div>
                                <div v-if="mesa.mesero" class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Mesero:</span>
                                    <span>{{ mesa.mesero.name }}</span>
                                </div>
                            </div>

                            <!-- Pedido/Reserva Activa -->
                            <div v-if="mesa.pedido_activo || mesa.reserva_activa" class="mt-3 p-2 bg-light rounded">
                                <div v-if="mesa.pedido_activo" class="small">
                                    <strong>Pedido #{{ mesa.pedido_activo.id_pedido }}</strong>
                                    <br>
                                    Cliente: {{ mesa.pedido_activo.cliente?.nombre || 'N/A' }}
                                    <br>
                                    Total: S/{{ mesa.pedido_activo.total || 0 }}
                                </div>
                                <div v-if="mesa.reserva_activa" class="small">
                                    <strong>Reserva #{{ mesa.reserva_activa.id_reserva }}</strong>
                                    <br>
                                    Cliente: {{ mesa.reserva_activa.cliente?.nombre || 'N/A' }}
                                    <br>
                                    {{ mesa.reserva_activa.hora_inicio }} - {{ mesa.reserva_activa.hora_fin }}
                                </div>
                            </div>

                            <!-- Acciones solo para cliente: solo reservar -->
                            <div class="mesa-actions mt-3">
                                <button v-if="mesa.estado === 'disponible'" @click="cambiarEstado(mesa, 'reservada')" class="btn btn-warning w-100">
                                    <i class="bi bi-bookmark"></i> Reservar
                                </button>
                                <span v-else class="text-muted small">Solo puedes reservar mesas disponibles</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vista Lista -->
            <div v-if="vistaActual === 'list'" class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Mesa</th>
                                    <th>Capacidad</th>
                                    <th>Ubicación</th>
                                    <th>Estado</th>
                                    <th>Mesero</th>
                                    <th>Actividad</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="mesa in mesasFiltradas" :key="mesa.id_mesa">
                                    <td>
                                        <strong>Mesa #{{ mesa.numero_mesa }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ mesa.capacidad }} <i class="bi bi-people ms-1"></i>
                                        </span>
                                    </td>
                                    <td>{{ mesa.ubicacion || 'No especificada' }}</td>
                                    <td>
                                        <span :class="getBadgeClass(mesa.estado)">
                                            {{ getEstadoTexto(mesa.estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ mesa.mesero?.name || 'Sin asignar' }}
                                    </td>
                                    <td>
                                        <div v-if="mesa.pedido_activo" class="small">
                                            <strong>Pedido #{{ mesa.pedido_activo.id_pedido }}</strong><br>
                                            {{ mesa.pedido_activo.cliente?.nombre || 'N/A' }}
                                        </div>
                                        <div v-else-if="mesa.reserva_activa" class="small">
                                            <strong>Reserva #{{ mesa.reserva_activa.id_reserva }}</strong><br>
                                            {{ mesa.reserva_activa.cliente?.nombre || 'N/A' }}
                                        </div>
                                        <span v-else class="text-muted">Sin actividad</span>
                                    </td>
                                    <td>
                                        <button v-if="mesa.estado === 'disponible'" @click="cambiarEstado(mesa, 'reservada')" class="btn btn-warning btn-sm w-100">
                                            <i class="bi bi-bookmark"></i> Reservar
                                        </button>
                                        <span v-else class="text-muted small">Solo puedes reservar mesas disponibles</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Props
const props = defineProps({
    mesas: Array,
    meseros: Array,
    estadisticas: Object,
    filtros: Object
})

// Estado reactivo
const vistaActual = ref('grid')
const filtros = ref({
    estado: props.filtros?.estado || '',
    ubicacion: props.filtros?.ubicacion || '',
    mesero_id: props.filtros?.mesero_id || ''
})

// Computed - Filtrar mesas localmente
const mesasFiltradas = computed(() => {
    if (!props.mesas) return []
    return props.mesas
})

// Métodos
function aplicarFiltros() {
    router.get('/mesas', filtros.value, {
        preserveState: true,
        preserveScroll: true
    })
}

function limpiarFiltros() {
    filtros.value = { estado: '', ubicacion: '', mesero_id: '' }
    aplicarFiltros()
}

function refreshMesas() {
    router.reload()
}

function editarMesa(mesa) {
    router.visit(`/mesas/${mesa.id_mesa}/edit`)
}

function cambiarEstado(mesa, nuevoEstado) {
    const textoEstado = getEstadoTexto(nuevoEstado)
    const confirmacion = confirm(`¿Cambiar estado de la Mesa #${mesa.numero_mesa} a "${textoEstado}"?`)

    if (confirmacion) {
        router.post(route('mesas.cambiar-estado', mesa.id_mesa), {
            estado: nuevoEstado
        }, {
            preserveScroll: true,
            onSuccess: () => {
                // Actualizar automáticamente
            },
            onError: (errors) => {
                console.error('Error al cambiar estado:', errors)
                alert('Error al cambiar el estado de la mesa')
            }
        })
    }
}

function asignarMesero(mesa) {
    // Aquí podrías abrir un modal para seleccionar mesero
    router.visit(route('mesas.edit', mesa.id_mesa) + '?tab=mesero')
}

function getMesaCardClass(estado) {
    const classes = {
        'disponible': 'border-success',
        'ocupada': 'border-danger',
        'reservada': 'border-warning',
        'mantenimiento': 'border-secondary'
    }
    return `mesa-card-${estado} ${classes[estado] || 'border-secondary'}`
}

function getBadgeClass(estado) {
    const classes = {
        'disponible': 'badge bg-success',
        'ocupada': 'badge bg-danger',
        'reservada': 'badge bg-warning text-dark',
        'mantenimiento': 'badge bg-secondary'
    }
    return classes[estado] || 'badge bg-secondary'
}

function getEstadoTexto(estado) {
    const textos = {
        'disponible': 'Disponible',
        'ocupada': 'Ocupada',
        'reservada': 'Reservada',
        'mantenimiento': 'Mantenimiento'
    }
    return textos[estado] || estado
}
</script>

<style scoped>
.mesa-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border-width: 2px;
}

.mesa-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.15);
}

.mesa-card-disponible:hover {
    border-color: #198754 !important;
}

.mesa-card-ocupada:hover {
    border-color: #dc3545 !important;
}

.mesa-card-reservada:hover {
    border-color: #ffc107 !important;
}

.mesa-card-mantenimiento:hover {
    border-color: #6c757d !important;
}

.mesa-numero {
    color: #495057;
}

.mesa-info {
    font-size: 0.9rem;
}

.mesa-actions .btn {
    border-radius: 0;
}

.mesa-actions .btn:first-child {
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
}

.mesa-actions .btn:last-child {
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}

.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
    border: none;
}

.bg-gradient {
    background: linear-gradient(45deg, var(--bs-bg-opacity, 1), rgba(255,255,255,0.15)) !important;
}

.table th {
    border-top: none;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}
</style>