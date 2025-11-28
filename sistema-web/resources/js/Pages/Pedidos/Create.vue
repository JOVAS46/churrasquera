<template>
    <AppLayout>
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2 class="text-primary mb-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        Nuevo Pedido
                    </h2>
                    <p class="text-muted">Crear un nuevo pedido para el restaurante</p>
                </div>
                <div class="col-md-4 text-end">
                    <Link :href="route('pedidos.index')" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>
                        Volver
                    </Link>
                </div>
            </div>

            <form @submit.prevent="submit">
                <div class="row">
                    <!-- Información del pedido -->
                    <div class="col-lg-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Información del Pedido
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Mesa -->
                                <div class="mb-3">
                                    <label class="form-label">Mesa <span class="text-danger">*</span></label>
                                    <select v-model="form.id_mesa" 
                                            class="form-select" 
                                            :class="{ 'is-invalid': errors.id_mesa }"
                                            required>
                                        <option value="">Seleccionar mesa</option>
                                        <option v-for="mesa in mesas" 
                                                :key="mesa.id_mesa" 
                                                :value="mesa.id_mesa">
                                            Mesa #{{ mesa.numero_mesa }} 
                                            ({{ mesa.capacidad }} personas)
                                            <span v-if="mesa.mesero"> - {{ mesa.mesero.nombre }}</span>
                                        </option>
                                    </select>
                                    <div v-if="errors.id_mesa" class="invalid-feedback">
                                        {{ errors.id_mesa }}
                                    </div>
                                </div>

                                <!-- Mesero -->
                                <div class="mb-3">
                                    <label class="form-label">Mesero <span class="text-danger">*</span></label>
                                    <select v-model="form.id_mesero" 
                                            class="form-select" 
                                            :class="{ 'is-invalid': errors.id_mesero }"
                                            required>
                                        <option value="">Seleccionar mesero</option>
                                        <option v-for="mesero in meseros" 
                                                :key="mesero.id_usuario" 
                                                :value="mesero.id_usuario">
                                            {{ mesero.nombre }} {{ mesero.apellido }}
                                        </option>
                                    </select>
                                    <div v-if="errors.id_mesero" class="invalid-feedback">
                                        {{ errors.id_mesero }}
                                    </div>
                                </div>

                                <!-- Observaciones -->
                                <div class="mb-3">
                                    <label class="form-label">Observaciones</label>
                                    <textarea v-model="form.observaciones" 
                                              class="form-control" 
                                              :class="{ 'is-invalid': errors.observaciones }"
                                              rows="3" 
                                              placeholder="Observaciones especiales del pedido...">
                                    </textarea>
                                    <div v-if="errors.observaciones" class="invalid-feedback">
                                        {{ errors.observaciones }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Resumen del pedido -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-calculator me-2"></i>
                                    Resumen
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Items:</span>
                                    <span>{{ totalItems }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Subtotal:</span>
                                    <span>S/ {{ subtotal.toFixed(2) }}</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Total:</strong>
                                    <strong class="text-primary fs-5">S/ {{ subtotal.toFixed(2) }}</strong>
                                </div>
                                
                                <button type="submit" 
                                        class="btn btn-primary w-100"
                                        :disabled="!puedeCrearPedido || processing">
                                    <i class="bi bi-check-circle me-1"></i>
                                    {{ processing ? 'Creando...' : 'Crear Pedido' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Productos y carrito -->
                    <div class="col-lg-8">
                        <!-- Productos disponibles -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h5 class="mb-0">
                                            <i class="bi bi-grid me-2"></i>
                                            Productos Disponibles
                                        </h5>
                                    </div>
                                    <div class="col-md-6">
                                        <input v-model="busquedaProducto" 
                                               type="text" 
                                               class="form-control" 
                                               placeholder="Buscar producto...">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                <div v-for="(productos, categoria) in productosFiltrados" :key="categoria">
                                    <h6 class="text-muted mt-3 mb-2">{{ categoria }}</h6>
                                    <div class="row">
                                        <div v-for="producto in productos" 
                                             :key="producto.id_producto" 
                                             class="col-md-6 mb-2">
                                            <div class="card card-producto h-100" 
                                                 @click="agregarProducto(producto)">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div class="flex-grow-1">
                                                            <h6 class="card-title mb-1">{{ producto.nombre }}</h6>
                                                            <small class="text-muted">{{ producto.descripcion }}</small>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="fw-bold text-primary">
                                                                S/ {{ producto.precio }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Carrito de pedido -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-cart me-2"></i>
                                    Items del Pedido
                                    <span v-if="form.items.length > 0" class="badge bg-primary ms-2">
                                        {{ form.items.length }}
                                    </span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div v-if="form.items.length === 0" class="text-center py-5 text-muted">
                                    <i class="bi bi-cart-x fs-1 d-block mb-2"></i>
                                    <p>No hay items en el pedido</p>
                                    <small>Haz clic en los productos de arriba para agregarlos</small>
                                </div>
                                
                                <div v-else class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th width="80">Cant.</th>
                                                <th width="100">P. Unit.</th>
                                                <th width="100">Subtotal</th>
                                                <th width="200">Observaciones</th>
                                                <th width="50"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in form.items" 
                                                :key="index"
                                                class="align-middle">
                                                <td>
                                                    <strong>{{ getProductoNombre(item.id_producto) }}</strong>
                                                </td>
                                                <td>
                                                    <input v-model.number="item.cantidad" 
                                                           type="number" 
                                                           min="1" 
                                                           class="form-control form-control-sm"
                                                           @change="actualizarSubtotal(item)">
                                                </td>
                                                <td>
                                                    <input v-model.number="item.precio_unitario" 
                                                           type="number" 
                                                           step="0.01" 
                                                           min="0" 
                                                           class="form-control form-control-sm"
                                                           @change="actualizarSubtotal(item)">
                                                </td>
                                                <td class="fw-bold">
                                                    S/ {{ (item.cantidad * item.precio_unitario).toFixed(2) }}
                                                </td>
                                                <td>
                                                    <input v-model="item.observaciones" 
                                                           type="text" 
                                                           class="form-control form-control-sm"
                                                           placeholder="Observaciones...">
                                                </td>
                                                <td>
                                                    <button @click="eliminarItem(index)" 
                                                            type="button" 
                                                            class="btn btn-outline-danger btn-sm">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Props
const props = defineProps({
    mesas: Array,
    productos: Object,
    meseros: Array,
    cocineros: Array,
    mesaSeleccionada: Object,
    errors: Object
})

// Estado reactivo
const busquedaProducto = ref('')
const processing = ref(false)

// Formulario
const form = useForm({
    id_mesa: props.mesaSeleccionada?.id_mesa || '',
    id_mesero: props.mesaSeleccionada?.id_mesero || '',
    observaciones: '',
    items: []
})

// Computed properties
const productosFiltrados = computed(() => {
    if (!busquedaProducto.value) return props.productos
    
    const termino = busquedaProducto.value.toLowerCase()
    const resultado = {}
    
    Object.keys(props.productos).forEach(categoria => {
        const productosFiltrados = props.productos[categoria].filter(producto =>
            producto.nombre.toLowerCase().includes(termino) ||
            (producto.descripcion && producto.descripcion.toLowerCase().includes(termino))
        )
        
        if (productosFiltrados.length > 0) {
            resultado[categoria] = productosFiltrados
        }
    })
    
    return resultado
})

const subtotal = computed(() => {
    return form.items.reduce((total, item) => {
        return total + (item.cantidad * item.precio_unitario)
    }, 0)
})

const totalItems = computed(() => {
    return form.items.reduce((total, item) => total + item.cantidad, 0)
})

const puedeCrearPedido = computed(() => {
    return form.id_mesa && form.id_mesero && form.items.length > 0
})

// Métodos
function agregarProducto(producto) {
    // Verificar si el producto ya está en el carrito
    const itemExistente = form.items.find(item => item.id_producto === producto.id_producto)
    
    if (itemExistente) {
        itemExistente.cantidad += 1
        actualizarSubtotal(itemExistente)
    } else {
        form.items.push({
            id_producto: producto.id_producto,
            cantidad: 1,
            precio_unitario: parseFloat(producto.precio || 0),
            observaciones: ''
        })
    }
}

function eliminarItem(index) {
    form.items.splice(index, 1)
}

function actualizarSubtotal(item) {
    // No necesitamos hacer nada aquí ya que el subtotal se calcula automáticamente
    // en el template usando item.cantidad * item.precio_unitario
}

function getProductoNombre(idProducto) {
    for (const categoria in props.productos) {
        const producto = props.productos[categoria].find(p => p.id_producto === idProducto)
        if (producto) return producto.nombre
    }
    return 'Producto desconocido'
}

function submit() {
    console.log('🚀 Intentando crear pedido...')
    console.log('Mesa:', form.id_mesa)
    console.log('Mesero:', form.id_mesero)
    console.log('Items:', form.items)
    console.log('Puede crear:', puedeCrearPedido.value)

    if (!puedeCrearPedido.value) {
        alert('❌ Faltan datos:\n- Mesa: ' + (form.id_mesa ? '✓' : '✗') + '\n- Mesero: ' + (form.id_mesero ? '✓' : '✗') + '\n- Productos: ' + form.items.length)
        return
    }

    processing.value = true

    console.log('📤 Enviando a:', route('pedidos.store'))
    console.log('📦 Datos:', JSON.stringify(form.data(), null, 2))

    form.post(route('pedidos.store'), {
        onSuccess: (response) => {
            console.log('✅ Pedido creado!', response)
            processing.value = false
            alert('✅ Pedido creado exitosamente!')
        },
        onError: (errors) => {
            console.error('❌ Errores:', errors)
            processing.value = false
            alert('❌ Error al crear pedido. Revisa la consola (F12)')
        }
    })
}
</script>

<style scoped>
.card-producto {
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid #e3e6f0;
}

.card-producto:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-color: #5a5af4;
}

.table th {
    border-top: none;
    background-color: #f8f9fc;
}

.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
    border: none;
}

.form-control:focus, .form-select:focus {
    border-color: #5a5af4;
    box-shadow: 0 0 0 0.2rem rgba(90, 90, 244, 0.25);
}

.btn-primary {
    background-color: #5a5af4;
    border-color: #5a5af4;
}

.btn-primary:hover {
    background-color: #4a4af4;
    border-color: #4a4af4;
}
</style>