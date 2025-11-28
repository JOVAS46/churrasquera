<template>
    <AppLayout>
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2 class="text-primary mb-0">
                        <i class="bi bi-table me-2"></i>
                        {{ mesa?.id_mesa ? 'Editar Mesa' : 'Nueva Mesa' }}
                    </h2>
                    <p class="text-muted">
                        {{ mesa?.id_mesa ? `Modificando Mesa #${mesa.numero_mesa}` : 'Complete los datos para crear una nueva mesa' }}
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <Link :href="route('mesas.index')" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>
                        Volver al Listado
                    </Link>
                </div>
            </div>

            <form @submit.prevent="guardarMesa" class="row">
                <!-- Formulario Principal -->
                <div class="col-xl-8">
                    <!-- Información Básica -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Información Básica
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Número de Mesa -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Número de Mesa <span class="text-danger">*</span></label>
                                    <input v-model="form.numero_mesa" 
                                           type="number" 
                                           class="form-control" 
                                           :class="{ 'is-invalid': errors.numero_mesa }"
                                           min="1" 
                                           max="999"
                                           required>
                                    <div v-if="errors.numero_mesa" class="invalid-feedback">{{ errors.numero_mesa }}</div>
                                    <div class="form-text">Número único identificador de la mesa</div>
                                </div>

                                <!-- Capacidad -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Capacidad <span class="text-danger">*</span></label>
                                    <select v-model="form.capacidad" 
                                            class="form-select" 
                                            :class="{ 'is-invalid': errors.capacidad }"
                                            required>
                                        <option value="">Seleccionar capacidad...</option>
                                        <option value="2">2 personas</option>
                                        <option value="4">4 personas</option>
                                        <option value="6">6 personas</option>
                                        <option value="8">8 personas</option>
                                        <option value="10">10 personas</option>
                                        <option value="12">12 personas</option>
                                    </select>
                                    <div v-if="errors.capacidad" class="invalid-feedback">{{ errors.capacidad }}</div>
                                    <div class="form-text">Número máximo de comensales</div>
                                </div>

                                <!-- Ubicación -->
                                <div class="col-12 mb-3">
                                    <label class="form-label">Ubicación <span class="text-danger">*</span></label>
                                    <select v-model="form.ubicacion" 
                                            class="form-select" 
                                            :class="{ 'is-invalid': errors.ubicacion }"
                                            required>
                                        <option value="">Seleccionar ubicación...</option>
                                        <option value="Salón principal">Salón principal</option>
                                        <option value="Terraza">Terraza</option>
                                        <option value="VIP">Área VIP</option>
                                        <option value="Bar">Bar</option>
                                        <option value="Jardín">Jardín</option>
                                        <option value="Privado">Salón privado</option>
                                    </select>
                                    <div v-if="errors.ubicacion" class="invalid-feedback">{{ errors.ubicacion }}</div>
                                </div>

                                <!-- Descripción -->
                                <div class="col-12">
                                    <label class="form-label">Descripción</label>
                                    <textarea v-model="form.descripcion" 
                                              class="form-control" 
                                              :class="{ 'is-invalid': errors.descripcion }"
                                              rows="3"
                                              placeholder="Características especiales, vista, ventanas, etc."></textarea>
                                    <div v-if="errors.descripcion" class="invalid-feedback">{{ errors.descripcion }}</div>
                                    <div class="form-text">Opcional: Descripción adicional de la mesa</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Configuración de Estado -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-gear me-2"></i>
                                Configuración de Estado
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Estado Inicial -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Estado Inicial</label>
                                    <select v-model="form.estado" 
                                            class="form-select" 
                                            :class="{ 'is-invalid': errors.estado }">
                                        <option value="disponible">Disponible</option>
                                        <option value="mantenimiento">Mantenimiento</option>
                                    </select>
                                    <div v-if="errors.estado" class="invalid-feedback">{{ errors.estado }}</div>
                                    <div class="form-text">Estado inicial de la mesa</div>
                                </div>

                                <!-- Mesero Asignado -->
                                <div class="col-md-6 mb-3" :class="{ 'd-none': activeTab !== 'mesero' }">
                                    <label class="form-label">Mesero Asignado</label>
                                    <select v-model="form.mesero_id" 
                                            class="form-select" 
                                            :class="{ 'is-invalid': errors.mesero_id }">
                                        <option value="">Sin asignar</option>
                                        <option v-for="mesero in meseros" 
                                                :key="mesero.id" 
                                                :value="mesero.id">
                                            {{ mesero.name }} {{ mesero.apellido || '' }}
                                        </option>
                                    </select>
                                    <div v-if="errors.mesero_id" class="invalid-feedback">{{ errors.mesero_id }}</div>
                                    <div class="form-text">Mesero responsable de atender esta mesa</div>
                                </div>

                                <!-- Disponibilidad -->
                                <div class="col-12">
                                    <div class="form-check">
                                        <input v-model="form.activa" 
                                               type="checkbox" 
                                               class="form-check-input" 
                                               id="mesaActiva">
                                        <label class="form-check-label" for="mesaActiva">
                                            Mesa activa para reservas y pedidos
                                        </label>
                                    </div>
                                    <div class="form-text">Si está deshabilitada, no aparecerá disponible para nuevas reservas</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Configuración Avanzada -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-sliders me-2"></i>
                                Configuración Avanzada
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Tiempo Límite -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tiempo Límite (minutos)</label>
                                    <input v-model.number="form.tiempo_limite" 
                                           type="number" 
                                           class="form-control" 
                                           :class="{ 'is-invalid': errors.tiempo_limite }"
                                           min="30" 
                                           max="480"
                                           placeholder="120">
                                    <div v-if="errors.tiempo_limite" class="invalid-feedback">{{ errors.tiempo_limite }}</div>
                                    <div class="form-text">Tiempo máximo de ocupación por defecto (opcional)</div>
                                </div>

                                <!-- Precio Base -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Precio Base</label>
                                    <div class="input-group">
                                        <span class="input-group-text">S/</span>
                                        <input v-model.number="form.precio_base" 
                                               type="number" 
                                               class="form-control" 
                                               :class="{ 'is-invalid': errors.precio_base }"
                                               min="0" 
                                               step="0.01"
                                               placeholder="0.00">
                                    </div>
                                    <div v-if="errors.precio_base" class="invalid-feedback">{{ errors.precio_base }}</div>
                                    <div class="form-text">Costo adicional por uso de mesa (opcional)</div>
                                </div>

                                <!-- Características Especiales -->
                                <div class="col-12">
                                    <label class="form-label">Características Especiales</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input v-model="caracteristicas.exterior" 
                                                       type="checkbox" 
                                                       class="form-check-input" 
                                                       id="exterior">
                                                <label class="form-check-label" for="exterior">
                                                    <i class="bi bi-sun me-1"></i>
                                                    Exterior
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input v-model="caracteristicas.vista_especial" 
                                                       type="checkbox" 
                                                       class="form-check-input" 
                                                       id="vista">
                                                <label class="form-check-label" for="vista">
                                                    <i class="bi bi-eye me-1"></i>
                                                    Vista Especial
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input v-model="caracteristicas.acceso_discapacitados" 
                                                       type="checkbox" 
                                                       class="form-check-input" 
                                                       id="accesible">
                                                <label class="form-check-label" for="accesible">
                                                    <i class="bi bi-universal-access me-1"></i>
                                                    Accesible
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input v-model="caracteristicas.aire_acondicionado" 
                                                       type="checkbox" 
                                                       class="form-check-input" 
                                                       id="aire">
                                                <label class="form-check-label" for="aire">
                                                    <i class="bi bi-snow me-1"></i>
                                                    Aire Acondicionado
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input v-model="caracteristicas.wifi" 
                                                       type="checkbox" 
                                                       class="form-check-input" 
                                                       id="wifi">
                                                <label class="form-check-label" for="wifi">
                                                    <i class="bi bi-wifi me-1"></i>
                                                    WiFi
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input v-model="caracteristicas.enchufes" 
                                                       type="checkbox" 
                                                       class="form-check-input" 
                                                       id="enchufes">
                                                <label class="form-check-label" for="enchufes">
                                                    <i class="bi bi-plug me-1"></i>
                                                    Enchufes
                                                </label>
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
                    <!-- Preview de la Mesa -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Vista Previa</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="mesa-preview" :class="getMesaPreviewClass()">
                                <i class="bi bi-table display-4 mb-3 d-block"></i>
                                <h5>Mesa #{{ form.numero_mesa || '?' }}</h5>
                                <div class="mesa-info">
                                    <span class="badge bg-light text-dark">
                                        {{ form.capacidad || 0 }} <i class="bi bi-people ms-1"></i>
                                    </span>
                                    <div class="mt-2">
                                        <small class="text-muted">{{ form.ubicacion || 'Sin ubicación' }}</small>
                                    </div>
                                    <div class="mt-2">
                                        <span :class="getBadgeClass(form.estado)">
                                            {{ getEstadoTexto(form.estado) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Validaciones -->
                    <div class="card mb-4" v-if="validaciones.length > 0">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Validaciones
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li v-for="validacion in validaciones" 
                                    :key="validacion.campo" 
                                    class="mb-2">
                                    <small :class="validacion.valido ? 'text-success' : 'text-danger'">
                                        <i :class="validacion.valido ? 'bi bi-check' : 'bi bi-x'"></i>
                                        {{ validacion.mensaje }}
                                    </small>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    <div class="card mb-4" v-if="mesa?.id_mesa">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Información Actual</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="text-muted">Estado Original:</td>
                                    <td>
                                        <span :class="getBadgeClass(mesa.estado)">
                                            {{ getEstadoTexto(mesa.estado) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Creada:</td>
                                    <td>{{ formatFecha(mesa.created_at) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Última Actualización:</td>
                                    <td>{{ formatFecha(mesa.updated_at) }}</td>
                                </tr>
                                <tr v-if="mesa.ultima_ocupacion">
                                    <td class="text-muted">Última Ocupación:</td>
                                    <td>{{ formatFecha(mesa.ultima_ocupacion) }}</td>
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
                                        :disabled="procesando || !formularioValido">
                                    <div v-if="procesando" class="spinner-border spinner-border-sm me-2"></div>
                                    <i v-else class="bi bi-check me-2"></i>
                                    {{ mesa?.id_mesa ? 'Actualizar Mesa' : 'Crear Mesa' }}
                                </button>
                                
                                <Link :href="route('mesas.index')" class="btn btn-outline-secondary">
                                    <i class="bi bi-x me-1"></i>
                                    Cancelar
                                </Link>

                                <div v-if="mesa?.id_mesa && mesa.estado !== 'ocupada'" class="border-top pt-2 mt-2">
                                    <button type="button" 
                                            @click="eliminarMesa" 
                                            class="btn btn-outline-danger w-100">
                                        <i class="bi bi-trash me-1"></i>
                                        Eliminar Mesa
                                    </button>
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
import { ref, computed, watch, onMounted } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Props
const props = defineProps({
    mesa: Object,
    meseros: Array,
    errors: Object
})

// URL params para pestañas
const params = new URLSearchParams(window.location.search)
const activeTab = ref(params.get('tab') || 'general')

// Estado reactivo
const procesando = ref(false)

// Formularios
const form = useForm({
    numero_mesa: props.mesa?.numero_mesa || '',
    capacidad: props.mesa?.capacidad || '',
    ubicacion: props.mesa?.ubicacion || '',
    descripcion: props.mesa?.descripcion || '',
    estado: props.mesa?.estado || 'disponible',
    mesero_id: props.mesa?.mesero_id || '',
    activa: props.mesa?.activa !== false,
    tiempo_limite: props.mesa?.tiempo_limite || 120,
    precio_base: props.mesa?.precio_base || 0
})

// Características especiales
const caracteristicas = ref({
    exterior: false,
    vista_especial: false,
    acceso_discapacitados: false,
    aire_acondicionado: false,
    wifi: false,
    enchufes: false
})

// Computed
const formularioValido = computed(() => {
    return form.numero_mesa && form.capacidad && form.ubicacion
})

const validaciones = computed(() => {
    const validaciones = []
    
    validaciones.push({
        campo: 'numero_mesa',
        valido: !!form.numero_mesa,
        mensaje: 'Número de mesa requerido'
    })
    
    validaciones.push({
        campo: 'capacidad',
        valido: !!form.capacidad,
        mensaje: 'Capacidad requerida'
    })
    
    validaciones.push({
        campo: 'ubicacion',
        valido: !!form.ubicacion,
        mensaje: 'Ubicación requerida'
    })
    
    return validaciones
})

// Métodos
function guardarMesa() {
    procesando.value = true
    
    // Agregar características al formulario
    form.caracteristicas = JSON.stringify(caracteristicas.value)
    
    const url = props.mesa?.id_mesa 
        ? route('mesas.update', props.mesa.id_mesa)
        : route('mesas.store')
    
    const method = props.mesa?.id_mesa ? 'put' : 'post'
    
    form[method](url, {
        onFinish: () => {
            procesando.value = false
        }
    })
}

function eliminarMesa() {
    if (confirm(`¿Está seguro de eliminar la Mesa #${props.mesa.numero_mesa}? Esta acción no se puede deshacer.`)) {
        router.delete(route('mesas.destroy', props.mesa.id_mesa))
    }
}

function getMesaPreviewClass() {
    const baseClass = 'mesa-preview-card'
    const estadoClass = form.estado ? `mesa-${form.estado}` : 'mesa-disponible'
    return `${baseClass} ${estadoClass}`
}

function getBadgeClass(estado) {
    const classes = {
        'disponible': 'badge bg-success',
        'ocupada': 'badge bg-danger',
        'reservada': 'badge bg-warning text-dark',
        'mantenimiento': 'badge bg-secondary'
    }
    return classes[estado] || 'badge bg-secondary'
}

function getEstadoTexto(estado) {
    const textos = {
        'disponible': 'Disponible',
        'ocupada': 'Ocupada',
        'reservada': 'Reservada',
        'mantenimiento': 'Mantenimiento'
    }
    return textos[estado] || estado
}

function formatFecha(fecha) {
    return new Date(fecha).toLocaleDateString('es-PE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

// Inicialización
onMounted(() => {
    // Cargar características existentes si estamos editando
    if (props.mesa?.caracteristicas) {
        try {
            const caracteristicasExistentes = JSON.parse(props.mesa.caracteristicas)
            caracteristicas.value = { ...caracteristicas.value, ...caracteristicasExistentes }
        } catch (e) {
            console.warn('No se pudieron cargar las características existentes')
        }
    }
})
</script>

<style scoped>
.mesa-preview-card {
    background: #f8f9fa;
    border: 2px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 2rem 1rem;
    transition: all 0.3s ease;
}

.mesa-preview-card.mesa-disponible {
    border-color: #198754;
    background: linear-gradient(145deg, #f8f9fa, #e8f5e8);
}

.mesa-preview-card.mesa-ocupada {
    border-color: #dc3545;
    background: linear-gradient(145deg, #f8f9fa, #f8e8e8);
}

.mesa-preview-card.mesa-reservada {
    border-color: #ffc107;
    background: linear-gradient(145deg, #f8f9fa, #fff8e8);
}

.mesa-preview-card.mesa-mantenimiento {
    border-color: #6c757d;
    background: linear-gradient(145deg, #f8f9fa, #e8e8e8);
}

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

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.mesa-info .badge {
    font-size: 0.875rem;
}
</style>