<template>
    <AppLayout>
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h1 class="text-primary mb-0">
                        ¡Hola, {{ $page.props.auth.user.nombre }}!
                    </h1>
                    <p class="text-muted mb-0">Bienvenido a tu panel de cliente</p>
                </div>
                <div class="col-md-4 text-end">
                    <button @click="mostrarModalNuevaReserva = true" class="btn btn-primary me-2">
                        <i class="bi bi-calendar-plus me-1"></i>
                        Nueva Reserva
                    </button>
                    <button @click="mostrarCarrito = !mostrarCarrito" class="btn btn-outline-primary position-relative">
                        <i class="bi bi-basket me-1"></i>
                        Pedido
                        <span v-if="carrito.length > 0" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ carrito.length }}
                        </span>
                    </button>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="row mb-4" v-if="estadisticas">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-primary bg-gradient text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">Pedidos Este Mes</h6>
                                    <h3 class="mb-0">{{ estadisticas.pedidos_este_mes }}</h3>
                                </div>
                                <i class="bi bi-basket fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-success bg-gradient text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">Gasto Total</h6>
                                    <h3 class="mb-0">S/{{ formatNumber(estadisticas.gasto_total) }}</h3>
                                </div>
                                <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-warning bg-gradient text-dark">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">Reservas Activas</h6>
                                    <h3 class="mb-0">{{ estadisticas.reservas_activas }}</h3>
                                </div>
                                <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-info bg-gradient text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">Plato Favorito</h6>
                                    <h6 class="mb-0 text-truncate">{{ estadisticas.plato_favorito }}</h6>
                                </div>
                                <i class="bi bi-heart fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Menú de Productos -->
                <div class="col-xl-8">
                    <!-- Filtros de Menú -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-menu-app me-2"></i>
                                    Menú del Día
                                </h5>
                                <div class="btn-group btn-group-sm">
                                    <button @click="categoriaSeleccionada = null" 
                                            class="btn" 
                                            :class="categoriaSeleccionada === null ? 'btn-primary' : 'btn-outline-primary'">
                                        Todos
                                    </button>
                                    <button v-for="categoria in categorias" 
                                            :key="categoria.id"
                                            @click="categoriaSeleccionada = categoria.id" 
                                            class="btn" 
                                            :class="categoriaSeleccionada === categoria.id ? 'btn-primary' : 'btn-outline-primary'">
                                        {{ categoria.nombre }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div v-for="producto in productosFiltrados" 
                                     :key="producto.id" 
                                     class="col-lg-6 col-xl-4 mb-3">
                                    <div class="card h-100 producto-card">
                                        <img :src="producto.imagen" 
                                             class="card-img-top" 
                                             style="height: 200px; object-fit: cover;" 
                                             :alt="producto.nombre">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ producto.nombre }}</h6>
                                            <p class="card-text text-muted small">{{ producto.descripcion }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong class="text-primary fs-5">S/{{ formatNumber(producto.precio) }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="bi bi-clock me-1"></i>
                                                        {{ producto.tiempo_preparacion }} min
                                                    </small>
                                                </div>
                                                <div class="text-end">
                                                    <div class="text-warning mb-1">
                                                        <i v-for="n in 5" :key="n" 
                                                           :class="n <= Math.floor(producto.rating) ? 'bi bi-star-fill' : 'bi bi-star'"></i>
                                                        <small class="text-muted ms-1">({{ producto.rating }})</small>
                                                    </div>
                                                    <button @click="agregarAlCarrito(producto)" 
                                                            class="btn btn-primary btn-sm">
                                                        <i class="bi bi-plus-circle me-1"></i>
                                                        Agregar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-xl-4">
                    <!-- Carrito de Pedido -->
                    <div class="card mb-4" :class="{ 'border-primary': mostrarCarrito }">
                        <div class="card-header cursor-pointer" @click="mostrarCarrito = !mostrarCarrito">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">
                                    <i class="bi bi-basket me-2"></i>
                                    Tu Pedido ({{ carrito.length }})
                                </h6>
                                <i :class="mostrarCarrito ? 'bi bi-chevron-up' : 'bi bi-chevron-down'"></i>
                            </div>
                        </div>
                        <div v-show="mostrarCarrito" class="card-body">
                            <div v-if="carrito.length === 0" class="text-center text-muted py-4">
                                <i class="bi bi-basket3 fs-1 opacity-50 d-block"></i>
                                <p>Tu carrito está vacío</p>
                            </div>
                            <div v-else>
                                <div v-for="item in carrito" :key="item.id" class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ item.nombre }}</h6>
                                        <small class="text-muted">S/{{ formatNumber(item.precio) }} c/u</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <button @click="actualizarCantidad(item, -1)" 
                                                class="btn btn-outline-primary btn-sm me-2"
                                                :disabled="item.cantidad <= 1">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <span class="mx-2">{{ item.cantidad }}</span>
                                        <button @click="actualizarCantidad(item, 1)" 
                                                class="btn btn-outline-primary btn-sm me-2">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                        <button @click="quitarDelCarrito(item)" 
                                                class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Opciones del pedido -->
                                <hr>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input v-model="pedidoConfig.es_para_llevar" 
                                               type="checkbox" 
                                               class="form-check-input" 
                                               id="paraLlevar">
                                        <label class="form-check-label" for="paraLlevar">
                                            Para llevar
                                        </label>
                                    </div>
                                </div>
                                
                                <div v-if="!pedidoConfig.es_para_llevar" class="mb-3">
                                    <label class="form-label">Mesa:</label>
                                    <select v-model="pedidoConfig.mesa_id" class="form-select form-select-sm">
                                        <option value="">Cualquier mesa disponible</option>
                                        <option v-for="mesa in mesasDisponibles" 
                                                :key="mesa.id" 
                                                :value="mesa.id">
                                            Mesa #{{ mesa.numero }} ({{ mesa.capacidad }} personas) - {{ mesa.ubicacion }}
                                        </option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Observaciones:</label>
                                    <textarea v-model="pedidoConfig.observaciones" 
                                              class="form-control form-control-sm" 
                                              rows="2" 
                                              placeholder="Instrucciones especiales..."></textarea>
                                </div>

                                <!-- Total y Enviar -->
                                <div class="border-top pt-3">
                                    <div class="d-flex justify-content-between mb-3">
                                        <strong>Total:</strong>
                                        <strong class="text-primary fs-5">S/{{ formatNumber(totalCarrito) }}</strong>
                                    </div>
                                    <button @click="enviarPedido" 
                                            class="btn btn-primary w-100"
                                            :disabled="enviandoPedido">
                                        <div v-if="enviandoPedido" class="spinner-border spinner-border-sm me-2"></div>
                                        <i v-else class="bi bi-send me-1"></i>
                                        {{ enviandoPedido ? 'Enviando...' : 'Enviar Pedido' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mis Reservas -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-calendar me-2"></i>
                                Mis Reservas
                            </h6>
                        </div>
                        <div class="card-body">
                            <div v-if="reservas.length === 0" class="text-center text-muted">
                                <i class="bi bi-calendar-x fs-1 opacity-50 d-block"></i>
                                <p>No tienes reservas</p>
                                <button @click="mostrarModalNuevaReserva = true" class="btn btn-primary btn-sm">
                                    Hacer Reserva
                                </button>
                            </div>
                            <div v-else>
                                <div v-for="reserva in reservas" :key="reserva.id" class="mb-3 p-3 border rounded">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-0">{{ reserva.fecha }}</h6>
                                            <small class="text-muted">{{ reserva.hora_inicio }} - {{ reserva.hora_fin }}</small>
                                        </div>
                                        <span :class="'badge bg-' + reserva.estadoColor">
                                            {{ reserva.estado }}
                                        </span>
                                    </div>
                                    <p class="mb-1">
                                        <i class="bi bi-table me-1"></i>
                                        {{ reserva.mesa }}
                                        <span class="ms-2">
                                            <i class="bi bi-people me-1"></i>
                                            {{ reserva.personas }} personas
                                        </span>
                                    </p>
                                    <div v-if="reserva.puede_cancelar" class="text-end">
                                        <button @click="cancelarReserva(reserva)" 
                                                class="btn btn-outline-danger btn-sm">
                                            Cancelar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historial de Pedidos -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-clock-history me-2"></i>
                                Pedidos Recientes
                            </h6>
                        </div>
                        <div class="card-body">
                            <div v-if="historialPedidos.length === 0" class="text-center text-muted">
                                <p>No tienes pedidos anteriores</p>
                            </div>
                            <div v-else>
                                <div v-for="pedido in historialPedidos" :key="pedido.id" class="mb-3 p-3 border rounded">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-0">Pedido #{{ pedido.id }}</h6>
                                            <small class="text-muted">{{ pedido.fecha }}</small>
                                        </div>
                                        <span :class="'badge bg-' + pedido.estadoColor">
                                            {{ pedido.estado }}
                                        </span>
                                    </div>
                                    <p class="mb-1">{{ pedido.descripcion }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-primary">S/{{ pedido.total }}</strong>
                                        <button @click="repetirPedido(pedido)" 
                                                class="btn btn-outline-primary btn-sm">
                                            Repetir
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Nueva Reserva -->
        <div class="modal fade" 
             :class="{ show: mostrarModalNuevaReserva }" 
             :style="{ display: mostrarModalNuevaReserva ? 'block' : 'none' }"
             tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nueva Reserva</h5>
                        <button type="button" class="btn-close" @click="mostrarModalNuevaReserva = false"></button>
                    </div>
                    <form @submit.prevent="guardarReserva">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha *</label>
                                    <input v-model="nuevaReserva.fecha_reserva" 
                                           type="date" 
                                           class="form-control" 
                                           :min="fechaMinima"
                                           required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Número de Personas *</label>
                                    <select v-model="nuevaReserva.numero_personas" class="form-select" required>
                                        <option value="">Seleccionar...</option>
                                        <option v-for="n in 12" :key="n" :value="n">{{ n }} persona{{ n > 1 ? 's' : '' }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hora de Inicio *</label>
                                    <input v-model="nuevaReserva.hora_inicio" 
                                           type="time" 
                                           class="form-control" 
                                           required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hora de Fin *</label>
                                    <input v-model="nuevaReserva.hora_fin" 
                                           type="time" 
                                           class="form-control" 
                                           required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Mesa Preferida</label>
                                    <select v-model="nuevaReserva.mesa_id" class="form-select">
                                        <option value="">Cualquier mesa disponible</option>
                                        <option v-for="mesa in mesasDisponibles" 
                                                :key="mesa.id" 
                                                :value="mesa.id">
                                            Mesa #{{ mesa.numero }} ({{ mesa.capacidad }} personas) - {{ mesa.ubicacion }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Observaciones</label>
                                    <textarea v-model="nuevaReserva.observaciones" 
                                              class="form-control" 
                                              rows="3" 
                                              placeholder="Ocasión especial, preferencias, alergias..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="mostrarModalNuevaReserva = false">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="guardandoReserva">
                                <div v-if="guardandoReserva" class="spinner-border spinner-border-sm me-2"></div>
                                {{ guardandoReserva ? 'Guardando...' : 'Crear Reserva' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div v-if="mostrarModalNuevaReserva" class="modal-backdrop fade show"></div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Props
const props = defineProps({
    productos: Array,
    mesasDisponibles: Array,
    reservas: Array,
    historialPedidos: Array,
    estadisticas: Object
})

// Estado reactivo
const categoriaSeleccionada = ref(null)
const carrito = ref([])
const mostrarCarrito = ref(true)
const mostrarModalNuevaReserva = ref(false)
const enviandoPedido = ref(false)
const guardandoReserva = ref(false)

// Configuración del pedido
const pedidoConfig = ref({
    es_para_llevar: false,
    mesa_id: '',
    observaciones: ''
})

// Nueva reserva
const nuevaReserva = ref({
    fecha_reserva: '',
    numero_personas: '',
    hora_inicio: '',
    hora_fin: '',
    mesa_id: '',
    observaciones: ''
})

// Computed
const categorias = computed(() => {
    const categoriasUnicas = [...new Set(props.productos.map(p => p.categoria))]
    return categoriasUnicas.map(id => ({
        id: id,
        nombre: getCategoriaName(id)
    }))
})

const productosFiltrados = computed(() => {
    if (categoriaSeleccionada.value === null) {
        return props.productos
    }
    return props.productos.filter(p => p.categoria === categoriaSeleccionada.value)
})

const totalCarrito = computed(() => {
    return carrito.value.reduce((total, item) => total + (item.precio * item.cantidad), 0)
})

const fechaMinima = computed(() => {
    const hoy = new Date()
    return hoy.toISOString().split('T')[0]
})

// Métodos
function getCategoriaName(id) {
    const nombres = {
        1: 'Entradas',
        2: 'Principales',
        3: 'Bebidas',
        4: 'Postres',
        5: 'Ensaladas',
        6: 'Sopas'
    }
    return nombres[id] || 'Otros'
}

function agregarAlCarrito(producto) {
    const itemExistente = carrito.value.find(item => item.id === producto.id)
    
    if (itemExistente) {
        itemExistente.cantidad++
    } else {
        carrito.value.push({
            id: producto.id,
            nombre: producto.nombre,
            precio: producto.precio,
            cantidad: 1
        })
    }
    
    mostrarCarrito.value = true
}

function actualizarCantidad(item, cambio) {
    const nuevaCantidad = item.cantidad + cambio
    if (nuevaCantidad > 0) {
        item.cantidad = nuevaCantidad
    }
}

function quitarDelCarrito(item) {
    const index = carrito.value.findIndex(i => i.id === item.id)
    if (index !== -1) {
        carrito.value.splice(index, 1)
    }
}

function enviarPedido() {
    if (carrito.value.length === 0) {
        alert('Tu carrito está vacío')
        return
    }
    
    enviandoPedido.value = true
    
    const datosEnvio = {
        items: carrito.value,
        total: totalCarrito.value,
        es_para_llevar: pedidoConfig.value.es_para_llevar,
        mesa_id: pedidoConfig.value.mesa_id,
        observaciones_generales: pedidoConfig.value.observaciones
    }
    
    router.post(route('cliente.enviar-pedido'), datosEnvio, {
        onSuccess: () => {
            carrito.value = []
            pedidoConfig.value = { es_para_llevar: false, mesa_id: '', observaciones: '' }
            mostrarCarrito.value = false
        },
        onFinish: () => {
            enviandoPedido.value = false
        }
    })
}

function guardarReserva() {
    guardandoReserva.value = true
    
    router.post(route('cliente.guardar-reserva'), nuevaReserva.value, {
        onSuccess: () => {
            mostrarModalNuevaReserva.value = false
            nuevaReserva.value = {
                fecha_reserva: '',
                numero_personas: '',
                hora_inicio: '',
                hora_fin: '',
                mesa_id: '',
                observaciones: ''
            }
        },
        onFinish: () => {
            guardandoReserva.value = false
        }
    })
}

function cancelarReserva(reserva) {
    if (confirm('¿Estás seguro de cancelar esta reserva?')) {
        router.delete(route('cliente.cancelar-reserva', reserva.id))
    }
}

function repetirPedido(pedido) {
    if (confirm('¿Agregar este pedido a tu carrito actual?')) {
        router.post(route('cliente.repetir-pedido'), {
            pedido_id: pedido.id
        })
    }
}

function formatNumber(number) {
    return Number(number).toFixed(2)
}
</script>

<style scoped>
.producto-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.producto-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.cursor-pointer {
    cursor: pointer;
}

.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
    border: none;
}

.bg-gradient {
    background: linear-gradient(45deg, var(--bs-bg-opacity, 1), rgba(255,255,255,0.15)) !important;
}

.modal.show {
    display: block !important;
}

.border-primary {
    border-color: #0d6efd !important;
    border-width: 2px !important;
}

.text-truncate {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
</style>
