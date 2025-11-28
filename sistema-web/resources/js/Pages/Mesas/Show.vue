<template>
    <AppLayout>
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2 class="text-primary mb-0">
                        <i class="bi bi-table me-2"></i>
                        Mesa #{{ mesa.numero_mesa }}
                    </h2>
                    <p class="text-muted">Detalles y gestión de la mesa</p>
                </div>
                <div class="col-md-4 text-end">
                    <Link :href="route('mesas.index')" class="btn btn-secondary me-2">
                        <i class="bi bi-arrow-left me-1"></i>
                        Volver
                    </Link>
                    <button @click="editarMesa" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>
                        Editar
                    </button>
                </div>
            </div>

            <div class="row">
                <!-- Información Principal -->
                <div class="col-xl-8">
                    <!-- Información de la Mesa -->
                    <div class="card mb-4" :class="getMesaCardClass(mesa.estado)">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Información de la Mesa
                            </h5>
                            <span :class="getBadgeClass(mesa.estado)" style="font-size: 1rem;">
                                {{ getEstadoTexto(mesa.estado) }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mesa-visual text-center mb-4">
                                        <i class="bi bi-table display-1 text-primary"></i>
                                        <h3 class="mt-2">Mesa #{{ mesa.numero_mesa }}</h3>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-muted fw-bold">Capacidad:</td>
                                            <td>
                                                <span class="badge bg-light text-dark fs-6">
                                                    {{ mesa.capacidad }} personas
                                                    <i class="bi bi-people ms-1"></i>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-bold">Ubicación:</td>
                                            <td>{{ mesa.ubicacion || 'No especificada' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-bold">Mesero Asignado:</td>
                                            <td>
                                                <span v-if="mesa.mesero" class="badge bg-info">
                                                    {{ mesa.mesero.name }}
                                                </span>
                                                <span v-else class="text-muted">Sin asignar</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-bold">Última Actualización:</td>
                                            <td>{{ formatFechaHora(mesa.updated_at) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Descripción si existe -->
                            <div v-if="mesa.descripcion" class="mt-3">
                                <h6 class="text-primary">Descripción</h6>
                                <p class="text-muted">{{ mesa.descripcion }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actividad Actual -->
                    <div class="card mb-4" v-if="mesa.pedido_activo || mesa.reserva_activa">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-activity me-2"></i>
                                Actividad Actual
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Pedido Activo -->
                            <div v-if="mesa.pedido_activo" class="alert alert-warning">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="alert-heading">
                                            <i class="bi bi-basket me-2"></i>
                                            Pedido Activo #{{ mesa.pedido_activo.id_pedido }}
                                        </h6>
                                        <p class="mb-1">
                                            <strong>Cliente:</strong> {{ mesa.pedido_activo.cliente?.nombre || 'N/A' }} {{ mesa.pedido_activo.cliente?.apellido || '' }}
                                        </p>
                                        <p class="mb-1">
                                            <strong>Estado:</strong> 
                                            <span class="badge bg-primary">{{ mesa.pedido_activo.estado }}</span>
                                        </p>
                                        <p class="mb-1">
                                            <strong>Total:</strong> S/{{ mesa.pedido_activo.total || '0.00' }}
                                        </p>
                                        <p class="mb-0">
                                            <small class="text-muted">
                                                Iniciado: {{ formatFechaHora(mesa.pedido_activo.created_at) }}
                                            </small>
                                        </p>
                                    </div>
                                    <div>
                                        <Link :href="route('pedidos.show', mesa.pedido_activo.id_pedido)" 
                                              class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i>
                                            Ver Pedido
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            <!-- Reserva Activa -->
                            <div v-if="mesa.reserva_activa" class="alert alert-info">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="alert-heading">
                                            <i class="bi bi-bookmark me-2"></i>
                                            Reserva Activa #{{ mesa.reserva_activa.id_reserva }}
                                        </h6>
                                        <p class="mb-1">
                                            <strong>Cliente:</strong> {{ mesa.reserva_activa.cliente?.nombre || 'N/A' }} {{ mesa.reserva_activa.cliente?.apellido || '' }}
                                        </p>
                                        <p class="mb-1">
                                            <strong>Fecha:</strong> {{ formatFecha(mesa.reserva_activa.fecha_reserva) }}
                                        </p>
                                        <p class="mb-1">
                                            <strong>Horario:</strong> 
                                            {{ mesa.reserva_activa.hora_inicio }} - {{ mesa.reserva_activa.hora_fin }}
                                        </p>
                                        <p class="mb-0">
                                            <strong>Personas:</strong> {{ mesa.reserva_activa.numero_personas }}
                                        </p>
                                    </div>
                                    <div>
                                        <Link :href="route('reservas.show', mesa.reserva_activa.id_reserva)" 
                                              class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye me-1"></i>
                                            Ver Reserva
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historial Reciente -->
                    <div class="card mb-4" v-if="historialReciente && historialReciente.length > 0">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-clock-history me-2"></i>
                                Historial Reciente
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div v-for="(evento, index) in historialReciente" 
                                     :key="index" 
                                     class="timeline-item">
                                    <div class="timeline-marker" :class="getEventoMarkerClass(evento.tipo)">
                                        <i :class="getEventoIcon(evento.tipo)"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong>{{ evento.descripcion }}</strong>
                                                <div class="text-muted mt-1" v-if="evento.detalles">
                                                    {{ evento.detalles }}
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                {{ formatFechaHora(evento.fecha) }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-xl-4">
                    <!-- Acciones Rápidas -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Acciones Rápidas</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <!-- Estado Disponible -->
                                <div v-if="mesa.estado === 'disponible'">
                                    <button @click="ocuparMesa" class="btn btn-warning">
                                        <i class="bi bi-person-plus me-1"></i>
                                        Ocupar Mesa
                                    </button>
                                    <Link :href="route('pedidos.create', { mesa: mesa.id_mesa })" 
                                          class="btn btn-outline-primary">
                                        <i class="bi bi-basket me-1"></i>
                                        Crear Pedido
                                    </Link>
                                    <Link :href="route('reservas.create', { mesa: mesa.id_mesa })" 
                                          class="btn btn-outline-info">
                                        <i class="bi bi-bookmark me-1"></i>
                                        Crear Reserva
                                    </Link>
                                </div>

                                <!-- Estado Ocupada -->
                                <div v-if="mesa.estado === 'ocupada'">
                                    <button @click="liberarMesa" class="btn btn-success">
                                        <i class="bi bi-person-dash me-1"></i>
                                        Liberar Mesa
                                    </button>
                                    <Link v-if="mesa.pedido_activo" 
                                          :href="route('ventas-modernas.create', { mesa: mesa.id_mesa, pedido: mesa.pedido_activo.id_pedido })" 
                                          class="btn btn-outline-success">
                                        <i class="bi bi-credit-card me-1"></i>
                                        Procesar Pago
                                    </Link>
                                </div>

                                <!-- Estado Reservada -->
                                <div v-if="mesa.estado === 'reservada'">
                                    <button @click="confirmarLlegada" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Confirmar Llegada
                                    </button>
                                    <button @click="cancelarReserva" class="btn btn-outline-danger">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Cancelar Reserva
                                    </button>
                                </div>

                                <!-- Estado Mantenimiento -->
                                <div v-if="mesa.estado === 'mantenimiento'">
                                    <button @click="finalizarMantenimiento" class="btn btn-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Finalizar Mantenimiento
                                    </button>
                                </div>

                                <!-- Acciones Comunes -->
                                <div class="mt-3 pt-3 border-top">
                                    <button @click="asignarMesero" 
                                            class="btn btn-outline-info"
                                            :disabled="mesa.estado === 'mantenimiento'">
                                        <i class="bi bi-person-badge me-1"></i>
                                        {{ mesa.mesero ? 'Cambiar' : 'Asignar' }} Mesero
                                    </button>
                                    <button @click="ponerMantenimiento" 
                                            class="btn btn-outline-secondary"
                                            v-if="mesa.estado !== 'mantenimiento'"
                                            :disabled="mesa.estado === 'ocupada'">
                                        <i class="bi bi-tools me-1"></i>
                                        Mantenimiento
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="card mb-4" v-if="estadisticas">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Estadísticas</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="stat-box">
                                        <h4 class="text-primary mb-0">{{ estadisticas.pedidos_hoy || 0 }}</h4>
                                        <small class="text-muted">Pedidos Hoy</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="stat-box">
                                        <h4 class="text-success mb-0">S/{{ estadisticas.ingresos_hoy || '0.00' }}</h4>
                                        <small class="text-muted">Ingresos Hoy</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-box">
                                        <h4 class="text-info mb-0">{{ estadisticas.tiempo_promedio || '0h' }}</h4>
                                        <small class="text-muted">Tiempo Promedio</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-box">
                                        <h4 class="text-warning mb-0">{{ estadisticas.rotacion_diaria || 0 }}</h4>
                                        <small class="text-muted">Rotaciones Hoy</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Registro -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Información de Registro</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="text-muted">Creada:</td>
                                    <td>{{ formatFechaHora(mesa.created_at) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Modificada:</td>
                                    <td>{{ formatFechaHora(mesa.updated_at) }}</td>
                                </tr>
                                <tr v-if="mesa.ultima_ocupacion">
                                    <td class="text-muted">Última Ocupación:</td>
                                    <td>{{ formatFechaHora(mesa.ultima_ocupacion) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Props
const props = defineProps({
    mesa: Object,
    historialReciente: Array,
    estadisticas: Object
})

// Métodos
function editarMesa() {
    router.visit(route('mesas.edit', props.mesa.id_mesa))
}

function ocuparMesa() {
    if (confirm(`¿Ocupar la Mesa #${props.mesa.numero_mesa}?`)) {
        router.post(route('mesas.ocupar', props.mesa.id_mesa))
    }
}

function liberarMesa() {
    if (confirm(`¿Liberar la Mesa #${props.mesa.numero_mesa}?`)) {
        router.post(route('mesas.liberar', props.mesa.id_mesa))
    }
}

function confirmarLlegada() {
    if (confirm('¿Confirmar llegada del cliente y ocupar mesa?')) {
        router.post(route('mesas.ocupar', props.mesa.id_mesa))
    }
}

function cancelarReserva() {
    if (props.mesa.reserva_activa) {
        if (confirm('¿Cancelar la reserva activa?')) {
            router.post(route('reservas.cambiar-estado', props.mesa.reserva_activa.id_reserva), {
                estado: 'cancelada'
            })
        }
    }
}

function finalizarMantenimiento() {
    if (confirm('¿Finalizar mantenimiento y habilitar mesa?')) {
        router.post(route('mesas.cambiar-estado', props.mesa.id_mesa), {
            estado: 'disponible'
        })
    }
}

function asignarMesero() {
    // Aquí podrías abrir un modal o redirigir a la página de edición
    router.visit(route('mesas.edit', props.mesa.id_mesa) + '?tab=mesero')
}

function ponerMantenimiento() {
    if (confirm('¿Poner mesa en mantenimiento?')) {
        router.post(route('mesas.cambiar-estado', props.mesa.id_mesa), {
            estado: 'mantenimiento'
        })
    }
}

function getMesaCardClass(estado) {
    const classes = {
        'disponible': 'border-success',
        'ocupada': 'border-danger',
        'reservada': 'border-warning',
        'mantenimiento': 'border-secondary'
    }
    return classes[estado] || 'border-secondary'
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

function getEventoMarkerClass(tipo) {
    const classes = {
        'ocupar': 'warning',
        'liberar': 'success',
        'reserva': 'info',
        'mantenimiento': 'secondary',
        'pedido': 'primary'
    }
    return `timeline-marker-${classes[tipo] || 'secondary'}`
}

function getEventoIcon(tipo) {
    const iconos = {
        'ocupar': 'bi bi-person-plus',
        'liberar': 'bi bi-person-dash',
        'reserva': 'bi bi-bookmark',
        'mantenimiento': 'bi bi-tools',
        'pedido': 'bi bi-basket'
    }
    return iconos[tipo] || 'bi bi-circle'
}

function formatFecha(fecha) {
    return new Date(fecha).toLocaleDateString('es-PE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    })
}

function formatFechaHora(fecha) {
    return new Date(fecha).toLocaleString('es-PE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>

<style scoped>
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
}

.timeline-marker-warning { background-color: #ffc107; color: #000; }
.timeline-marker-success { background-color: #198754; }
.timeline-marker-danger { background-color: #dc3545; }
.timeline-marker-primary { background-color: #0d6efd; }
.timeline-marker-secondary { background-color: #6c757d; }
.timeline-marker-info { background-color: #0dcaf0; color: #000; }

.timeline-content {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.375rem;
    border: 1px solid #dee2e6;
}

.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
    border: none;
    border-width: 2px;
}

.table-borderless td {
    padding: 0.25rem 0;
}

.stat-box {
    padding: 0.5rem;
    border-radius: 0.375rem;
    background: #f8f9fa;
}

.mesa-visual {
    background: #f8f9fa;
    border-radius: 0.375rem;
    padding: 2rem;
}
</style>