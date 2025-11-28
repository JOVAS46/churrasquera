<template>
    <AppLayout>
        <div class="container-fluid py-4">
            <!-- Cabecera -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <Link :href="route('tickets.index')" class="btn btn-outline-secondary me-3">
                            <i class="bi bi-arrow-left"></i>
                        </Link>
                        <div>
                            <h2 class="text-primary mb-0">
                                <i class="bi bi-ticket-perforated me-2"></i>
                                Ticket {{ ticket.codigo_ticket }}
                            </h2>
                            <p class="text-muted mb-0">{{ ticket.titulo }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="btn-group">
                        <Link v-if="canEdit" :href="route('tickets.edit', ticket.id_ticket)" 
                              class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i>
                            Editar
                        </Link>
                        <button v-if="canResolve && ticket.estado !== 'resuelto'" 
                                @click="abrirModalResolver"
                                class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>
                            Resolver
                        </button>
                        <button v-if="canResolve && ticket.estado === 'resuelto'" 
                                @click="cerrarTicket"
                                class="btn btn-dark">
                            <i class="bi bi-lock me-1"></i>
                            Cerrar
                        </button>
                        <button v-if="canAssign" @click="abrirModalAsignar" class="btn btn-info">
                            <i class="bi bi-person-plus me-1"></i>
                            Asignar
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Columna Principal -->
                <div class="col-lg-8">
                    <!-- Información del Ticket -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Detalles del Ticket
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Categoría:</strong> 
                                        <span class="badge rounded-pill ms-2" :class="getCategoriaClass(ticket.categoria)">
                                            {{ formatCategoria(ticket.categoria) }}
                                        </span>
                                    </p>
                                    <p><strong>Prioridad:</strong> 
                                        <span class="badge rounded-pill ms-2" :class="getPrioridadClass(ticket.prioridad)">
                                            {{ formatPrioridad(ticket.prioridad) }}
                                        </span>
                                    </p>
                                    <p><strong>Estado:</strong> 
                                        <span class="badge rounded-pill ms-2" :class="getEstadoClass(ticket.estado)">
                                            {{ formatEstado(ticket.estado) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Creado por:</strong> {{ ticket.usuario_creador?.nombre }} {{ ticket.usuario_creador?.apellido }}</p>
                                    <p><strong>Asignado a:</strong> 
                                        <span v-if="ticket.usuario_asignado">
                                            {{ ticket.usuario_asignado.nombre }} {{ ticket.usuario_asignado.apellido }}
                                        </span>
                                        <span v-else class="text-muted">No asignado</span>
                                    </p>
                                    <p><strong>Fecha límite:</strong> 
                                        <span v-if="ticket.fecha_limite">
                                            {{ formatFecha(ticket.fecha_limite) }}
                                            <i v-if="isVencido(ticket)" class="bi bi-exclamation-triangle text-danger ms-1"></i>
                                        </span>
                                        <span v-else class="text-muted">No definida</span>
                                    </p>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div>
                                <h6>Descripción:</h6>
                                <p class="mb-0">{{ ticket.descripcion }}</p>
                            </div>

                            <div v-if="ticket.etiquetas && ticket.etiquetas.length" class="mt-3">
                                <h6>Etiquetas:</h6>
                                <span v-for="etiqueta in ticket.etiquetas" :key="etiqueta" 
                                      class="badge bg-light text-dark me-1">{{ etiqueta }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Información Relacionada -->
                    <div v-if="ticket.mesa || ticket.pedido || ticket.reserva" class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-link-45deg me-2"></i>
                                Información Relacionada
                            </h6>
                        </div>
                        <div class="card-body">
                            <div v-if="ticket.mesa" class="mb-3">
                                <h6 class="text-primary">Mesa</h6>
                                <p>Mesa {{ ticket.mesa.numero_mesa }} - {{ ticket.mesa.ubicacion }}</p>
                            </div>
                            <div v-if="ticket.pedido" class="mb-3">
                                <h6 class="text-primary">Pedido</h6>
                                <p>Pedido #{{ ticket.pedido.id_pedido }} 
                                   <span v-if="ticket.pedido.cliente">
                                       - {{ ticket.pedido.cliente.nombre }} {{ ticket.pedido.cliente.apellido }}
                                   </span>
                                </p>
                            </div>
                            <div v-if="ticket.reserva">
                                <h6 class="text-primary">Reserva</h6>
                                <p>Reserva #{{ ticket.reserva.id_reserva }}
                                   <span v-if="ticket.reserva.cliente">
                                       - {{ ticket.reserva.cliente.nombre }} {{ ticket.reserva.cliente.apellido }}
                                   </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Comentarios -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="bi bi-chat-dots me-2"></i>
                                Comentarios ({{ comentarios.total || 0 }})
                            </h6>
                            <button v-if="canComment" @click="mostrarFormComentario = !mostrarFormComentario" 
                                    class="btn btn-sm btn-primary">
                                <i class="bi bi-plus me-1"></i>
                                Añadir Comentario
                            </button>
                        </div>
                        
                        <!-- Formulario de Comentario -->
                        <div v-if="mostrarFormComentario && canComment" class="card-body border-bottom">
                            <form @submit.prevent="enviarComentario">
                                <div class="mb-3">
                                    <textarea v-model="nuevoComentario.comentario" 
                                              class="form-control" 
                                              rows="3" 
                                              placeholder="Escribe tu comentario..."
                                              required></textarea>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               v-model="nuevoComentario.es_interno" 
                                               class="form-check-input" 
                                               id="comentarioInterno">
                                        <label class="form-check-label" for="comentarioInterno">
                                            Comentario interno (no visible para clientes)
                                        </label>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="button" @click="mostrarFormComentario = false" 
                                            class="btn btn-secondary me-2">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send me-1"></i>
                                        Enviar
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Lista de Comentarios -->
                        <div class="card-body p-0">
                            <div v-for="comentario in comentarios.data" :key="comentario.id_comentario" 
                                 class="border-bottom p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                            {{ comentario.usuario?.nombre?.charAt(0) }}{{ comentario.usuario?.apellido?.charAt(0) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-1">
                                                {{ comentario.usuario?.nombre }} {{ comentario.usuario?.apellido }}
                                                <span class="badge bg-light text-dark ms-2">{{ formatTipoComentario(comentario.tipo) }}</span>
                                                <span v-if="comentario.es_interno" class="badge bg-warning ms-1">Interno</span>
                                            </h6>
                                            <small class="text-muted">{{ formatFecha(comentario.created_at) }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2 ms-5">
                                    <p class="mb-0">{{ comentario.comentario }}</p>
                                    <div v-if="comentario.adjuntos && comentario.adjuntos.length" class="mt-2">
                                        <small class="text-muted">Adjuntos:</small>
                                        <div v-for="adjunto in comentario.adjuntos" :key="adjunto" class="mt-1">
                                            <a :href="adjunto" class="text-decoration-none">
                                                <i class="bi bi-paperclip me-1"></i>{{ adjunto }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Paginación de Comentarios -->
                            <div v-if="comentarios.data && comentarios.data.length > 0" class="p-3">
                                <nav>
                                    <ul class="pagination pagination-sm justify-content-center mb-0">
                                        <li class="page-item" :class="{ disabled: !comentarios.prev_page_url }">
                                            <Link class="page-link" :href="comentarios.prev_page_url || '#'">
                                                Anterior
                                            </Link>
                                        </li>
                                        <li v-for="link in comentarios.links.slice(1, -1)" :key="link.label" 
                                            class="page-item" :class="{ active: link.active }">
                                            <Link class="page-link" :href="link.url" v-html="link.label"></Link>
                                        </li>
                                        <li class="page-item" :class="{ disabled: !comentarios.next_page_url }">
                                            <Link class="page-link" :href="comentarios.next_page_url || '#'">
                                                Siguiente
                                            </Link>
                                        </li>
                                    </ul>
                                </nav>
                            </div>

                            <div v-else class="p-4 text-center">
                                <i class="bi bi-chat-dots fs-1 text-muted"></i>
                                <p class="mt-2 text-muted">No hay comentarios</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Acciones Rápidas -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-lightning me-2"></i>
                                Acciones Rápidas
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button v-if="canAssign" @click="abrirModalAsignar" class="btn btn-outline-info">
                                    <i class="bi bi-person-plus me-2"></i>
                                    Reasignar Ticket
                                </button>
                                <button v-if="canResolve && ticket.estado !== 'resuelto'" 
                                        @click="abrirModalResolver" class="btn btn-outline-success">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Marcar como Resuelto
                                </button>
                                <button v-if="canResolve && ticket.estado === 'resuelto'" 
                                        @click="cerrarTicket" class="btn btn-outline-dark">
                                    <i class="bi bi-lock me-2"></i>
                                    Cerrar Ticket
                                </button>
                                <button v-if="ticket.estado !== 'escalado'" 
                                        @click="escalarTicket" class="btn btn-outline-warning">
                                    <i class="bi bi-arrow-up me-2"></i>
                                    Escalar Ticket
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tickets Relacionados -->
                    <div v-if="ticketsRelacionados && ticketsRelacionados.length" class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-link me-2"></i>
                                Tickets Relacionados
                            </h6>
                        </div>
                        <div class="card-body">
                            <div v-for="relacionado in ticketsRelacionados" :key="relacionado.id_ticket" 
                                 class="border-bottom pb-2 mb-2 last:border-0">
                                <Link :href="route('tickets.show', relacionado.id_ticket)" 
                                      class="text-decoration-none">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <code class="text-primary">{{ relacionado.codigo_ticket }}</code>
                                            <div class="small text-muted">{{ relacionado.titulo }}</div>
                                        </div>
                                        <span class="badge rounded-pill" :class="getEstadoClass(relacionado.estado)">
                                            {{ formatEstado(relacionado.estado) }}
                                        </span>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Resolver Ticket -->
        <div class="modal fade" id="modalResolver" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Resolver Ticket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form @submit.prevent="resolverTicket">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Descripción de la resolución</label>
                                <textarea v-model="resolucion.resolucion" 
                                          class="form-control" 
                                          rows="4" 
                                          placeholder="Describe cómo se resolvió el ticket..."
                                          required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i>
                                Resolver Ticket
                            </button>
                        </div>
                    </form>
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
        ticket: Object,
        comentarios: Object,
        usuarios: Array,
        ticketsRelacionados: Array,
        canEdit: Boolean,
        canComment: Boolean,
        canAssign: Boolean,
        canResolve: Boolean
    },
    setup(props) {
        const mostrarFormComentario = ref(false)
        
        const nuevoComentario = reactive({
            comentario: '',
            es_interno: false
        })

        const asignacion = reactive({
            usuario_asignado_id: props.ticket.usuario_asignado_id || ''
        })

        const resolucion = reactive({
            resolucion: ''
        })

        const enviarComentario = () => {
            router.post(route('tickets.comentario', props.ticket.id_ticket), nuevoComentario, {
                onSuccess: () => {
                    nuevoComentario.comentario = ''
                    nuevoComentario.es_interno = false
                    mostrarFormComentario.value = false
                }
            })
        }

        const abrirModalAsignar = () => {
            new bootstrap.Modal(document.getElementById('modalAsignar')).show()
        }

        const asignarTicket = () => {
            router.post(route('tickets.asignar', props.ticket.id_ticket), asignacion, {
                onSuccess: () => {
                    bootstrap.Modal.getInstance(document.getElementById('modalAsignar')).hide()
                }
            })
        }

        const abrirModalResolver = () => {
            new bootstrap.Modal(document.getElementById('modalResolver')).show()
        }

        const resolverTicket = () => {
            router.post(route('tickets.resolver', props.ticket.id_ticket), resolucion, {
                onSuccess: () => {
                    bootstrap.Modal.getInstance(document.getElementById('modalResolver')).hide()
                    resolucion.resolucion = ''
                }
            })
        }

        const cerrarTicket = () => {
            if (confirm('¿Estás seguro de que deseas cerrar este ticket?')) {
                router.post(route('tickets.cerrar', props.ticket.id_ticket))
            }
        }

        const escalarTicket = () => {
            if (confirm('¿Estás seguro de que deseas escalar este ticket?')) {
                router.patch(route('tickets.update', props.ticket.id_ticket), {
                    estado: 'escalado'
                })
            }
        }

        // Funciones de formato
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

        const formatTipoComentario = (tipo) => {
            const formatos = {
                'comentario': 'Comentario',
                'cambio_estado': 'Cambio Estado',
                'asignacion': 'Asignación',
                'resolucion': 'Resolución',
                'escalacion': 'Escalación'
            }
            return formatos[tipo] || tipo
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
            mostrarFormComentario,
            nuevoComentario,
            asignacion,
            resolucion,
            enviarComentario,
            abrirModalAsignar,
            asignarTicket,
            abrirModalResolver,
            resolverTicket,
            cerrarTicket,
            escalarTicket,
            getCategoriaClass,
            getPrioridadClass,
            getEstadoClass,
            formatCategoria,
            formatPrioridad,
            formatEstado,
            formatTipoComentario,
            formatFecha,
            isVencido
        }
    }
}
</script>

<style scoped>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 12px;
}
</style>