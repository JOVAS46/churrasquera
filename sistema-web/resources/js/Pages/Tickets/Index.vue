<template>
    <AppLayout>
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2 class="text-primary mb-0">
                        <i class="bi bi-ticket-perforated me-2"></i>
                        Sistema de Tickets
                    </h2>
                    <p class="text-muted">Gestión de tickets de soporte e incidencias</p>
                </div>
                <div class="col-md-4 text-end" v-if="canCreate">
                    <Link :href="route('tickets.create')" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Nuevo Ticket
                    </Link>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card bg-primary bg-gradient text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">Total</h6>
                                    <h3 class="mb-0">{{ estadisticas.total || 0 }}</h3>
                                </div>
                                <i class="bi bi-ticket fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-warning bg-gradient text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">Abiertos</h6>
                                    <h3 class="mb-0">{{ estadisticas.abiertos || 0 }}</h3>
                                </div>
                                <i class="bi bi-folder2-open fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-danger bg-gradient text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">Vencidos</h6>
                                    <h3 class="mb-0">{{ estadisticas.vencidos || 0 }}</h3>
                                </div>
                                <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success bg-gradient text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">Resueltos Hoy</h6>
                                    <h3 class="mb-0">{{ estadisticas.resueltos_hoy || 0 }}</h3>
                                </div>
                                <i class="bi bi-check-circle fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Por Prioridad</h6>
                            <div class="row">
                                <div class="col-3 text-center">
                                    <div class="badge bg-danger rounded-pill">{{ estadisticas.por_prioridad?.critica || 0 }}</div>
                                    <small class="d-block">Crítica</small>
                                </div>
                                <div class="col-3 text-center">
                                    <div class="badge bg-warning rounded-pill">{{ estadisticas.por_prioridad?.alta || 0 }}</div>
                                    <small class="d-block">Alta</small>
                                </div>
                                <div class="col-3 text-center">
                                    <div class="badge bg-info rounded-pill">{{ estadisticas.por_prioridad?.media || 0 }}</div>
                                    <small class="d-block">Media</small>
                                </div>
                                <div class="col-3 text-center">
                                    <div class="badge bg-secondary rounded-pill">{{ estadisticas.por_prioridad?.baja || 0 }}</div>
                                    <small class="d-block">Baja</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-funnel me-2"></i>
                        Filtros
                    </h6>
                </div>
                <div class="card-body">
                    <form @submit.prevent="aplicarFiltros">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Estado</label>
                                <select v-model="filtros.estado" class="form-select form-select-sm">
                                    <option value="">Todos</option>
                                    <option value="abierto">Abierto</option>
                                    <option value="en_proceso">En Proceso</option>
                                    <option value="esperando_respuesta">Esperando Respuesta</option>
                                    <option value="resuelto">Resuelto</option>
                                    <option value="cerrado">Cerrado</option>
                                    <option value="escalado">Escalado</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Prioridad</label>
                                <select v-model="filtros.prioridad" class="form-select form-select-sm">
                                    <option value="">Todas</option>
                                    <option value="critica">Crítica</option>
                                    <option value="alta">Alta</option>
                                    <option value="media">Media</option>
                                    <option value="baja">Baja</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Categoría</label>
                                <select v-model="filtros.categoria" class="form-select form-select-sm">
                                    <option value="">Todas</option>
                                    <option value="soporte_tecnico">Soporte Técnico</option>
                                    <option value="queja_cliente">Queja Cliente</option>
                                    <option value="sugerencia">Sugerencia</option>
                                    <option value="problema_servicio">Problema Servicio</option>
                                    <option value="incidente_operativo">Incidente Operativo</option>
                                    <option value="solicitud_mantenimiento">Solicitud Mantenimiento</option>
                                    <option value="consulta_general">Consulta General</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Asignado a</label>
                                <select v-model="filtros.asignado_a" class="form-select form-select-sm">
                                    <option value="">Todos</option>
                                    <option v-for="usuario in usuarios" :key="usuario.id_usuario" :value="usuario.id_usuario">
                                        {{ usuario.nombre }} {{ usuario.apellido }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Desde</label>
                                <input type="date" v-model="filtros.fecha_desde" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Hasta</label>
                                <input type="date" v-model="filtros.fecha_hasta" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-search me-1"></i>
                                Filtrar
                            </button>
                            <button type="button" @click="limpiarFiltros" class="btn btn-outline-secondary btn-sm ms-2">
                                <i class="bi bi-x me-1"></i>
                                Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Tickets -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Tickets ({{ tickets.total || 0 }})
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Título</th>
                                    <th>Categoría</th>
                                    <th>Prioridad</th>
                                    <th>Estado</th>
                                    <th>Creado por</th>
                                    <th>Asignado a</th>
                                    <th>Creado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="ticket in tickets.data" :key="ticket.id_ticket">
                                    <td>
                                        <code class="text-primary">{{ ticket.codigo_ticket }}</code>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ ticket.titulo }}</div>
                                        <small class="text-muted" v-if="ticket.descripcion">
                                            {{ ticket.descripcion.substring(0, 50) }}...
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill" :class="getCategoriaClass(ticket.categoria)">
                                            {{ formatCategoria(ticket.categoria) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill" :class="getPrioridadClass(ticket.prioridad)">
                                            {{ formatPrioridad(ticket.prioridad) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill" :class="getEstadoClass(ticket.estado)">
                                            {{ formatEstado(ticket.estado) }}
                                        </span>
                                        <i v-if="isVencido(ticket)" class="bi bi-exclamation-triangle text-danger ms-1" 
                                           title="Ticket vencido"></i>
                                    </td>
                                    <td>
                                        <small>{{ ticket.usuario_creador?.nombre }} {{ ticket.usuario_creador?.apellido }}</small>
                                    </td>
                                    <td>
                                        <small v-if="ticket.usuario_asignado">
                                            {{ ticket.usuario_asignado.nombre }} {{ ticket.usuario_asignado.apellido }}
                                        </small>
                                        <span v-else class="text-muted">No asignado</span>
                                    </td>
                                    <td>
                                        <small>{{ formatFecha(ticket.created_at) }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <Link :href="route('tickets.show', ticket.id_ticket)" 
                                                  class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </Link>
                                            <Link v-if="canEdit" :href="route('tickets.edit', ticket.id_ticket)" 
                                                  class="btn btn-outline-warning btn-sm">
                                                <i class="bi bi-pencil"></i>
                                            </Link>
                                            <button v-if="canAssign && ticket.estado !== 'cerrado'" 
                                                    @click="abrirModalAsignar(ticket)"
                                                    class="btn btn-outline-info btn-sm">
                                                <i class="bi bi-person-plus"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div v-if="tickets.data && tickets.data.length > 0" class="p-3 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Mostrando {{ tickets.from }} a {{ tickets.to }} de {{ tickets.total }} resultados
                            </small>
                            <nav>
                                <ul class="pagination pagination-sm mb-0">
                                    <li class="page-item" :class="{ disabled: !tickets.prev_page_url }">
                                        <Link class="page-link" :href="tickets.prev_page_url || '#'">
                                            Anterior
                                        </Link>
                                    </li>
                                    <li v-for="link in tickets.links.slice(1, -1)" :key="link.label" 
                                        class="page-item" :class="{ active: link.active }">
                                        <Link class="page-link" :href="link.url" v-html="link.label"></Link>
                                    </li>
                                    <li class="page-item" :class="{ disabled: !tickets.next_page_url }">
                                        <Link class="page-link" :href="tickets.next_page_url || '#'">
                                            Siguiente
                                        </Link>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>

                    <div v-else class="p-4 text-center">
                        <i class="bi bi-ticket-perforated fs-1 text-muted"></i>
                        <p class="mt-2 text-muted">No hay tickets para mostrar</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Asignar Ticket -->
        <div class="modal fade" id="modalAsignar" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Asignar Ticket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form @submit.prevent="asignarTicket">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Ticket</label>
                                <p class="form-control-plaintext">
                                    <code>{{ ticketSeleccionado?.codigo_ticket }}</code> - {{ ticketSeleccionado?.titulo }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Asignar a</label>
                                <select v-model="asignacion.usuario_asignado_id" class="form-select" required>
                                    <option value="">Seleccionar usuario...</option>
                                    <option v-for="usuario in usuarios" :key="usuario.id_usuario" :value="usuario.id_usuario">
                                        {{ usuario.nombre }} {{ usuario.apellido }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-plus me-1"></i>
                                Asignar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { reactive, ref } from 'vue'
import { router } from '@inertiajs/vue3'

export default {
    components: {
        AppLayout,
        Link
    },
    props: {
        tickets: Object,
        estadisticas: Object,
        usuarios: Array,
        filtros: Object,
        canCreate: Boolean,
        canAssign: Boolean
    },
    setup(props) {
        const filtros = reactive({
            estado: props.filtros?.estado || '',
            prioridad: props.filtros?.prioridad || '',
            categoria: props.filtros?.categoria || '',
            asignado_a: props.filtros?.asignado_a || '',
            creado_por: props.filtros?.creado_por || '',
            fecha_desde: props.filtros?.fecha_desde || '',
            fecha_hasta: props.filtros?.fecha_hasta || ''
        })

        const ticketSeleccionado = ref(null)
        const asignacion = reactive({
            usuario_asignado_id: ''
        })

        const aplicarFiltros = () => {
            router.get(route('tickets.index'), filtros, {
                preserveState: true,
                preserveScroll: true
            })
        }

        const limpiarFiltros = () => {
            Object.keys(filtros).forEach(key => {
                filtros[key] = ''
            })
            aplicarFiltros()
        }

        const abrirModalAsignar = (ticket) => {
            ticketSeleccionado.value = ticket
            asignacion.usuario_asignado_id = ticket.usuario_asignado_id || ''
            new bootstrap.Modal(document.getElementById('modalAsignar')).show()
        }

        const asignarTicket = () => {
            router.post(route('tickets.asignar', ticketSeleccionado.value.id_ticket), asignacion, {
                onSuccess: () => {
                    bootstrap.Modal.getInstance(document.getElementById('modalAsignar')).hide()
                    asignacion.usuario_asignado_id = ''
                }
            })
        }

        const getCategoriaClass = (categoria) => {
            const clases = {
                'soporte_tecnico': 'bg-primary',
                'queja_cliente': 'bg-danger',
                'sugerencia': 'bg-success',
                'problema_servicio': 'bg-warning',
                'incidente_operativo': 'bg-dark',
                'solicitud_mantenimiento': 'bg-info',
                'consulta_general': 'bg-secondary'
            }
            return clases[categoria] || 'bg-secondary'
        }

        const getPrioridadClass = (prioridad) => {
            const clases = {
                'critica': 'bg-danger',
                'alta': 'bg-warning',
                'media': 'bg-info',
                'baja': 'bg-secondary'
            }
            return clases[prioridad] || 'bg-secondary'
        }

        const getEstadoClass = (estado) => {
            const clases = {
                'abierto': 'bg-warning',
                'en_proceso': 'bg-info',
                'esperando_respuesta': 'bg-primary',
                'resuelto': 'bg-success',
                'cerrado': 'bg-dark',
                'escalado': 'bg-danger'
            }
            return clases[estado] || 'bg-secondary'
        }

        const formatCategoria = (categoria) => {
            const formatos = {
                'soporte_tecnico': 'Soporte Técnico',
                'queja_cliente': 'Queja Cliente',
                'sugerencia': 'Sugerencia',
                'problema_servicio': 'Problema Servicio',
                'incidente_operativo': 'Incidente Operativo',
                'solicitud_mantenimiento': 'Solicitud Mantenimiento',
                'consulta_general': 'Consulta General'
            }
            return formatos[categoria] || categoria
        }

        const formatPrioridad = (prioridad) => {
            const formatos = {
                'critica': 'Crítica',
                'alta': 'Alta',
                'media': 'Media',
                'baja': 'Baja'
            }
            return formatos[prioridad] || prioridad
        }

        const formatEstado = (estado) => {
            const formatos = {
                'abierto': 'Abierto',
                'en_proceso': 'En Proceso',
                'esperando_respuesta': 'Esperando Respuesta',
                'resuelto': 'Resuelto',
                'cerrado': 'Cerrado',
                'escalado': 'Escalado'
            }
            return formatos[estado] || estado
        }

        const formatFecha = (fecha) => {
            return new Date(fecha).toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            })
        }

        const isVencido = (ticket) => {
            if (!ticket.fecha_limite) return false
            return new Date(ticket.fecha_limite) < new Date() && 
                   !['resuelto', 'cerrado'].includes(ticket.estado)
        }

        return {
            filtros,
            ticketSeleccionado,
            asignacion,
            aplicarFiltros,
            limpiarFiltros,
            abrirModalAsignar,
            asignarTicket,
            getCategoriaClass,
            getPrioridadClass,
            getEstadoClass,
            formatCategoria,
            formatPrioridad,
            formatEstado,
            formatFecha,
            isVencido
        }
    }
}
</script>