<template>
    <AppLayout>
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2 class="text-primary mb-0">
                        <i class="bi bi-calendar-check me-2"></i>
                        Gestión de Reservas
                    </h2>
                    <p class="text-muted">Administra las reservas del restaurante</p>
                </div>
                <div class="col-md-4 text-end">
                    <Link :href="route('reservas.create')" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Nueva Reserva
                    </Link>
                </div>
            </div>

            <!-- Estadísticas -->
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
                    <div class="card bg-success bg-gradient text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">Confirmadas</h6>
                                    <h3 class="mb-0">{{ estadisticas.confirmadas || 0 }}</h3>
                                </div>
                                <i class="bi bi-check-circle fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info bg-gradient text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">Para Hoy</h6>
                                    <h3 class="mb-0">{{ estadisticas.hoy || 0 }}</h3>
                                </div>
                                <i class="bi bi-calendar-day fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-primary bg-gradient text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">Esta Semana</h6>
                                    <h3 class="mb-0">{{ estadisticas.proximasSemanaa || 0 }}</h3>
                                </div>
                                <i class="bi bi-calendar-week fs-1 opacity-50"></i>
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
                                <option value="pendiente">Pendiente</option>
                                <option value="confirmada">Confirmada</option>
                                <option value="cancelada">Cancelada</option>
                                <option value="completada">Completada</option>
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

            <!-- Lista de reservas -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Mesa</th>
                                    <th>Fecha</th>
                                    <th>Horario</th>
                                    <th>Personas</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="reserva in reservas.data" :key="reserva.id_reserva">
                                    <td>
                                        <strong>#{{ reserva.id_reserva }}</strong>
                                    </td>
                                    <td>
                                        {{ reserva.cliente?.nombre || 'Sin cliente' }}
                                        {{ reserva.cliente?.apellido || '' }}
                                        <br>
                                        <small class="text-muted">{{ reserva.cliente?.email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            Mesa #{{ reserva.mesa?.numero_mesa }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ reserva.mesa?.capacidad }} personas</small>
                                    </td>
                                    <td>
                                        {{ formatFecha(reserva.fecha_reserva) }}
                                    </td>
                                    <td>
                                        <strong>{{ reserva.hora_inicio }}</strong> - {{ reserva.hora_fin }}
                                        <br>
                                        <small class="text-muted">{{ calcularDuracion(reserva.hora_inicio, reserva.hora_fin) }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark">
                                            {{ reserva.numero_personas }}
                                            <i class="bi bi-people ms-1"></i>
                                        </span>
                                    </td>
                                    <td>
                                        <span :class="getBadgeClass(reserva.estado)">
                                            {{ getEstadoTexto(reserva.estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <Link :href="route('reservas.show', reserva.id_reserva)" 
                                                  class="btn btn-outline-info">
                                                <i class="bi bi-eye"></i>
                                            </Link>
                                            <button v-if="reserva.estado === 'pendiente'"
                                                    @click="editarReserva(reserva)"
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
                                                    <li v-if="reserva.estado === 'pendiente'">
                                                        <a @click="cambiarEstado(reserva, 'confirmada')" 
                                                           class="dropdown-item" href="#">
                                                            <i class="bi bi-check-circle text-success"></i>
                                                            Confirmar
                                                        </a>
                                                    </li>
                                                    <li v-if="reserva.estado === 'confirmada'">
                                                        <a @click="cambiarEstado(reserva, 'completada')" 
                                                           class="dropdown-item" href="#">
                                                            <i class="bi bi-check2-all text-primary"></i>
                                                            Completar
                                                        </a>
                                                    </li>
                                                    <li v-if="['pendiente', 'confirmada'].includes(reserva.estado)">
                                                        <hr class="dropdown-divider">
                                                        <a @click="cancelarReserva(reserva)" 
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
                    <nav v-if="reservas.links.length > 3" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li v-for="link in reservas.links" :key="link.label"
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
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Props
const props = defineProps({
    reservas: Object,
    mesas: Array,
    estadisticas: Object,
    filtros: Object
})

// Estado reactivo
const filtros = ref({
    estado: props.filtros.estado || '',
    fecha: props.filtros.fecha || '',
    mesa_id: props.filtros.mesa_id || ''
})

// Métodos
function aplicarFiltros() {
    router.get(route('reservas.index'), filtros.value, {
        preserveState: true,
        preserveScroll: true
    })
}

function limpiarFiltros() {
    filtros.value = { estado: '', fecha: '', mesa_id: '' }
    aplicarFiltros()
}

function editarReserva(reserva) {
    router.visit(route('reservas.edit', reserva.id_reserva))
}

function cambiarEstado(reserva, nuevoEstado) {
    const textoEstado = getEstadoTexto(nuevoEstado)
    if (confirm(`¿Cambiar estado de la reserva #${reserva.id_reserva} a "${textoEstado}"?`)) {
        router.post(route('reservas.cambiar-estado', reserva.id_reserva), {
            estado: nuevoEstado
        })
    }
}

function cancelarReserva(reserva) {
    if (confirm(`¿Está seguro de cancelar la reserva #${reserva.id_reserva}?`)) {
        cambiarEstado(reserva, 'cancelada')
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

function formatFecha(fecha) {
    return new Date(fecha).toLocaleDateString('es-PE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
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