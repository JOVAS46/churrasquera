<template>
    <div class="busqueda-global-wrapper">
        <!-- Campo de búsqueda -->
        <div class="busqueda-container" :class="{ 'expanded': busquedaFocused }">
            <div class="input-group">
                <input 
                    type="text" 
                    v-model="terminoBusqueda"
                    @focus="busquedaFocused = true"
                    @blur="handleBlur"
                    @keydown.enter="realizarBusqueda"
                    @input="buscarSugerencias"
                    placeholder="Buscar en el sistema..."
                    class="form-control busqueda-input"
                    autocomplete="off"
                >
                <button 
                    @click="realizarBusqueda"
                    class="btn btn-primary busqueda-btn"
                    :disabled="!terminoBusqueda.trim()"
                >
                    <i class="fas fa-search"></i>
                </button>
            </div>

            <!-- Sugerencias y resultados -->
            <div 
                v-if="mostrarSugerencias && (sugerencias.length > 0 || busquedasRecientes.length > 0)"
                class="sugerencias-dropdown"
                @mousedown.prevent
            >
                <!-- Búsquedas recientes -->
                <div v-if="busquedasRecientes.length > 0 && !terminoBusqueda.trim()" class="sugerencias-seccion">
                    <h6 class="sugerencias-titulo">
                        <i class="fas fa-history"></i>
                        Búsquedas recientes
                    </h6>
                    <div 
                        v-for="busqueda in busquedasRecientes" 
                        :key="busqueda.id"
                        @click="seleccionarSugerencia(busqueda.termino)"
                        class="sugerencia-item reciente"
                    >
                        <i class="fas fa-clock-rotate-left"></i>
                        <span>{{ busqueda.termino }}</span>
                        <small class="fecha">{{ formatearFecha(busqueda.created_at) }}</small>
                    </div>
                </div>

                <!-- Sugerencias automáticas -->
                <div v-if="sugerencias.length > 0" class="sugerencias-seccion">
                    <h6 class="sugerencias-titulo">
                        <i class="fas fa-lightbulb"></i>
                        Sugerencias
                    </h6>
                    <div 
                        v-for="(sugerencia, index) in sugerencias" 
                        :key="index"
                        @click="seleccionarSugerencia(sugerencia.texto)"
                        class="sugerencia-item"
                        :class="{ 'selected': index === sugerenciaSeleccionada }"
                    >
                        <i :class="sugerencia.icono"></i>
                        <span v-html="resaltarTexto(sugerencia.texto)"></span>
                        <small class="categoria">{{ sugerencia.categoria }}</small>
                    </div>
                </div>

                <!-- Acciones rápidas -->
                <div v-if="terminoBusqueda.trim()" class="sugerencias-seccion">
                    <div 
                        @click="buscarEnCategoria('todo')"
                        class="sugerencia-item accion"
                    >
                        <i class="fas fa-search"></i>
                        <span>Buscar "<strong>{{ terminoBusqueda }}</strong>" en todo el sistema</span>
                    </div>
                    <div 
                        @click="buscarEnCategoria('usuarios')"
                        class="sugerencia-item accion"
                    >
                        <i class="fas fa-users"></i>
                        <span>Buscar en usuarios</span>
                    </div>
                    <div 
                        @click="buscarEnCategoria('productos')"
                        class="sugerencia-item accion"
                    >
                        <i class="fas fa-box"></i>
                        <span>Buscar en productos</span>
                    </div>
                    <div 
                        @click="buscarEnCategoria('pedidos')"
                        class="sugerencia-item accion"
                    >
                        <i class="fas fa-receipt"></i>
                        <span>Buscar en pedidos</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de resultados -->
        <div v-if="mostrarResultados" class="modal fade show" style="display: block;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-search"></i>
                            Resultados de búsqueda: "{{ ultimaBusqueda }}"
                        </h5>
                        <button 
                            @click="cerrarResultados"
                            type="button" 
                            class="btn-close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <!-- Loading -->
                        <div v-if="cargandoResultados" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Buscando...</span>
                            </div>
                            <p class="mt-2">Buscando...</p>
                        </div>

                        <!-- Resultados -->
                        <div v-else-if="resultados.length > 0">
                            <div class="row">
                                <div v-for="resultado in resultados" :key="resultado.id" class="col-12 mb-3">
                                    <div class="card resultado-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start">
                                                <div class="resultado-icono me-3">
                                                    <i :class="resultado.icono"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="resultado-titulo">
                                                        <a :href="resultado.url" v-html="resaltarTexto(resultado.titulo)"></a>
                                                    </h6>
                                                    <p class="resultado-descripcion" v-html="resaltarTexto(resultado.descripcion)"></p>
                                                    <div class="resultado-meta">
                                                        <span class="badge bg-primary me-2">{{ resultado.categoria }}</span>
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar"></i>
                                                            {{ formatearFecha(resultado.fecha) }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Paginación -->
                            <nav v-if="paginacion && paginacion.last_page > 1" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item" :class="{ disabled: paginacion.current_page === 1 }">
                                        <a class="page-link" @click="cambiarPagina(paginacion.current_page - 1)">
                                            Anterior
                                        </a>
                                    </li>
                                    <li 
                                        v-for="page in paginasVisibles" 
                                        :key="page"
                                        class="page-item" 
                                        :class="{ active: page === paginacion.current_page }"
                                    >
                                        <a class="page-link" @click="cambiarPagina(page)">{{ page }}</a>
                                    </li>
                                    <li class="page-item" :class="{ disabled: paginacion.current_page === paginacion.last_page }">
                                        <a class="page-link" @click="cambiarPagina(paginacion.current_page + 1)">
                                            Siguiente
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>

                        <!-- Sin resultados -->
                        <div v-else class="text-center py-4">
                            <div class="empty-state">
                                <i class="fas fa-search-minus fa-3x text-muted mb-3"></i>
                                <h6>No se encontraron resultados</h6>
                                <p class="text-muted">
                                    No hay resultados para "<strong>{{ ultimaBusqueda }}</strong>"
                                </p>
                                <button @click="limpiarBusqueda" class="btn btn-outline-primary">
                                    <i class="fas fa-refresh"></i>
                                    Nueva búsqueda
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <small class="text-muted" v-if="resultados.length > 0">
                                {{ paginacion.total }} resultado(s) encontrado(s)
                            </small>
                            <button @click="cerrarResultados" class="btn btn-secondary">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Overlay del modal -->
        <div v-if="mostrarResultados" class="modal-backdrop fade show" @click="cerrarResultados"></div>
    </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

export default {
    name: 'BusquedaGlobal',
    props: {
        busquedasRecientes: {
            type: Array,
            default: () => []
        }
    },
    setup(props) {
        const terminoBusqueda = ref('')
        const busquedaFocused = ref(false)
        const mostrarSugerencias = ref(false)
        const mostrarResultados = ref(false)
        const cargandoResultados = ref(false)
        const sugerencias = ref([])
        const resultados = ref([])
        const paginacion = ref(null)
        const ultimaBusqueda = ref('')
        const sugerenciaSeleccionada = ref(-1)
        const timeoutBusqueda = ref(null)

        // Computed
        const paginasVisibles = computed(() => {
            if (!paginacion.value) return []
            
            const total = paginacion.value.last_page
            const actual = paginacion.value.current_page
            const delta = 2
            const range = []
            
            for (let i = Math.max(1, actual - delta); i <= Math.min(total, actual + delta); i++) {
                range.push(i)
            }
            
            return range
        })

        // Métodos
        const buscarSugerencias = async () => {
            const termino = terminoBusqueda.value.trim()
            
            if (termino.length < 2) {
                sugerencias.value = []
                mostrarSugerencias.value = true
                return
            }
            
            // Debounce
            if (timeoutBusqueda.value) {
                clearTimeout(timeoutBusqueda.value)
            }
            
            timeoutBusqueda.value = setTimeout(async () => {
                try {
                    const response = await axios.get('/api/busqueda/sugerencias', {
                        params: { q: termino }
                    })
                    sugerencias.value = response.data
                    mostrarSugerencias.value = true
                } catch (error) {
                    console.error('Error obteniendo sugerencias:', error)
                    sugerencias.value = []
                }
            }, 300)
        }

        const seleccionarSugerencia = (texto) => {
            terminoBusqueda.value = texto
            mostrarSugerencias.value = false
            realizarBusqueda()
        }

        const realizarBusqueda = async (categoria = 'todo', pagina = 1) => {
            const termino = terminoBusqueda.value.trim()
            if (!termino) return
            
            ultimaBusqueda.value = termino
            mostrarSugerencias.value = false
            mostrarResultados.value = true
            cargandoResultados.value = true
            
            try {
                const response = await axios.get('/api/busqueda/resultados', {
                    params: { 
                        q: termino, 
                        categoria: categoria,
                        page: pagina
                    }
                })
                
                resultados.value = response.data.data || []
                paginacion.value = {
                    current_page: response.data.current_page,
                    last_page: response.data.last_page,
                    total: response.data.total
                }
                
                // Guardar búsqueda en historial
                await axios.post('/api/busqueda/guardar', {
                    termino: termino,
                    categoria: categoria,
                    resultados_encontrados: response.data.total
                })
                
            } catch (error) {
                console.error('Error realizando búsqueda:', error)
                resultados.value = []
                paginacion.value = null
            } finally {
                cargandoResultados.value = false
            }
        }

        const buscarEnCategoria = (categoria) => {
            realizarBusqueda(categoria)
        }

        const cambiarPagina = (pagina) => {
            if (pagina >= 1 && pagina <= paginacion.value.last_page) {
                realizarBusqueda('todo', pagina)
            }
        }

        const resaltarTexto = (texto) => {
            if (!ultimaBusqueda.value) return texto
            
            const regex = new RegExp(`(${ultimaBusqueda.value})`, 'gi')
            return texto.replace(regex, '<mark>$1</mark>')
        }

        const formatearFecha = (fecha) => {
            return new Date(fecha).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            })
        }

        const handleBlur = () => {
            setTimeout(() => {
                busquedaFocused.value = false
                mostrarSugerencias.value = false
            }, 200)
        }

        const cerrarResultados = () => {
            mostrarResultados.value = false
        }

        const limpiarBusqueda = () => {
            terminoBusqueda.value = ''
            resultados.value = []
            ultimaBusqueda.value = ''
            cerrarResultados()
        }

        // Navegación con teclado
        const manejarTeclado = (evento) => {
            if (!mostrarSugerencias.value) return
            
            const totalSugerencias = sugerencias.value.length
            
            switch (evento.key) {
                case 'ArrowDown':
                    evento.preventDefault()
                    sugerenciaSeleccionada.value = Math.min(
                        sugerenciaSeleccionada.value + 1, 
                        totalSugerencias - 1
                    )
                    break
                case 'ArrowUp':
                    evento.preventDefault()
                    sugerenciaSeleccionada.value = Math.max(
                        sugerenciaSeleccionada.value - 1, 
                        -1
                    )
                    break
                case 'Enter':
                    evento.preventDefault()
                    if (sugerenciaSeleccionada.value >= 0) {
                        seleccionarSugerencia(sugerencias.value[sugerenciaSeleccionada.value].texto)
                    } else {
                        realizarBusqueda()
                    }
                    break
                case 'Escape':
                    mostrarSugerencias.value = false
                    break
            }
        }

        // Lifecycle
        onMounted(() => {
            document.addEventListener('keydown', manejarTeclado)
        })

        onUnmounted(() => {
            document.removeEventListener('keydown', manejarTeclado)
            if (timeoutBusqueda.value) {
                clearTimeout(timeoutBusqueda.value)
            }
        })

        return {
            terminoBusqueda,
            busquedaFocused,
            mostrarSugerencias,
            mostrarResultados,
            cargandoResultados,
            sugerencias,
            resultados,
            paginacion,
            ultimaBusqueda,
            sugerenciaSeleccionada,
            paginasVisibles,
            buscarSugerencias,
            seleccionarSugerencia,
            realizarBusqueda,
            buscarEnCategoria,
            cambiarPagina,
            resaltarTexto,
            formatearFecha,
            handleBlur,
            cerrarResultados,
            limpiarBusqueda
        }
    }
}
</script>

