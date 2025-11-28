<template>
    <AppLayout>
        <div class="container-fluid py-4">
            <!-- Cabecera -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <Link :href="route('tickets.show', ticket.id_ticket)" class="btn btn-outline-secondary me-3">
                            <i class="bi bi-arrow-left"></i>
                        </Link>
                        <div>
                            <h2 class="text-primary mb-0">
                                <i class="bi bi-pencil me-2"></i>
                                Editar Ticket
                            </h2>
                            <p class="text-muted mb-0">
                                <code>{{ ticket.codigo_ticket }}</code> - {{ ticket.titulo }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <form @submit.prevent="actualizarTicket">
                <div class="row">
                    <!-- Formulario Principal -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Información del Ticket
                                </h6>
                            </div>
                            <div class="card-body">
                                <!-- Título -->
                                <div class="mb-3">
                                    <label for="titulo" class="form-label">Título *</label>
                                    <input type="text" 
                                           id="titulo"
                                           v-model="form.titulo" 
                                           class="form-control" 
                                           :class="{ 'is-invalid': errors.titulo }"
                                           placeholder="Describe brevemente el problema o solicitud"
                                           required>
                                    <div v-if="errors.titulo" class="invalid-feedback">
                                        {{ errors.titulo }}
                                    </div>
                                </div>

                                <!-- Descripción -->
                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción *</label>
                                    <textarea id="descripcion"
                                              v-model="form.descripcion" 
                                              class="form-control" 
                                              :class="{ 'is-invalid': errors.descripcion }"
                                              rows="5"
                                              placeholder="Describe detalladamente el problema"
                                              required></textarea>
                                    <div v-if="errors.descripcion" class="invalid-feedback">
                                        {{ errors.descripcion }}
                                    </div>
                                </div>

                                <!-- Categoría y Prioridad -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="categoria" class="form-label">Categoría *</label>
                                        <select id="categoria"
                                                v-model="form.categoria" 
                                                class="form-select" 
                                                :class="{ 'is-invalid': errors.categoria }"
                                                required>
                                            <option value="">Seleccionar categoría...</option>
                                            <option value="soporte_tecnico">Soporte Técnico</option>
                                            <option value="queja_cliente">Queja Cliente</option>
                                            <option value="sugerencia">Sugerencia</option>
                                            <option value="problema_servicio">Problema Servicio</option>
                                            <option value="incidente_operativo">Incidente Operativo</option>
                                            <option value="solicitud_mantenimiento">Solicitud Mantenimiento</option>
                                            <option value="consulta_general">Consulta General</option>
                                        </select>
                                        <div v-if="errors.categoria" class="invalid-feedback">
                                            {{ errors.categoria }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="prioridad" class="form-label">Prioridad *</label>
                                        <select id="prioridad"
                                                v-model="form.prioridad" 
                                                class="form-select" 
                                                :class="{ 'is-invalid': errors.prioridad }"
                                                required>
                                            <option value="">Seleccionar prioridad...</option>
                                            <option value="baja">Baja - Sin impacto inmediato</option>
                                            <option value="media">Media - Afecta operación parcialmente</option>
                                            <option value="alta">Alta - Afecta operación significativamente</option>
                                            <option value="critica">Crítica - Bloquea operación completamente</option>
                                        </select>
                                        <div v-if="errors.prioridad" class="invalid-feedback">
                                            {{ errors.prioridad }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Estado -->
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label for="estado" class="form-label">Estado *</label>
                                        <select id="estado"
                                                v-model="form.estado" 
                                                class="form-select" 
                                                :class="{ 'is-invalid': errors.estado }"
                                                required>
                                            <option value="abierto">Abierto</option>
                                            <option value="en_proceso">En Proceso</option>
                                            <option value="esperando_respuesta">Esperando Respuesta</option>
                                            <option value="resuelto">Resuelto</option>
                                            <option value="cerrado">Cerrado</option>
                                            <option value="escalado">Escalado</option>
                                        </select>
                                        <div v-if="errors.estado" class="invalid-feedback">
                                            {{ errors.estado }}
                                        </div>
                                        <small class="form-text text-muted">
                                            Los cambios de estado se registrarán automáticamente
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="usuario_asignado_id" class="form-label">Asignado a</label>
                                        <select id="usuario_asignado_id" 
                                                v-model="form.usuario_asignado_id" 
                                                class="form-select">
                                            <option value="">Sin asignar</option>
                                            <option v-for="usuario in usuarios" :key="usuario.id_usuario" :value="usuario.id_usuario">
                                                {{ usuario.nombre }} {{ usuario.apellido }} ({{ getRolNombre(usuario.id_rol) }})
                                            </option>
                                        </select>
                                        <small class="form-text text-muted">
                                            Los cambios de asignación se registrarán automáticamente
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información Relacionada -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-link-45deg me-2"></i>
                                    Información Relacionada
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="mesa_id" class="form-label">Mesa</label>
                                        <select id="mesa_id" v-model="form.mesa_id" class="form-select" disabled>
                                            <option value="">Sin mesa asociada</option>
                                            <option v-for="mesa in mesas" :key="mesa.id_mesa" :value="mesa.id_mesa">
                                                Mesa {{ mesa.numero_mesa }} - {{ mesa.ubicacion }}
                                            </option>
                                        </select>
                                        <small class="form-text text-muted">
                                            Las relaciones no se pueden modificar después de la creación
                                        </small>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="pedido_id" class="form-label">Pedido</label>
                                        <select id="pedido_id" v-model="form.pedido_id" class="form-select" disabled>
                                            <option value="">Sin pedido asociado</option>
                                            <option v-for="pedido in pedidos" :key="pedido.id_pedido" :value="pedido.id_pedido">
                                                Pedido #{{ pedido.id_pedido }}
                                                <span v-if="pedido.cliente">
                                                    - {{ pedido.cliente.nombre }} {{ pedido.cliente.apellido }}
                                                </span>
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="reserva_id" class="form-label">Reserva</label>
                                        <select id="reserva_id" v-model="form.reserva_id" class="form-select" disabled>
                                            <option value="">Sin reserva asociada</option>
                                            <option v-for="reserva in reservas" :key="reserva.id_reserva" :value="reserva.id_reserva">
                                                Reserva #{{ reserva.id_reserva }}
                                                <span v-if="reserva.cliente">
                                                    - {{ reserva.cliente.nombre }} {{ reserva.cliente.apellido }}
                                                </span>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Información del Ticket -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-info-square me-2"></i>
                                    Información
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <small class="text-muted">Código:</small>
                                    <div><code>{{ ticket.codigo_ticket }}</code></div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">Creado por:</small>
                                    <div>{{ ticket.usuario_creador?.nombre }} {{ ticket.usuario_creador?.apellido }}</div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">Creado:</small>
                                    <div>{{ formatFecha(ticket.created_at) }}</div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">Última actualización:</small>
                                    <div>{{ formatFecha(ticket.updated_at) }}</div>
                                </div>
                                <div v-if="ticket.fecha_resolucion" class="mb-3">
                                    <small class="text-muted">Resuelto:</small>
                                    <div>{{ formatFecha(ticket.fecha_resolucion) }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Configuración Avanzada -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-gear me-2"></i>
                                    Configuración
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="fecha_limite" class="form-label">Fecha límite</label>
                                    <input type="datetime-local" 
                                           id="fecha_limite"
                                           v-model="form.fecha_limite" 
                                           class="form-control"
                                           :min="fechaMinima">
                                    <small class="form-text text-muted">
                                        Opcional - para seguimiento de SLA
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Etiquetas -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-tags me-2"></i>
                                    Etiquetas
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <input type="text" 
                                           v-model="nuevaEtiqueta"
                                           @keyup.enter="agregarEtiqueta"
                                           class="form-control" 
                                           placeholder="Agregar etiqueta...">
                                </div>
                                <div v-if="form.etiquetas && form.etiquetas.length">
                                    <span v-for="(etiqueta, index) in form.etiquetas" 
                                          :key="index" 
                                          class="badge bg-light text-dark me-1 mb-1">
                                        {{ etiqueta }}
                                        <i @click="eliminarEtiqueta(index)" 
                                           class="bi bi-x ms-1 cursor-pointer"></i>
                                    </span>
                                </div>
                                <small class="text-muted">
                                    Presiona Enter para agregar una etiqueta
                                </small>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" 
                                            :disabled="processing"
                                            class="btn btn-primary">
                                        <i class="bi bi-check-circle me-1"></i>
                                        <span v-if="processing">Actualizando...</span>
                                        <span v-else>Actualizar Ticket</span>
                                    </button>
                                    <Link :href="route('tickets.show', ticket.id_ticket)" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Cancelar
                                    </Link>
                                </div>

                                <hr>

                                <!-- Acciones de Estado -->
                                <div class="d-grid gap-2">
                                    <button v-if="ticket.estado !== 'resuelto' && ticket.estado !== 'cerrado'" 
                                            type="button" 
                                            @click="marcarResuelto"
                                            class="btn btn-outline-success btn-sm">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Marcar como Resuelto
                                    </button>
                                    <button v-if="ticket.estado === 'resuelto'" 
                                            type="button" 
                                            @click="cerrarTicket"
                                            class="btn btn-outline-dark btn-sm">
                                        <i class="bi bi-lock me-1"></i>
                                        Cerrar Ticket
                                    </button>
                                    <button v-if="ticket.estado !== 'escalado'" 
                                            type="button" 
                                            @click="escalarTicket"
                                            class="btn btn-outline-warning btn-sm">
                                        <i class="bi bi-arrow-up me-1"></i>
                                        Escalar Ticket
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Historial de Estados -->
                        <div v-if="ticket.tiempo_resolucion_minutos" class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-clock me-2"></i>
                                    SLA
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <small class="text-muted">Tiempo de respuesta esperado:</small>
                                    <div>{{ ticket.tiempo_respuesta_minutos }} minutos</div>
                                </div>
                                <div v-if="ticket.tiempo_resolucion_minutos">
                                    <small class="text-muted">Tiempo de resolución:</small>
                                    <div>{{ ticket.tiempo_resolucion_minutos }} minutos</div>
                                </div>
                                <div v-if="isVencido" class="mt-2">
                                    <span class="badge bg-danger">Vencido</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { reactive, ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

export default {
    components: {
        AppLayout,
        Link
    },
    props: {
        ticket: Object,
        usuarios: Array,
        mesas: Array,
        pedidos: Array,
        reservas: Array,
        errors: Object
    },
    setup(props) {
        const processing = ref(false)
        const nuevaEtiqueta = ref('')

        const form = reactive({
            titulo: props.ticket.titulo,
            descripcion: props.ticket.descripcion,
            categoria: props.ticket.categoria,
            prioridad: props.ticket.prioridad,
            estado: props.ticket.estado,
            usuario_asignado_id: props.ticket.usuario_asignado_id || '',
            mesa_id: props.ticket.mesa_id || '',
            pedido_id: props.ticket.pedido_id || '',
            reserva_id: props.ticket.reserva_id || '',
            fecha_limite: props.ticket.fecha_limite ? 
                new Date(props.ticket.fecha_limite).toISOString().slice(0, 16) : '',
            etiquetas: props.ticket.etiquetas || []
        })

        // Fecha mínima (ahora)
        const fechaMinima = computed(() => {
            const now = new Date()
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset())
            return now.toISOString().slice(0, 16)
        })

        const isVencido = computed(() => {
            if (!props.ticket.fecha_limite) return false
            return new Date(props.ticket.fecha_limite) < new Date() && 
                   !['resuelto', 'cerrado'].includes(props.ticket.estado)
        })

        const actualizarTicket = () => {
            processing.value = true
            
            router.put(route('tickets.update', props.ticket.id_ticket), form, {
                onFinish: () => {
                    processing.value = false
                }
            })
        }

        const agregarEtiqueta = () => {
            if (nuevaEtiqueta.value.trim() && !form.etiquetas.includes(nuevaEtiqueta.value.trim())) {
                form.etiquetas.push(nuevaEtiqueta.value.trim())
                nuevaEtiqueta.value = ''
            }
        }

        const eliminarEtiqueta = (index) => {
            form.etiquetas.splice(index, 1)
        }

        const marcarResuelto = () => {
            const resolucion = prompt('Describe cómo se resolvió el ticket:')
            if (resolucion) {
                router.post(route('tickets.resolver', props.ticket.id_ticket), {
                    resolucion: resolucion
                })
            }
        }

        const cerrarTicket = () => {
            if (confirm('¿Estás seguro de que deseas cerrar este ticket?')) {
                router.post(route('tickets.cerrar', props.ticket.id_ticket))
            }
        }

        const escalarTicket = () => {
            if (confirm('¿Estás seguro de que deseas escalar este ticket?')) {
                form.estado = 'escalado'
                actualizarTicket()
            }
        }

        const getRolNombre = (idRol) => {
            const roles = {
                1: 'Admin',
                2: 'Cajero',
                3: 'Mesero',
                4: 'Cocinero',
                5: 'Cliente'
            }
            return roles[idRol] || 'Usuario'
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

        return {
            form,
            processing,
            nuevaEtiqueta,
            fechaMinima,
            isVencido,
            actualizarTicket,
            agregarEtiqueta,
            eliminarEtiqueta,
            marcarResuelto,
            cerrarTicket,
            escalarTicket,
            getRolNombre,
            formatFecha
        }
    }
}
</script>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}
</style>