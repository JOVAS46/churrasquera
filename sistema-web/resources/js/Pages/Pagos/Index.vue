<template>
  <Head title="Gestión de Pagos" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Gestión de Pagos
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Estadísticas -->
        <div class="mb-8 grid grid-cols-1 md:grid-cols-4 gap-6">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-center">
              <div class="text-3xl font-bold text-green-600">Bs. {{ formatMoney(estadisticas.total_hoy) }}</div>
              <div class="text-sm text-gray-600">Total Hoy</div>
            </div>
          </div>
          
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-center">
              <div class="text-3xl font-bold text-blue-600">{{ estadisticas.completados_hoy }}</div>
              <div class="text-sm text-gray-600">Pagos Completados</div>
            </div>
          </div>
          
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-center">
              <div class="text-3xl font-bold text-yellow-600">{{ estadisticas.pendientes }}</div>
              <div class="text-sm text-gray-600">Pagos Pendientes</div>
            </div>
          </div>
          
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-center">
              <div class="text-3xl font-bold text-red-600">{{ estadisticas.vencidos }}</div>
              <div class="text-sm text-gray-600">Pagos Vencidos</div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            
            <!-- Filtros -->
            <div class="mb-6">
              <div class="flex flex-wrap gap-4 items-center">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                  <select v-model="filtros.estado" @change="aplicarFiltros" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="completado">Completado</option>
                    <option value="cancelado">Cancelado</option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Desde</label>
                  <input v-model="filtros.fecha_desde" @change="aplicarFiltros" type="date" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Hasta</label>
                  <input v-model="filtros.fecha_hasta" @change="aplicarFiltros" type="date" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                </div>
                
                <div class="flex items-end">
                  <button @click="limpiarFiltros" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm transition duration-200">
                    Limpiar
                  </button>
                </div>
              </div>
            </div>

            <!-- Tabla de pagos -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      ID Pago
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Pedido
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Monto
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Método
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Estado
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Fecha
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Acciones
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="pago in pagos.data" :key="pago.id_pago" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      #{{ pago.id_pago }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      Pedido #{{ pago.pedido?.id_pedido || 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                      Bs. {{ formatMoney(pago.monto) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ pago.metodo_pago?.nombre || 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="getEstadoBadgeClass(pago.estado)" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                        {{ getEstadoText(pago.estado) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(pago.fecha_pago) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                      <Link :href="`/pagos/${pago.id_pago}`" class="text-indigo-600 hover:text-indigo-900">
                        Ver
                      </Link>
                      
                      <button v-if="pago.estado === 'pendiente'" @click="consultarEstado(pago.id_pago)" class="text-blue-600 hover:text-blue-900">
                        Consultar
                      </button>
                      
                      <button v-if="pago.estado === 'pendiente'" @click="cancelarPago(pago.id_pago)" class="text-red-600 hover:text-red-900">
                        Cancelar
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Paginación -->
            <div v-if="pagos.links" class="mt-6">
              <nav class="flex justify-between items-center">
                <div class="text-sm text-gray-700">
                  Mostrando {{ pagos.from }} a {{ pagos.to }} de {{ pagos.total }} resultados
                </div>
                <div class="flex space-x-2">
                  <Link v-for="link in pagos.links" :key="link.label" 
                        :href="link.url" 
                        :class="[
                          'px-3 py-2 rounded-md text-sm',
                          link.active 
                            ? 'bg-blue-500 text-white' 
                            : link.url 
                              ? 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' 
                              : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                        ]"
                        v-html="link.label">
                  </Link>
                </div>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AppLayout.vue'

// Props
const props = defineProps({
  pagos: Object,
  estadisticas: Object,
  filtros: Object
})

// Estado reactivo para filtros
const filtros = reactive({
  estado: props.filtros?.estado || '',
  fecha_desde: props.filtros?.fecha_desde || '',
  fecha_hasta: props.filtros?.fecha_hasta || ''
})

// Métodos
const formatMoney = (amount) => {
  return new Intl.NumberFormat('es-BO', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount || 0)
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('es-BO', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getEstadoBadgeClass = (estado) => {
  switch (estado) {
    case 'completado':
      return 'bg-green-100 text-green-800'
    case 'pendiente':
      return 'bg-yellow-100 text-yellow-800'
    case 'cancelado':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getEstadoText = (estado) => {
  switch (estado) {
    case 'completado':
      return 'Completado'
    case 'pendiente':
      return 'Pendiente'
    case 'cancelado':
      return 'Cancelado'
    default:
      return 'Desconocido'
  }
}

const aplicarFiltros = () => {
  router.get('/pagos', filtros, {
    preserveState: true,
    replace: true
  })
}

const limpiarFiltros = () => {
  filtros.estado = ''
  filtros.fecha_desde = ''
  filtros.fecha_hasta = ''
  aplicarFiltros()
}

const consultarEstado = (pagoId) => {
  router.get(`/pago/consultar/${pagoId}`, {}, {
    onSuccess: () => {
      router.reload({ only: ['pagos', 'estadisticas'] })
    }
  })
}

const cancelarPago = (pagoId) => {
  if (confirm('¿Estás seguro de que deseas cancelar este pago?')) {
    router.post(`/pago/cancelar/${pagoId}`, {}, {
      onSuccess: () => {
        router.reload({ only: ['pagos', 'estadisticas'] })
      }
    })
  }
}
</script>