<style scoped>
.busqueda-global-wrapper {
    position: relative;
}

.busqueda-container {
    position: relative;
    max-width: 400px;
    transition: all 0.3s ease;
}

.busqueda-container.expanded {
    max-width: 500px;
}

.busqueda-input {
    border-radius: 25px 0 0 25px;
    padding-left: 1.5rem;
    border-right: none;
    transition: all 0.3s ease;
}

.busqueda-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    border-color: #3498db;
}

.busqueda-btn {
    border-radius: 0 25px 25px 0;
    border-left: none;
    padding: 0.5rem 1rem;
}

.sugerencias-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--color-fondo-carta, white);
    border: 1px solid var(--color-borde, #dee2e6);
    border-radius: 0 0 12px 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    max-height: 400px;
    overflow-y: auto;
    margin-top: 2px;
}

.sugerencias-seccion {
    padding: 0.5rem 0;
}

.sugerencias-seccion:not(:last-child) {
    border-bottom: 1px solid var(--color-borde, #eee);
}

.sugerencias-titulo {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--color-texto-muted, #666);
    padding: 0.5rem 1rem;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.sugerencia-item {
    padding: 0.75rem 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    border-left: 3px solid transparent;
}

.sugerencia-item:hover,
.sugerencia-item.selected {
    background: var(--color-fondo-secundario, #f8f9fa);
    border-left-color: var(--color-primario, #3498db);
}

.sugerencia-item.reciente {
    color: var(--color-texto-muted, #666);
}

.sugerencia-item.accion {
    font-weight: 500;
    color: var(--color-primario, #3498db);
}

.sugerencia-item i {
    width: 16px;
    text-align: center;
    color: var(--color-texto-muted, #999);
}

.sugerencia-item.accion i {
    color: var(--color-primario, #3498db);
}

.categoria,
.fecha {
    margin-left: auto;
    color: var(--color-texto-muted, #999);
    font-size: 0.75rem;
}

.resultado-card {
    transition: all 0.3s ease;
    border: 1px solid var(--color-borde, #dee2e6);
}

.resultado-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

.resultado-icono {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--color-fondo-secundario, #f8f9fa);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-primario, #3498db);
    font-size: 1.2rem;
}

.resultado-titulo a {
    color: var(--color-texto-primario, #2c3e50);
    text-decoration: none;
    font-weight: 600;
}

.resultado-titulo a:hover {
    color: var(--color-primario, #3498db);
    text-decoration: underline;
}

.resultado-descripcion {
    color: var(--color-texto-secundario, #5a6c7d);
    margin: 0.5rem 0;
    line-height: 1.5;
}

.resultado-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.75rem;
}

.empty-state {
    padding: 2rem;
}

.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5);
}

mark {
    background-color: #fff3cd;
    padding: 0.1em 0.2em;
    border-radius: 3px;
    font-weight: 600;
}

/* Tema específico para búsquedas recientes */
.sugerencia-item.reciente:hover {
    background: linear-gradient(45deg, var(--color-fondo-secundario, #f8f9fa), rgba(52, 152, 219, 0.05));
}

/* Responsive */
@media (max-width: 768px) {
    .busqueda-container {
        max-width: 100%;
    }
    
    .busqueda-container.expanded {
        max-width: 100%;
    }
    
    .modal-dialog {
        margin: 0.5rem;
    }
    
    .sugerencias-dropdown {
        max-height: 300px;
    }
}

/* Scrollbar personalizado para sugerencias */
.sugerencias-dropdown::-webkit-scrollbar {
    width: 6px;
}

.sugerencias-dropdown::-webkit-scrollbar-track {
    background: var(--color-fondo-terciario, #f1f3f4);
}

.sugerencias-dropdown::-webkit-scrollbar-thumb {
    background: var(--color-texto-muted, #bdc3c7);
    border-radius: 3px;
}

.sugerencias-dropdown::-webkit-scrollbar-thumb:hover {
    background: var(--color-primario, #3498db);
}
</style>