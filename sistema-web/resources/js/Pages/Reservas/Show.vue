<template>
    <AppLayout>
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2 class="text-primary mb-0">
                        <i class="bi bi-calendar-check me-2"></i>
                        Reserva #{{ reserva.id_reserva }}
                    </h2>
                    <p class="text-muted">Detalles de la reserva</p>
                </div>
                <div class="col-md-4 text-end">
                    <Link :href="route('reservas.index')" class="btn btn-secondary me-2">
                        <i class="bi bi-arrow-left me-1"></i>
                        Volver
                    </Link>
                    <button v-if="reserva.estado === 'pendiente'"
                            @click="editarReserva"
                            class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>
                        Editar
                    </button>
                </div>
            </div>

            <div class="row">
                <!-- Información Principal -->
                <div class="col-xl-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Información de la Reserva
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary">
                                        <i class="bi bi-person me-1"></i>
                                        Información del Cliente
                                    </h6>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td class="text-muted fw-bold">Nombre:</td>
                                            <td>{{ reserva.cliente?.nombre || 'Sin cliente' }} {{ reserva.cliente?.apellido || '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-bold">Email:</td>
                                            <td>{{ reserva.cliente?.email || 'No disponible' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-bold">Teléfono:</td>
                                            <td>{{ reserva.cliente?.telefono || 'No disponible' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary">
                                        <i class="bi bi-calendar me-1"></i>
                                        Detalles de la Reserva
                                    </h6>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td class="text-muted fw-bold">Fecha:</td>
                                            <td>{{ formatFecha(reserva.fecha_reserva) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-bold">Horario:</td>
                                            <td>
                                                <strong>{{ reserva.hora_inicio }}</strong> - {{ reserva.hora_fin }}
                                                <br>
                                                <small class="text-muted">Duración: {{ calcularDuracion(reserva.hora_inicio, reserva.hora_fin) }}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-bold">Personas:</td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ reserva.numero_personas }} personas
                                                    <i class="bi bi-people ms-1"></i>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Mesa Asignada -->
                            <hr class="my-4">
                            <h6 class="text-primary">
                                <i class="bi bi-table me-1"></i>
                                Mesa Asignada
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h5 class="card-title">Mesa #{{ reserva.mesa?.numero_mesa }}</h5>
                                            <p class="card-text">
                                                <i class="bi bi-people me-1"></i>
                                                Capacidad: {{ reserva.mesa?.capacidad }} personas
                                                <br>
                                                <i class="bi bi-geo-alt me-1"></i>
                                                Ubicación: {{ reserva.mesa?.ubicacion || 'Salón principal' }}
                                            </p>
                                            <span :class="getMesaBadgeClass(reserva.mesa?.estado)">
                                                {{ getMesaEstadoTexto(reserva.mesa?.estado) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" v-if="reserva.observaciones">
                                    <h6 class="text-primary">
                                        <i class="bi bi-chat-dots me-1"></i>
                                        Observaciones
                                    </h6>
                                    <div class="alert alert-info">
                                        {{ reserva.observaciones }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historial de Estado -->
                    <div class="card mb-4" v-if="historialEstado && historialEstado.length > 0">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-clock-history me-2"></i>
                                Historial de Estados
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div v-for="(evento, index) in historialEstado" 
                                     :key="index" 
                                     class="timeline-item">
                                    <div class="timeline-marker" :class="getTimelineMarkerClass(evento.estado)">
                                        <i :class="getEstadoIcon(evento.estado)"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span :class="getBadgeClass(evento.estado)">
                                                {{ getEstadoTexto(evento.estado) }}
                                            </span>
                                            <small class="text-muted">
                                                {{ formatFechaHora(evento.fecha_cambio) }}
                                            </small>
                                        </div>
                                        <div class="text-muted mt-1" v-if="evento.usuario">
                                            Por: {{ evento.usuario }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-xl-4">
                    <!-- Estado Actual -->
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <h5 class="card-title">Estado Actual</h5>
                            <div class="my-3">
                                <span :class="getBadgeClass(reserva.estado)" style="font-size: 1.2rem;">
                                    {{ getEstadoTexto(reserva.estado) }}
                                </span>
                            </div>
                            <small class="text-muted">
                                Última actualización: {{ formatFechaHora(reserva.updated_at) }}
                            </small>
                        </div>
                    </div>

                    <!-- Acciones Rápidas -->
                    <div class="card mb-4" v-if="reserva.estado !== 'cancelada'">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Acciones Rápidas</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button v-if="reserva.estado === 'pendiente'"
                                        @click="cambiarEstado('confirmada')"
                                        class="btn btn-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Confirmar Reserva
                                </button>

                                <button v-if="reserva.estado === 'confirmada'"
                                        @click="cambiarEstado('completada')"
                                        class="btn btn-primary">
                                    <i class="bi bi-check2-all me-1"></i>
                                    Marcar Completada
                                </button>

                                <button v-if="['pendiente', 'confirmada'].includes(reserva.estado)"
                                        @click="cancelarReserva"
                                        class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Cancelar Reserva
                                </button>

                                <Link v-if="reserva.estado === 'confirmada'"
                                      :href="route('pedidos.create', { mesa: reserva.mesa_id, reserva: reserva.id_reserva })"
                                      class="btn btn-outline-primary">
                                    <i class="bi bi-basket me-1"></i>
                                    Crear Pedido
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Fechas -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Información de Registro</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="text-muted">Creada:</td>
                                    <td>{{ formatFechaHora(reserva.created_at) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Modificada:</td>
                                    <td>{{ formatFechaHora(reserva.updated_at) }}</td>
                                </tr>
                                <tr v-if="reserva.fecha_confirmacion">
                                    <td class="text-muted">Confirmada:</td>
                                    <td>{{ formatFechaHora(reserva.fecha_confirmacion) }}</td>
                                </tr>
                                <tr v-if="reserva.fecha_cancelacion">
                                    <td class="text-muted">Cancelada:</td>
                                    <td>{{ formatFechaHora(reserva.fecha_cancelacion) }}</td>
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
    reserva: Object,
    historialEstado: Array
})

// Métodos
function editarReserva() {
    router.visit(route('reservas.edit', props.reserva.id_reserva))
}

function cambiarEstado(nuevoEstado) {
    const textoEstado = getEstadoTexto(nuevoEstado)
    if (confirm(`¿Cambiar estado de la reserva a "${textoEstado}"?`)) {
        router.post(route('reservas.cambiar-estado', props.reserva.id_reserva), {
            estado: nuevoEstado
        })
    }
}

function cancelarReserva() {
    if (confirm('¿Está seguro de cancelar esta reserva?')) {
        cambiarEstado('cancelada')
    }
}

function getBadgeClass(estado) {
    const classes = {
        'pendiente': 'badge bg-warning text-dark',
        'confirmada': 'badge bg-success',
        'cancelada': 'badge bg-danger',
        'completada': 'badge bg-primary'
    }
    return classes[estado] || 'badge bg-secondary'
}

function getEstadoTexto(estado) {
    const textos = {
        'pendiente': 'Pendiente',
        'confirmada': 'Confirmada',
        'cancelada': 'Cancelada',
        'completada': 'Completada'
    }
    return textos[estado] || estado
}

function getEstadoIcon(estado) {
    const iconos = {
        'pendiente': 'bi bi-clock',
        'confirmada': 'bi bi-check-circle',
        'cancelada': 'bi bi-x-circle',
        'completada': 'bi bi-check2-all'
    }
    return iconos[estado] || 'bi bi-circle'
}

function getTimelineMarkerClass(estado) {
    const classes = {
        'pendiente': 'warning',
        'confirmada': 'success',
        'cancelada': 'danger',
        'completada': 'primary'
    }
    return `timeline-marker-${classes[estado] || 'secondary'}`
}

function getMesaBadgeClass(estado) {
    const classes = {
        'disponible': 'badge bg-success',
        'ocupada': 'badge bg-danger',
        'reservada': 'badge bg-warning text-dark',
        'mantenimiento': 'badge bg-secondary'
    }
    return classes[estado] || 'badge bg-secondary'
}

function getMesaEstadoTexto(estado) {
    const textos = {
        'disponible': 'Disponible',
        'ocupada': 'Ocupada',
        'reservada': 'Reservada',
        'mantenimiento': 'Mantenimiento'
    }
    return textos[estado] || estado
}

function formatFecha(fecha) {
    return new Date(fecha).toLocaleDateString('es-PE', {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
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

function calcularDuracion(horaInicio, horaFin) {
    const inicio = new Date(`2000-01-01 ${horaInicio}`)
    const fin = new Date(`2000-01-01 ${horaFin}`)
    const duracion = (fin - inicio) / (1000 * 60) // minutos
    
    if (duracion >= 60) {
        const horas = Math.floor(duracion / 60)
        const minutos = duracion % 60
        return `${horas}h ${minutos > 0 ? minutos + 'm' : ''}`
    }
    return `${duracion}m`
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

.timeline-content {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.375rem;
    border: 1px solid #dee2e6;
}

.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
    border: none;
}

.table-borderless td {
    padding: 0.25rem 0;
}
</style>