<template>
    <AppLayout>
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="text-primary mb-0">
                        <i class="bi bi-bar-chart-line me-2"></i>
                        Reportes y Estadísticas
                    </h2>
                    <p class="text-muted">Dashboard completo del negocio y métricas de rendimiento</p>
                </div>
            </div>

            <!-- Estadísticas Generales - Cards Principales -->
            <div class="row mb-4">
                <!-- Visitas -->
                <div class="col-md-6 col-xl-3 mb-3">
                    <div class="card stats-card border-start border-primary border-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Visitas Totales</p>
                                    <h3 class="mb-0">{{ formatNumber(estadisticas.visitas_totales) }}</h3>
                                    <small class="text-success">
                                        <i class="bi bi-arrow-up"></i> Hoy: {{ estadisticas.visitas_hoy }}
                                    </small>
                                </div>
                                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-eye fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ingresos -->
                <div class="col-md-6 col-xl-3 mb-3">
                    <div class="card stats-card border-start border-success border-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Ingresos del Mes</p>
                                    <h3 class="mb-0">Bs. {{ formatMoney(estadisticas.ingresos_mes) }}</h3>
                                    <small class="text-success">
                                        <i class="bi bi-arrow-up"></i> Hoy: Bs. {{ formatMoney(estadisticas.ingresos_hoy) }}
                                    </small>
                                </div>
                                <div class="icon-box bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-cash-coin fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pedidos -->
                <div class="col-md-6 col-xl-3 mb-3">
                    <div class="card stats-card border-start border-warning border-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Pedidos del Mes</p>
                                    <h3 class="mb-0">{{ estadisticas.pedidos_mes }}</h3>
                                    <small class="text-warning">
                                        <i class="bi bi-clock"></i> Pendientes: {{ estadisticas.pedidos_pendientes }}
                                    </small>
                                </div>
                                <div class="icon-box bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-cart fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reservas -->
                <div class="col-md-6 col-xl-3 mb-3">
                    <div class="card stats-card border-start border-info border-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Reservas del Mes</p>
                                    <h3 class="mb-0">{{ estadisticas.reservas_mes }}</h3>
                                    <small class="text-info">
                                        <i class="bi bi-calendar-check"></i> Hoy: {{ estadisticas.reservas_hoy }}
                                    </small>
                                </div>
                                <div class="icon-box bg-info bg-opacity-10 text-info">
                                    <i class="bi bi-calendar3 fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Segunda Fila de Estadísticas -->
            <div class="row mb-4">
                <!-- Productos -->
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-3 text-muted">
                                <i class="bi bi-box-seam me-2"></i>Productos
                            </h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total:</span>
                                <strong>{{ estadisticas.productos_totales }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Disponibles:</span>
                                <span class="badge bg-success">{{ estadisticas.productos_disponibles }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>No disponibles:</span>
                                <span class="badge bg-danger">{{ estadisticas.productos_totales - estadisticas.productos_disponibles }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mesas -->
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-3 text-muted">
                                <i class="bi bi-table me-2"></i>Mesas
                            </h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total:</span>
                                <strong>{{ estadisticas.mesas_totales }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Disponibles:</span>
                                <span class="badge bg-success">{{ estadisticas.mesas_disponibles }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Ocupadas:</span>
                                <span class="badge bg-warning">{{ estadisticas.mesas_ocupadas }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Usuarios -->
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-3 text-muted">
                                <i class="bi bi-people me-2"></i>Usuarios
                            </h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total:</span>
                                <strong>{{ estadisticas.usuarios_totales }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Activos:</span>
                                <span class="badge bg-success">{{ estadisticas.usuarios_activos }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Inactivos:</span>
                                <span class="badge bg-secondary">{{ estadisticas.usuarios_totales - estadisticas.usuarios_activos }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Visitas por Página -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-graph-up me-2"></i>
                                Páginas Más Visitadas (Top 10)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div v-if="visitasPorPagina.length === 0" class="text-muted text-center py-4">
                                No hay datos de visitas disponibles
                            </div>
                            <div v-else>
                                <div v-for="(visita, index) in visitasPorPagina" :key="index" class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-truncate">{{ visita.pagina }}</span>
                                        <strong>{{ formatNumber(visita.total_visitas) }}</strong>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar"
                                             :style="{ width: calcularPorcentaje(visita.total_visitas, visitasPorPagina[0].total_visitas) + '%' }">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Productos Más Vendidos -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-trophy me-2"></i>
                                Productos Más Vendidos (Top 10)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div v-if="productosMasVendidos.length === 0" class="text-muted text-center py-4">
                                No hay datos de ventas disponibles
                            </div>
                            <div v-else class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Producto</th>
                                            <th class="text-end">Precio</th>
                                            <th class="text-end">Vendidos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(producto, index) in productosMasVendidos" :key="index">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ producto.nombre }}</td>
                                            <td class="text-end">Bs. {{ formatMoney(producto.precio) }}</td>
                                            <td class="text-end">
                                                <span class="badge bg-primary">{{ producto.veces_vendido }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reservas por Estado -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-pie-chart me-2"></i>
                                Reservas por Estado
                            </h5>
                        </div>
                        <div class="card-body">
                            <div v-if="Object.keys(reservasPorEstado).length === 0" class="text-muted text-center py-4">
                                No hay reservas registradas
                            </div>
                            <div v-else>
                                <div v-for="(total, estado) in reservasPorEstado" :key="estado" class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-capitalize">{{ estado }}</span>
                                        <strong>{{ total }}</strong>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar"
                                             :class="getEstadoColor(estado)"
                                             :style="{ width: calcularPorcentajeReservas(total) + '%' }">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ingresos Mensuales -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-calendar2-month me-2"></i>
                                Ingresos por Mes ({{ new Date().getFullYear() }})
                            </h5>
                        </div>
                        <div class="card-body">
                            <div v-if="Object.keys(ingresosMensuales).length === 0" class="text-muted text-center py-4">
                                No hay datos de ingresos disponibles
                            </div>
                            <div v-else class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Mes</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="mes in 12" :key="mes">
                                            <td>{{ getNombreMes(mes) }}</td>
                                            <td class="text-end">
                                                <strong class="text-success">
                                                    Bs. {{ formatMoney(ingresosMensuales[mes] || 0) }}
                                                </strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

// Props
const props = defineProps({
    estadisticas: Object,
    visitasPorPagina: Array,
    productosMasVendidos: Array,
    reservasPorEstado: Object,
    ingresosMensuales: Object
})

// Métodos
function formatNumber(number) {
    return new Intl.NumberFormat('es-BO').format(number || 0)
}

function formatMoney(amount) {
    return new Intl.NumberFormat('es-BO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount || 0)
}

function calcularPorcentaje(valor, maximo) {
    if (!maximo || maximo === 0) return 0
    return Math.round((valor / maximo) * 100)
}

function calcularPorcentajeReservas(total) {
    const totalReservas = Object.values(props.reservasPorEstado).reduce((sum, val) => sum + val, 0)
    if (!totalReservas) return 0
    return Math.round((total / totalReservas) * 100)
}

function getEstadoColor(estado) {
    const colores = {
        'pendiente': 'bg-warning',
        'confirmada': 'bg-info',
        'completada': 'bg-success',
        'cancelada': 'bg-danger'
    }
    return colores[estado] || 'bg-secondary'
}

function getNombreMes(numeroMes) {
    const meses = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ]
    return meses[numeroMes - 1]
}
</script>

<style scoped>
.stats-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.icon-box {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: none;
}

.progress {
    background-color: #e9ecef;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    color: #6c757d;
    text-transform: uppercase;
}
</style>
