<template>
    <AppLayout>
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2 class="text-primary mb-0">
                        <i class="bi bi-box-seam me-2"></i>
                        {{ producto?.id_producto ? 'Editar Producto' : 'Nuevo Producto' }}
                    </h2>
                </div>
                <div class="col-md-4 text-end">
                    <Link :href="route('productos.index')" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>
                        Volver al Listado
                    </Link>
                </div>
            </div>

            <form @submit.prevent="guardarProducto" class="row">
                <!-- Formulario Principal -->
                <div class="col-xl-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Información Básica
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Nombre -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input v-model="form.nombre"
                                           type="text"
                                           class="form-control"
                                           :class="{ 'is-invalid': errors.nombre }"
                                           placeholder="Ej: Asado de tira"
                                           required>
                                    <div v-if="errors.nombre" class="invalid-feedback">{{ errors.nombre }}</div>
                                </div>

                                <!-- Categoría -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Categoría <span class="text-danger">*</span></label>
                                    <select v-model="form.id_categoria"
                                            class="form-select"
                                            :class="{ 'is-invalid': errors.id_categoria }"
                                            required>
                                        <option value="">Seleccionar categoría...</option>
                                        <option v-for="categoria in categorias"
                                                :key="categoria.id_categoria"
                                                :value="categoria.id_categoria">
                                            {{ categoria.nombre }}
                                        </option>
                                    </select>
                                    <div v-if="errors.id_categoria" class="invalid-feedback">{{ errors.id_categoria }}</div>
                                </div>

                                <!-- Descripción -->
                                <div class="col-12 mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea v-model="form.descripcion"
                                              class="form-control"
                                              :class="{ 'is-invalid': errors.descripcion }"
                                              rows="3"
                                              placeholder="Descripción del producto..."></textarea>
                                    <div v-if="errors.descripcion" class="invalid-feedback">{{ errors.descripcion }}</div>
                                </div>

                                <!-- Precio -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Precio (Bs.) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Bs.</span>
                                        <input v-model.number="form.precio"
                                               type="number"
                                               step="0.01"
                                               min="0"
                                               class="form-control"
                                               :class="{ 'is-invalid': errors.precio }"
                                               placeholder="0.00"
                                               required>
                                        <div v-if="errors.precio" class="invalid-feedback">{{ errors.precio }}</div>
                                    </div>
                                </div>

                                <!-- Tiempo de preparación -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tiempo de Preparación (minutos)</label>
                                    <div class="input-group">
                                        <input v-model.number="form.tiempo_preparacion"
                                               type="number"
                                               min="0"
                                               class="form-control"
                                               :class="{ 'is-invalid': errors.tiempo_preparacion }"
                                               placeholder="15">
                                        <span class="input-group-text">min</span>
                                        <div v-if="errors.tiempo_preparacion" class="invalid-feedback">{{ errors.tiempo_preparacion }}</div>
                                    </div>
                                </div>

                                <!-- Imagen -->
                                <div class="col-12 mb-3">
                                    <label class="form-label">Imagen del Producto</label>
                                    <input type="file"
                                           class="form-control"
                                           :class="{ 'is-invalid': errors.imagen }"
                                           accept="image/*"
                                           @change="handleImagenChange">
                                    <div v-if="errors.imagen" class="invalid-feedback">{{ errors.imagen }}</div>
                                    <small class="form-text text-muted">Tamaño máximo: 2MB. Formatos: JPG, PNG, GIF</small>

                                    <!-- Preview de imagen -->
                                    <div v-if="imagenPreview" class="mt-3">
                                        <img :src="imagenPreview" class="img-thumbnail" style="max-height: 200px" alt="Preview">
                                    </div>
                                    <div v-else-if="producto?.imagen" class="mt-3">
                                        <img :src="`/storage/${producto.imagen}`" class="img-thumbnail" style="max-height: 200px" alt="Imagen actual">
                                        <p class="text-muted small">Imagen actual</p>
                                    </div>
                                </div>

                                <!-- Disponible -->
                                <div class="col-12 mb-3">
                                    <div class="form-check form-switch">
                                        <input v-model="form.disponible"
                                               type="checkbox"
                                               class="form-check-input"
                                               id="disponible">
                                        <label class="form-check-label" for="disponible">
                                            Producto disponible para la venta
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Insumos -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-box me-2"></i>
                                Insumos Necesarios
                            </h5>
                            <button type="button" class="btn btn-sm btn-primary" @click="agregarInsumo">
                                <i class="bi bi-plus"></i> Agregar Insumo
                            </button>
                        </div>
                        <div class="card-body">
                            <div v-if="form.insumos.length === 0" class="text-muted text-center py-3">
                                <i class="bi bi-inbox fs-1 opacity-50"></i>
                                <p>No hay insumos agregados</p>
                            </div>

                            <div v-for="(insumo, index) in form.insumos" :key="index" class="row mb-3 align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label">Insumo</label>
                                    <select v-model="insumo.id_insumo"
                                            class="form-select"
                                            :class="{ 'is-invalid': errors[`insumos.${index}.id_insumo`] }"
                                            required>
                                        <option value="">Seleccionar insumo...</option>
                                        <option v-for="ins in insumos"
                                                :key="ins.id_insumo"
                                                :value="ins.id_insumo">
                                            {{ ins.nombre }} ({{ ins.unidad_medida?.nombre }})
                                        </option>
                                    </select>
                                    <div v-if="errors[`insumos.${index}.id_insumo`]" class="invalid-feedback">
                                        {{ errors[`insumos.${index}.id_insumo`] }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Cantidad Necesaria</label>
                                    <input v-model.number="insumo.cantidad_necesaria"
                                           type="number"
                                           step="0.01"
                                           min="0"
                                           class="form-control"
                                           :class="{ 'is-invalid': errors[`insumos.${index}.cantidad_necesaria`] }"
                                           placeholder="0.00"
                                           required>
                                    <div v-if="errors[`insumos.${index}.cantidad_necesaria`]" class="invalid-feedback">
                                        {{ errors[`insumos.${index}.cantidad_necesaria`] }}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button"
                                            class="btn btn-outline-danger w-100"
                                            @click="eliminarInsumo(index)">
                                        <i class="bi bi-trash"></i>
                                    </button>
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
                            <h6 class="card-title mb-0">Resumen</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="text-muted">Nombre:</td>
                                    <td>{{ form.nombre || 'Sin nombre' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Categoría:</td>
                                    <td>{{ categoriaSeleccionada?.nombre || 'Sin categoría' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Precio:</td>
                                    <td class="text-success fw-bold">Bs. {{ formatoPrecio(form.precio) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tiempo prep.:</td>
                                    <td>{{ form.tiempo_preparacion || 0 }} min</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Estado:</td>
                                    <td>
                                        <span class="badge" :class="form.disponible ? 'bg-success' : 'bg-danger'">
                                            {{ form.disponible ? 'Disponible' : 'No disponible' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Insumos:</td>
                                    <td>{{ form.insumos.length }} insumo(s)</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit"
                                        class="btn btn-primary btn-lg"
                                        :disabled="procesando">
                                    <div v-if="procesando" class="spinner-border spinner-border-sm me-2"></div>
                                    <i v-else class="bi bi-check me-2"></i>
                                    {{ producto?.id_producto ? 'Actualizar Producto' : 'Crear Producto' }}
                                </button>

                                <Link :href="route('productos.index')" class="btn btn-outline-secondary">
                                    <i class="bi bi-x me-1"></i>
                                    Cancelar
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Props
const props = defineProps({
    producto: Object,
    categorias: Array,
    insumos: Array,
    errors: Object
})

// Estado reactivo
const procesando = ref(false)
const imagenPreview = ref(null)

// Formulario
const form = useForm({
    nombre: props.producto?.nombre || '',
    descripcion: props.producto?.descripcion || '',
    precio: props.producto?.precio || 0,
    tiempo_preparacion: props.producto?.tiempo_preparacion || null,
    id_categoria: props.producto?.id_categoria || '',
    disponible: props.producto?.disponible ?? true,
    imagen: null,
    insumos: props.producto?.insumos?.map(i => ({
        id_insumo: i.id_insumo,
        cantidad_necesaria: i.pivot?.cantidad_necesaria || 0
    })) || []
})

// Computed
const categoriaSeleccionada = computed(() => {
    return props.categorias.find(c => c.id_categoria == form.id_categoria)
})

// Métodos
function formatoPrecio(precio) {
    return parseFloat(precio || 0).toFixed(2)
}

function handleImagenChange(event) {
    const file = event.target.files[0]
    if (file) {
        form.imagen = file

        // Crear preview
        const reader = new FileReader()
        reader.onload = (e) => {
            imagenPreview.value = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

function agregarInsumo() {
    form.insumos.push({
        id_insumo: '',
        cantidad_necesaria: 0
    })
}

function eliminarInsumo(index) {
    form.insumos.splice(index, 1)
}

function guardarProducto() {
    procesando.value = true

    const url = props.producto?.id_producto
        ? route('productos.update', props.producto.id_producto)
        : route('productos.store')

    const method = props.producto?.id_producto ? 'post' : 'post'

    // Si es edición, necesitamos usar _method: PUT
    if (props.producto?.id_producto) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT'
        }))
    }

    form.post(url, {
        onFinish: () => {
            procesando.value = false
        }
    })
}
</script>

<style scoped>
.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
    border: none;
}

.table-borderless td {
    padding: 0.25rem 0;
}

.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
}
</style>
