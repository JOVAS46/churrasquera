<template>
    <div class="contador-visitas-footer">
        <div class="container">
            <div class="row align-items-center py-2">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                    <small class="text-muted">
                        <i class="bi bi-eye me-1"></i>
                        <strong>{{ paginaActual }}</strong>: {{ formatNumber(visitasPaginaActual) }} visitas
                    </small>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <small class="text-muted">
                        <i class="bi bi-globe me-1"></i>
                        Total del sitio: <strong>{{ formatNumber(visitasTotales) }}</strong> visitas
                        <span class="ms-2">|</span>
                        <i class="bi bi-file-text ms-2 me-1"></i>
                        {{ paginasUnicas }} páginas únicas
                    </small>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// Props
const props = defineProps({
    pagina: {
        type: String,
        default: window.location.pathname
    }
})

// Estado reactivo
const visitasPaginaActual = ref(0)
const visitasTotales = ref(0)
const paginasUnicas = ref(0)
const paginaActual = ref(props.pagina)

// Métodos
function formatNumber(number) {
    return new Intl.NumberFormat('es-BO').format(number || 0)
}

async function cargarEstadisticas() {
    try {
        const response = await axios.get('/api/contador-visitas', {
            params: {
                pagina: props.pagina
            }
        })

        if (response.data) {
            visitasPaginaActual.value = response.data.visitas_pagina || 0
            visitasTotales.value = response.data.visitas_totales || 0
            paginasUnicas.value = response.data.paginas_unicas || 0
        }
    } catch (error) {
        console.error('Error al cargar estadísticas de visitas:', error)
    }
}

// Lifecycle
onMounted(() => {
    cargarEstadisticas()
})
</script>

<style scoped>
.contador-visitas-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
    font-size: 0.875rem;
}

.contador-visitas-footer small {
    display: inline-block;
}

.contador-visitas-footer i {
    opacity: 0.7;
}

@media (max-width: 767px) {
    .contador-visitas-footer {
        font-size: 0.75rem;
    }
}
</style>
