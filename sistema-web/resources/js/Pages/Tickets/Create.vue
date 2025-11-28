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
                                Nuevo Ticket
                            </h2>
                            <p class="text-muted mb-0">Crea un nuevo ticket de soporte o incidencia</p>
                        </div>
                    </div>
                </div>
            </div>

            <form @submit.prevent="guardarTicket">
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
                                              placeholder="Describe detalladamente el problema, incluyendo pasos para reproducirlo si aplica"
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
                            </div>
                        </div>

                        <!-- Información Relacionada -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-link-45deg me-2"></i>
                                    Información Relacionada (Opcional)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="mesa_id" class="form-label">Mesa</label>
                                        <select id="mesa_id" v-model="form.mesa_id" class="form-select">
                                            <option value="">Sin mesa asociada</option>
                                            <option v-for="mesa in mesas" :key="mesa.id_mesa" :value="mesa.id_mesa">
                                                Mesa {{ mesa.numero_mesa }} - {{ mesa.ubicacion }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="pedido_id" class="form-label">Pedido</label>
                                        <select id="pedido_id" v-model="form.pedido_id" class="form-select">
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
                                        <select id="reserva_id" v-model="form.reserva_id" class="form-select">
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

                        <!-- Datos Pre-cargados -->
                        <div v-if="preData && Object.keys(preData).length" class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Datos Pre-cargados
                                </h6>
                            </div>
                            <div class="card-body">
                                <div v-if="preData.mesa" class="alert alert-info">
                                    <strong>Mesa:</strong> Mesa {{ preData.mesa.numero_mesa }} - {{ preData.mesa.ubicacion }}
                                </div>
                                <div v-if="preData.pedido" class="alert alert-info">
                                    <strong>Pedido:</strong> Pedido #{{ preData.pedido.id_pedido }}
                                    <span v-if="preData.pedido.cliente">
                                        - {{ preData.pedido.cliente.nombre }} {{ preData.pedido.cliente.apellido }}
                                    </span>
                                    <span v-if="preData.pedido.mesa">
                                        (Mesa {{ preData.pedido.mesa.numero_mesa }})
                                    </span>
                                </div>
                                <div v-if="preData.reserva" class="alert alert-info">
                                    <strong>Reserva:</strong> Reserva #{{ preData.reserva.id_reserva }}
                                    <span v-if="preData.reserva.cliente">
                                        - {{ preData.reserva.cliente.nombre }} {{ preData.reserva.cliente.apellido }}
                                    </span>
                                    <span v-if="preData.reserva.mesa">
                                        (Mesa {{ preData.reserva.mesa.numero_mesa }})
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Asignación -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-person-gear me-2"></i>
                                    Asignación
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="usuario_asignado_id" class="form-label">Asignar a</label>
                                    <select id="usuario_asignado_id" 
                                            v-model="form.usuario_asignado_id" 
                                            class="form-select">
                                        <option value="">Sin asignar</option>
                                        <option v-for="usuario in usuarios" :key="usuario.id_usuario" :value="usuario.id_usuario">
                                            {{ usuario.nombre }} {{ usuario.apellido }} ({{ getRolNombre(usuario.id_rol) }})
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="fecha_limite" class="form-label">Fecha límite</label>
                                    <input type="datetime-local" 
                                           id="fecha_limite"
                                           v-model="form.fecha_limite" 
                                           class="form-control"
                                           :min="fechaMinima">
                                </div>

                                <div class="mb-3">
                                    <label for="canal_origen" class="form-label">Canal de origen</label>
                                    <select id="canal_origen" v-model="form.canal_origen" class="form-select">
                                        <option value="sistema">Sistema Web</option>
                                        <option value="telefono">Teléfono</option>
                                        <option value="email">Email</option>
                                        <option value="presencial">Presencial</option>
                                        <option value="chat">Chat</option>
                                    </select>
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" 
                                           id="es_interno"
                                           v-model="form.es_interno" 
                                           class="form-check-input">
                                    <label class="form-check-label" for="es_interno">
                                        Ticket interno (no visible para clientes)
                                    </label>
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
                                        <span v-if="processing">Guardando...</span>
                                        <span v-else>Crear Ticket</span>
                                    </button>
                                    <Link :href="route('tickets.index')" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Cancelar
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Guía de Prioridades -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-question-circle me-2"></i>
                                    Guía de Prioridades
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <span class="badge bg-danger">Crítica</span>
                                    <small class="d-block text-muted">Sistema inoperativo, pérdida de servicio total</small>
                                </div>
                                <div class="mb-2">
                                    <span class="badge bg-warning">Alta</span>
                                    <small class="d-block text-muted">Funcionalidad importante afectada</small>
                                </div>
                                <div class="mb-2">
                                    <span class="badge bg-info">Media</span>
                                    <small class="d-block text-muted">Problema que afecta parcialmente</small>
                                </div>
                                <div>
                                    <span class="badge bg-secondary">Baja</span>
                                    <small class="d-block text-muted">Mejoras, consultas, problemas menores</small>
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
        usuarios: Array,
        mesas: Array,
        pedidos: Array,
        reservas: Array,
        preData: Object,
        errors: Object
    },
    setup(props) {
        const processing = ref(false)
        const nuevaEtiqueta = ref('')

        const form = reactive({
            titulo: '',
            descripcion: '',
            categoria: '',
            prioridad: '',
            usuario_asignado_id: '',
            mesa_id: '',
            pedido_id: '',
            reserva_id: '',
            fecha_limite: '',
            etiquetas: [],
            es_interno: false,
            canal_origen: 'sistema'
        })

        // Fecha mínima (ahora)
        const fechaMinima = computed(() => {
            const now = new Date()
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset())
            return now.toISOString().slice(0, 16)
        })

        // Pre-cargar datos si vienen en props
        onMounted(() => {
            if (props.preData) {
                if (props.preData.mesa) {
                    form.mesa_id = props.preData.mesa.id_mesa
                }
                if (props.preData.pedido) {
                    form.pedido_id = props.preData.pedido.id_pedido
                    if (props.preData.pedido.mesa) {
                        form.mesa_id = props.preData.pedido.mesa.id_mesa
                    }
                }
                if (props.preData.reserva) {
                    form.reserva_id = props.preData.reserva.id_reserva
                    if (props.preData.reserva.mesa) {
                        form.mesa_id = props.preData.reserva.mesa.id_mesa
                    }
                }
            }
        })

        const guardarTicket = () => {
            processing.value = true
            
            router.post(route('tickets.store'), form, {
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

        return {
            form,
            processing,
            nuevaEtiqueta,
            fechaMinima,
            guardarTicket,
            agregarEtiqueta,
            eliminarEtiqueta,
            getRolNombre
        }
    }
}
</script>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}
</style>