<template>
  <Head title="Detalle del Pago" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Detalle del Pago #{{ pago.id_pago }}
        </h2>
        <Link href="/pagos" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm transition duration-200">
          Volver a Pagos
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            
            <!-- Estado del pago -->
            <div class="mb-8">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Estado del Pago</h3>
                <span :class="getEstadoBadgeClass(pago.estado)" class="px-4 py-2 rounded-full text-sm font-medium">
                  {{ getEstadoText(pago.estado) }}
                </span>
              </div>
            </div>

            <!-- Información del pago -->
            <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-4">
                <h4 class="font-semibold text-gray-800 mb-3">Información del Pago</h4>
                
                <div>
                  <label class="block text-sm font-medium text-gray-600">ID de Pago</label>
                  <p class="text-lg font-semibold">#{{ pago.id_pago }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-600">Monto</label>
                  <p class="text-2xl font-bold text-green-600">Bs. {{ formatMoney(pago.monto) }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-600">Método de Pago</label>
                  <p class="text-lg">{{ pago.metodo_pago?.nombre || 'N/A' }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-600">Fecha de Pago</label>
                  <p class="text-lg">{{ formatDate(pago.fecha_pago) }}</p>
                </div>

                <div v-if="pago.payment_number">
                  <label class="block text-sm font-medium text-gray-600">Número de Pago</label>
                  <p class="text-lg font-mono">{{ pago.payment_number }}</p>
                </div>

                <div v-if="pago.transaction_id">
                  <label class="block text-sm font-medium text-gray-600">ID de Transacción</label>
                  <p class="text-lg font-mono">{{ pago.transaction_id }}</p>
                </div>
              </div>

              <!-- Información del pedido -->
              <div class="space-y-4">
                <h4 class="font-semibold text-gray-800 mb-3">Información del Pedido</h4>
                
                <div v-if="pago.pedido">
                  <div>
                    <label class="block text-sm font-medium text-gray-600">Pedido</label>
                    <p class="text-lg">#{{ pago.pedido.id_pedido }}</p>
                  </div>

                  <div v-if="pago.pedido.cliente">
                    <label class="block text-sm font-medium text-gray-600">Cliente</label>
                    <p class="text-lg">{{ pago.pedido.cliente.nombre }}</p>
                  </div>

                  <div v-if="pago.pedido.mesa">
                    <label class="block text-sm font-medium text-gray-600">Mesa</label>
                    <p class="text-lg">Mesa {{ pago.pedido.mesa.numero }}</p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-600">Estado del Pedido</label>
                    <span :class="getPedidoEstadoBadgeClass(pago.pedido.estado)" class="px-2 py-1 rounded text-sm font-medium">
                      {{ pago.pedido.estado }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Código QR si existe -->
            <div v-if="pago.qr_image && pago.estado === 'pendiente'" class="mb-8">
              <h4 class="font-semibold text-gray-800 mb-3">Código QR</h4>
              <div class="text-center">
                <img :src="pago.qr_image" alt="Código QR" class="mx-auto bg-white p-2 rounded border max-w-xs">
                <p class="text-sm text-gray-600 mt-2">Escanea este código para realizar el pago</p>
              </div>
            </div>

            <!-- Detalles del pedido -->
            <div v-if="pago.pedido?.detalles" class="mb-8">
              <h4 class="font-semibold text-gray-800 mb-3">Detalles del Pedido</h4>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="detalle in pago.pedido.detalles" :key="detalle.id">
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ detalle.producto.nombre }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ detalle.cantidad }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        Bs. {{ formatMoney(detalle.precio_unitario) }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                        Bs. {{ formatMoney(detalle.subtotal) }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Información de expiración -->
            <div v-if="pago.expires_at && pago.estado === 'pendiente'" class="mb-8">
              <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                  </svg>
                  <div>
                    <h5 class="font-semibold text-yellow-800">Tiempo de Expiración</h5>
                    <p class="text-yellow-600">
                      Este pago expira el {{ formatDate(pago.expires_at) }}
                      <span v-if="isExpired" class="font-semibold">(EXPIRADO)</span>
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Datos del callback -->
            <div v-if="pago.callback_data" class="mb-8">
              <h4 class="font-semibold text-gray-800 mb-3">Datos de la Transacción</h4>
              <div class="bg-gray-50 rounded-lg p-4">
                <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ JSON.stringify(pago.callback_data, null, 2) }}</pre>
              </div>
            </div>

            <!-- Acciones -->
            <div v-if="pago.estado === 'pendiente'" class="flex space-x-4">
              <button @click="consultarEstado" :disabled="consultando" class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white px-6 py-2 rounded-lg transition duration-200">
                <span v-if="consultando">Consultando...</span>
                <span v-else>Consultar Estado</span>
              </button>
              
              <button @click="cancelarPago" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition duration-200">
                Cancelar Pago
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AppLayout.vue'

// Props
const props = defineProps({
  pago: Object
})

// Estado
const consultando = ref(false)

// Computed
const isExpired = computed(() => {
  if (!props.pago.expires_at) return false
  return new Date(props.pago.expires_at) < new Date()
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
    minute: '2-digit',
    second: '2-digit'
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

const getPedidoEstadoBadgeClass = (estado) => {
  switch (estado) {
    case 'pendiente':
      return 'bg-yellow-100 text-yellow-800'
    case 'en_preparacion':
      return 'bg-blue-100 text-blue-800'
    case 'listo':
      return 'bg-green-100 text-green-800'
    case 'entregado':
      return 'bg-gray-100 text-gray-800'
    case 'pagado':
      return 'bg-green-100 text-green-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const consultarEstado = () => {
  consultando.value = true
  
  router.get(`/pago/consultar/${props.pago.id_pago}`, {}, {
    onSuccess: () => {
      router.reload()
    },
    onFinish: () => {
      consultando.value = false
    }
  })
}

const cancelarPago = () => {
  if (confirm('¿Estás seguro de que deseas cancelar este pago?')) {
    router.post(`/pago/cancelar/${props.pago.id_pago}`, {}, {
      onSuccess: () => {
        router.reload()
      }
    })
  }
}
</script>