<template>
    <AppLayout>
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2 class="text-primary mb-0">
                        <i class="bi bi-box-seam me-2"></i>
                        Gestión de Productos
                    </h2>
                    <p class="text-muted">Administra el catálogo de productos del restaurante</p>
                </div>
                <div class="col-md-4 text-end">
                    <Link :href="route('productos.create')" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Nuevo Producto
                    </Link>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Buscar</label>
                            <input v-model="filtros.buscar"
                                   type="text"
                                   class="form-control"
                                   placeholder="Nombre o descripción..."
                                   @input="aplicarFiltros">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Categoría</label>
                            <select v-model="filtros.id_categoria"
                                    class="form-select"
                                    @change="aplicarFiltros">
                                <option value="">Todas las categorías</option>
                                <option v-for="categoria in categorias"
                                        :key="categoria.id_categoria"
                                        :value="categoria.id_categoria">
                                    {{ categoria.nombre }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Disponibilidad</label>
                            <select v-model="filtros.disponible"
                                    class="form-select"
                                    @change="aplicarFiltros">
                                <option value="">Todos</option>
                                <option value="1">Disponibles</option>
                                <option value="0">No disponibles</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button @click="limpiarFiltros" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-x-circle me-1"></i>
                                Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Productos -->
            <div class="row">
                <div v-if="productos.data.length === 0" class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle me-2"></i>
                        No se encontraron productos
                    </div>
                </div>

                <div v-for="producto in productos.data"
                     :key="producto.id_producto"
                     class="col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card h-100 producto-card">
                        <!-- Imagen del producto -->
                        <div class="producto-imagen-container">
                            <img v-if="producto.imagen"
                                 :src="`/storage/${producto.imagen}`"
                                 class="card-img-top producto-imagen"
                                 :alt="producto.nombre">
                            <div v-else class="producto-sin-imagen">
                                <i class="bi bi-image"></i>
                                <p>Sin imagen</p>
                            </div>
                            <!-- Badge de disponibilidad -->
                            <span class="badge position-absolute top-0 end-0 m-2"
                                  :class="producto.disponible ? 'bg-success' : 'bg-danger'">
                                {{ producto.disponible ? 'Disponible' : 'No disponible' }}
                            </span>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ producto.nombre }}</h5>
                            <p class="card-text text-muted small flex-grow-1">
                                {{ producto.descripcion || 'Sin descripción' }}
                            </p>

                            <div class="mb-2">
                                <span class="badge bg-primary">
                                    {{ producto.categoria?.nombre || 'Sin categoría' }}
                                </span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="text-success mb-0">
                                    Bs. {{ formatoPrecio(producto.precio) }}
                                </h4>
                                <small class="text-muted" v-if="producto.tiempo_preparacion">
                                    <i class="bi bi-clock"></i> {{ producto.tiempo_preparacion }} min
                                </small>
                            </div>

                            <!-- Acciones -->
                            <div class="btn-group" role="group">
                                <Link :href="route('productos.edit', producto.id_producto)"
                                      class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </Link>
                                <button @click="toggleDisponibilidad(producto)"
                                        class="btn btn-sm btn-outline-warning"
                                        :title="producto.disponible ? 'Marcar no disponible' : 'Marcar disponible'">
                                    <i :class="producto.disponible ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                </button>
                                <button @click="confirmarEliminacion(producto)"
                                        class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paginación -->
            <div v-if="productos.data.length > 0" class="row mt-4">
                <div class="col-12">
                    <nav>
                        <ul class="pagination justify-content-center">
                            <li v-for="link in productos.links"
                                :key="link.label"
                                class="page-item"
                                :class="{ active: link.active, disabled: !link.url }">
                                <Link v-if="link.url"
                                      :href="link.url"
                                      class="page-link"
                                      v-html="link.label">
                                </Link>
                                <span v-else class="page-link" v-html="link.label"></span>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Modal de confirmación de eliminación -->
        <div class="modal fade"
             :class="{ show: mostrarModalEliminar }"
             :style="{ display: mostrarModalEliminar ? 'block' : 'none' }"
             tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" @click="mostrarModalEliminar = false"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas eliminar el producto <strong>{{ productoAEliminar?.nombre }}</strong>?</p>
                        <p class="text-danger">Esta acción no se puede deshacer.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="mostrarModalEliminar = false">
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" @click="eliminarProducto">
                            <i class="bi bi-trash me-1"></i>
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="mostrarModalEliminar" class="modal-backdrop fade show"></div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Props
const props = defineProps({
    productos: Object,
    categorias: Array,
    filtros: Object
})

// Estado reactivo
const mostrarModalEliminar = ref(false)
const productoAEliminar = ref(null)
const filtros = ref({
    buscar: props.filtros?.buscar || '',
    id_categoria: props.filtros?.id_categoria || '',
    disponible: props.filtros?.disponible || ''
})

// Métodos
function aplicarFiltros() {
    router.get(route('productos.index'), filtros.value, {
        preserveState: true,
        preserveScroll: true
    })
}

function limpiarFiltros() {
    filtros.value = {
        buscar: '',
        id_categoria: '',
        disponible: ''
    }
    aplicarFiltros()
}

function formatoPrecio(precio) {
    return parseFloat(precio).toFixed(2)
}

function toggleDisponibilidad(producto) {
    router.post(route('productos.toggle-disponibilidad', producto.id_producto), {}, {
        preserveState: true,
        preserveScroll: true
    })
}

function confirmarEliminacion(producto) {
    productoAEliminar.value = producto
    mostrarModalEliminar.value = true
}

function eliminarProducto() {
    if (productoAEliminar.value) {
        router.delete(route('productos.destroy', productoAEliminar.value.id_producto), {
            onFinish: () => {
                mostrarModalEliminar.value = false
                productoAEliminar.value = null
            }
        })
    }
}
</script>

<style scoped>
.producto-card {
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.producto-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.producto-imagen-container {
    position: relative;
    height: 200px;
    overflow: hidden;
    background-color: #f8f9fa;
}

.producto-imagen {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.producto-sin-imagen {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #6c757d;
}

.producto-sin-imagen i {
    font-size: 3rem;
    margin-bottom: 0.5rem;
}

.modal.show {
    display: block !important;
}
</style>
