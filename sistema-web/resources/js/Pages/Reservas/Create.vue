<template>
    <AppLayout>
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2 class="text-primary mb-0">
                        <i class="bi bi-calendar-plus me-2"></i>
                        {{ reserva?.id_reserva ? 'Editar Reserva' : 'Nueva Reserva' }}
                    </h2>
                </div>
                <div class="col-md-4 text-end">
                    <Link :href="route('reservas.index')" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>
                        Volver al Listado
                    </Link>
                </div>
            </div>

            <form @submit.prevent="guardarReserva" class="row">
                <!-- Formulario Principal -->
                <div class="col-xl-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-person-plus me-2"></i>
                                Información del Cliente
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Búsqueda/Selección de Cliente -->
                                <div class="col-12 mb-3">
                                    <label class="form-label">Cliente <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select v-model="form.id_cliente" 
                                                class="form-select" 
                                                :class="{ 'is-invalid': errors.id_cliente }"
                                                required>
                                            <option value="">Seleccionar cliente...</option>
                                            <option v-for="cliente in clientes"
                                                    :key="cliente.id_usuario"
                                                    :value="cliente.id_usuario">
                                                {{ cliente.nombre }} {{ cliente.apellido }} - {{ cliente.email }}
                                            </option>
                                        </select>
                                        <button type="button" class="btn btn-outline-primary" @click="mostrarModalCliente = true">
                                            <i class="bi bi-plus"></i>
                                            Nuevo
                                        </button>
                                    </div>
                                    <div v-if="errors.id_cliente" class="invalid-feedback">{{ errors.id_cliente }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-calendar-event me-2"></i>
                                Detalles de la Reserva
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Fecha -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                    <input v-model="form.fecha_reserva" 
                                           type="date" 
                                           class="form-control" 
                                           :class="{ 'is-invalid': errors.fecha_reserva }"
                                           :min="fechaMinima"
                                           @change="verificarDisponibilidad"
                                           required>
                                    <div v-if="errors.fecha_reserva" class="invalid-feedback">{{ errors.fecha_reserva }}</div>
                                </div>

                                <!-- Número de Personas -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Número de Personas <span class="text-danger">*</span></label>
                                    <input v-model.number="form.numero_personas" 
                                           type="number" 
                                           class="form-control" 
                                           :class="{ 'is-invalid': errors.numero_personas }"
                                           min="1" 
                                           max="20"
                                           @input="verificarDisponibilidad"
                                           required>
                                    <div v-if="errors.numero_personas" class="invalid-feedback">{{ errors.numero_personas }}</div>
                                </div>

                                <!-- Hora Inicio -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hora de Inicio <span class="text-danger">*</span></label>
                                    <input v-model="form.hora_inicio" 
                                           type="time" 
                                           class="form-control" 
                                           :class="{ 'is-invalid': errors.hora_inicio }"
                                           @change="calcularHoraFin"
                                           required>
                                    <div v-if="errors.hora_inicio" class="invalid-feedback">{{ errors.hora_inicio }}</div>
                                </div>

                                <!-- Hora Fin -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hora de Fin <span class="text-danger">*</span></label>
                                    <input v-model="form.hora_fin" 
                                           type="time" 
                                           class="form-control" 
                                           :class="{ 'is-invalid': errors.hora_fin }"
                                           required>
                                    <div v-if="errors.hora_fin" class="invalid-feedback">{{ errors.hora_fin }}</div>
                                    <div v-if="duracionReserva" class="form-text">
                                        Duración: {{ duracionReserva }}
                                    </div>
                                </div>

                                <!-- Mesa -->
                                <div class="col-12 mb-3">
                                    <label class="form-label">Mesa <span class="text-danger">*</span></label>
                                    <select v-model="form.id_mesa"
                                            class="form-select"
                                            :class="{ 'is-invalid': errors.id_mesa }"
                                            required>
                                        <option value="">Seleccionar mesa...</option>
                                        <optgroup v-if="mesasDisponibles.length > 0" label="Mesas Disponibles">
                                            <option v-for="mesa in mesasDisponibles" :key="mesa.id_mesa" :value="mesa.id_mesa">
                                                {{ `Mesa #${mesa.numero_mesa} (${mesa.capacidad} personas) - ${(mesa.ubicacion || 'Salón principal')}` }}
                                            </option>
                                        </optgroup>
                                        <optgroup v-if="mesasOcupadas.length > 0" label="Mesas No Disponibles" disabled>
                                            <option v-for="mesa in mesasOcupadas" :key="mesa.id_mesa" :value="mesa.id_mesa" disabled>
                                                {{ `Mesa #${mesa.numero_mesa} (${mesa.capacidad} personas) - Ocupada` }}
                                            </option>
                                        </optgroup>
                                    </select>
                                    <div v-if="errors.id_mesa" class="invalid-feedback">{{ errors.id_mesa }}</div>
                                    <div v-if="!verificandoDisponibilidad && form.fecha_reserva && form.numero_personas && mesasDisponibles.length === 0" 
                                         class="form-text text-danger">
                                        No hay mesas disponibles para esta fecha y número de personas
                                    </div>
                                </div>

                                <!-- Observaciones -->
                                <div class="col-12">
                                    <label class="form-label">Observaciones</label>
                                    <textarea v-model="form.observaciones" 
                                              class="form-control" 
                                              :class="{ 'is-invalid': errors.observaciones }"
                                              rows="3"
                                              placeholder="Comentarios especiales, alergias, preferencias..."></textarea>
                                    <div v-if="errors.observaciones" class="invalid-feedback">{{ errors.observaciones }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-xl-4">
                    <!-- Resumen -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Resumen de la Reserva</h6>
                        </div>
                        <div class="card-body">
                            <div v-if="!clienteSeleccionado" class="text-muted text-center">
                                <i class="bi bi-person-x fs-1 opacity-50"></i>
                                <p>Seleccione un cliente para ver el resumen</p>
                            </div>
                            <div v-else>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="text-muted">Cliente:</td>
                                        <td>{{ clienteSeleccionado.nombre }} {{ clienteSeleccionado.apellido }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Fecha:</td>
                                        <td>{{ form.fecha_reserva ? formatFecha(form.fecha_reserva) : 'No seleccionada' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Horario:</td>
                                        <td>
                                            {{ form.hora_inicio || '--:--' }} - {{ form.hora_fin || '--:--' }}
                                            <br>
                                            <small class="text-muted" v-if="duracionReserva">{{ duracionReserva }}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Personas:</td>
                                        <td>{{ form.numero_personas || 0 }} personas</td>
                                    </tr>
                                    <tr v-if="mesaSeleccionada">
                                        <td class="text-muted">Mesa:</td>
                                        <td>
                                            Mesa #{{ mesaSeleccionada.numero_mesa }}
                                            <br>
                                            <small class="text-muted">{{ mesaSeleccionada.ubicacion || 'Salón principal' }}</small>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Estado de Disponibilidad -->
                    <div class="card mb-4" v-if="form.fecha_reserva && form.hora_inicio && form.hora_fin">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Disponibilidad</h6>
                        </div>
                        <div class="card-body">
                            <div v-if="verificandoDisponibilidad" class="text-center">
                                <div class="spinner-border spinner-border-sm me-2"></div>
                                Verificando disponibilidad...
                            </div>
                            <div v-else-if="mesasDisponibles.length > 0" class="text-success">
                                <i class="bi bi-check-circle me-2"></i>
                                {{ mesasDisponibles.length }} mesa(s) disponible(s)
                            </div>
                            <div v-else class="text-danger">
                                <i class="bi bi-x-circle me-2"></i>
                                No hay mesas disponibles
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" 
                                        class="btn btn-primary btn-lg"
                                        :disabled="procesando || !puedeGuardar">
                                    <div v-if="procesando" class="spinner-border spinner-border-sm me-2"></div>
                                    <i v-else class="bi bi-check me-2"></i>
                                    {{ reserva?.id_reserva ? 'Actualizar Reserva' : 'Crear Reserva' }}
                                </button>
                                
                                <Link :href="route('reservas.index')" class="btn btn-outline-secondary">
                                    <i class="bi bi-x me-1"></i>
                                    Cancelar
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Modal Nuevo Cliente -->
        <div class="modal fade" 
             :class="{ show: mostrarModalCliente }" 
             :style="{ display: mostrarModalCliente ? 'block' : 'none' }"
             tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nuevo Cliente</h5>
                        <button type="button" class="btn-close" @click="mostrarModalCliente = false"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="crearCliente">
                            <div class="mb-3">
                                <label class="form-label">Nombre *</label>
                                <input v-model="nuevoCliente.nombre" type="text" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Apellido *</label>
                                <input v-model="nuevoCliente.apellido" type="text" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input v-model="nuevoCliente.email" type="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input v-model="nuevoCliente.telefono" type="tel" class="form-control">
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary me-2" @click="mostrarModalCliente = false">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary" :disabled="creandoCliente">
                                    <div v-if="creandoCliente" class="spinner-border spinner-border-sm me-2"></div>
                                    Crear Cliente
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="mostrarModalCliente" class="modal-backdrop fade show"></div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Props
const props = defineProps({
    reserva: Object,
    clientes: Array,
    mesas: Array,
    errors: Object
})

// Estado reactivo
const mostrarModalCliente = ref(false)
const verificandoDisponibilidad = ref(false)
const procesando = ref(false)
const creandoCliente = ref(false)
const mesasDisponibles = ref([])
const mesasOcupadas = ref([])

// Formularios
const form = useForm({
    id_cliente: props.reserva?.id_cliente || '',
    fecha_reserva: props.reserva?.fecha_reserva || '',
    hora_inicio: props.reserva?.hora_inicio || '',
    hora_fin: props.reserva?.hora_fin || '',
    numero_personas: props.reserva?.numero_personas || 2,
    id_mesa: props.reserva?.id_mesa || '',
    observaciones: props.reserva?.observaciones || ''
})

const nuevoCliente = ref({
    nombre: '',
    apellido: '',
    email: '',
    telefono: ''
})

// Computed
const fechaMinima = computed(() => {
    const hoy = new Date()
    return hoy.toISOString().split('T')[0]
})

const clienteSeleccionado = computed(() => {
    // Si solo hay un cliente (modo cliente), usarlo automáticamente
    if (props.clientes && props.clientes.length === 1) {
        if (!form.id_cliente) form.id_cliente = props.clientes[0].id_usuario;
        return props.clientes[0];
    }
    return props.clientes.find(cliente => cliente.id_usuario == form.id_cliente);
})

const mesaSeleccionada = computed(() => {
    return [...mesasDisponibles.value, ...mesasOcupadas.value].find(mesa => mesa.id_mesa == form.id_mesa)
})

const duracionReserva = computed(() => {
    if (!form.hora_inicio || !form.hora_fin) return null
    
    const inicio = new Date(`2000-01-01 ${form.hora_inicio}`)
    const fin = new Date(`2000-01-01 ${form.hora_fin}`)
    const duracion = (fin - inicio) / (1000 * 60) // minutos
    
    if (duracion <= 0) return 'Horario inválido'
    
    if (duracion >= 60) {
        const horas = Math.floor(duracion / 60)
        const minutos = duracion % 60
        return `${horas}h ${minutos > 0 ? minutos + 'm' : ''}`
    }
    return `${duracion}m`
})

const puedeGuardar = computed(() => {
    return form.id_cliente && form.fecha_reserva && form.hora_inicio &&
           form.hora_fin && form.numero_personas && form.id_mesa &&
           mesasDisponibles.value.some(mesa => mesa.id_mesa == form.id_mesa)
})

// Watchers
watch([() => form.fecha_reserva, () => form.hora_inicio, () => form.hora_fin, () => form.numero_personas], () => {
    if (form.fecha_reserva && form.hora_inicio && form.hora_fin && form.numero_personas) {
        verificarDisponibilidad()
    }
})

// Métodos
function calcularHoraFin() {
    if (!form.hora_inicio) return
    
    const inicio = new Date(`2000-01-01 ${form.hora_inicio}`)
    inicio.setHours(inicio.getHours() + 2) // Duración predeterminada de 2 horas
    
    const horas = inicio.getHours().toString().padStart(2, '0')
    const minutos = inicio.getMinutes().toString().padStart(2, '0')
    form.hora_fin = `${horas}:${minutos}`
}

async function verificarDisponibilidad() {
    if (!form.fecha_reserva || !form.hora_inicio || !form.hora_fin || !form.numero_personas) {
        return
    }
    
    verificandoDisponibilidad.value = true
    
    try {
        const response = await axios.get(route('api.reservas.disponibilidad'), {
            params: {
                fecha: form.fecha_reserva,
                hora_inicio: form.hora_inicio,
                hora_fin: form.hora_fin,
                numero_personas: form.numero_personas,
                reserva_id: props.reserva?.id_reserva
            }
        })
        
        mesasDisponibles.value = response.data.disponibles || []
        mesasOcupadas.value = response.data.ocupadas || []
        
        // Si la mesa actual no está disponible, resetear selección
        if (form.id_mesa && !mesasDisponibles.value.some(mesa => mesa.id_mesa == form.id_mesa)) {
            form.id_mesa = ''
        }
    } catch (error) {
        console.error('Error al verificar disponibilidad:', error)
        mesasDisponibles.value = []
        mesasOcupadas.value = []
    } finally {
        verificandoDisponibilidad.value = false
    }
}

function guardarReserva() {
    procesando.value = true
    
    const url = props.reserva?.id_reserva 
        ? route('reservas.update', props.reserva.id_reserva)
        : route('reservas.store')
    
    const method = props.reserva?.id_reserva ? 'put' : 'post'
    
    form[method](url, {
        onFinish: () => {
            procesando.value = false
        }
    })
}

async function crearCliente() {
    creandoCliente.value = true
    
    try {
        const response = await axios.post(route('clientes.store'), nuevoCliente.value)
        
        // Agregar nuevo cliente a la lista y seleccionarlo
        props.clientes.push(response.data.cliente)
        form.id_cliente = response.data.cliente.id_usuario
        
        // Limpiar formulario y cerrar modal
        nuevoCliente.value = { nombre: '', apellido: '', email: '', telefono: '' }
        mostrarModalCliente.value = false
        
    } catch (error) {
        console.error('Error al crear cliente:', error)
        alert('Error al crear el cliente')
    } finally {
        creandoCliente.value = false
    }
}

function formatFecha(fecha) {
    return new Date(fecha).toLocaleDateString('es-PE', {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    })
}

// Inicialización
onMounted(() => {
    if (props.reserva?.id_reserva) {
        verificarDisponibilidad()
    }
})
</script>

<style scoped>
.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
    border: none;
}

.table-borderless td {
    padding: 0.25rem 0;
}

.modal.show {
    display: block !important;
}

.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
}
</style>