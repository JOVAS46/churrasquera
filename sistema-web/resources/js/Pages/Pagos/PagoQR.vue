<template>
  <Head title="Pago con QR - PagoFácil" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Pago con QR - PagoFácil
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <!-- Información del pedido -->
            <div class="mb-8">
              <h3 class="text-lg font-semibold text-gray-800 mb-4">Información del Pedido</h3>
              <div class="bg-gray-50 p-4 rounded-lg">
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <p class="text-sm text-gray-600">Pedido #</p>
                    <p class="font-semibold">{{ pedido.codigo || pedido.id_pedido }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-600">Cliente</p>
                    <p class="font-semibold">{{ pedido.cliente?.nombre || 'Cliente Anónimo' }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-600">Mesa</p>
                    <p class="font-semibold">{{ pedido.mesa?.numero || 'Para llevar' }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-600">Total a Pagar</p>
                    <p class="font-semibold text-green-600 text-xl">Bs. {{ formatMoney(pedido.total) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Detalles del pedido -->
            <div class="mb-8">
              <h4 class="text-md font-semibold text-gray-700 mb-3">Detalles del Pedido</h4>
              <div class="space-y-2">
                <div v-for="detalle in pedido.detalles" :key="detalle.id" class="flex justify-between items-center py-2 border-b">
                  <div>
                    <span class="font-medium">{{ detalle.producto.nombre }}</span>
                    <span class="text-gray-600 ml-2">x{{ detalle.cantidad }}</span>
                  </div>
                  <span class="font-semibold">Bs. {{ formatMoney(detalle.subtotal) }}</span>
                </div>
              </div>
            </div>

            <!-- Estado del pago -->
            <div v-if="pagoActual" class="mb-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Estado del Pago</h3>
                <span :class="getEstadoBadgeClass(pagoActual.estado)" class="px-3 py-1 rounded-full text-sm font-medium">
                  {{ getEstadoText(pagoActual.estado) }}
                </span>
              </div>

              <!-- Pago completado o pendiente con QR -->
              <div v-if="(pagoActual.estado === 'completado' || pagoActual.estado === 'pendiente') && pagoActual.qr_image" class="text-center">
                <div :class="pagoActual.estado === 'completado' ? 'bg-green-50 border border-green-200' : 'bg-blue-50 border border-blue-200'" class="rounded-lg p-6">
                  <h4 v-if="pagoActual.estado === 'completado'" class="font-semibold text-green-800 mb-4">¡Pago Completado!</h4>
                  <h4 v-else class="font-semibold text-blue-800 mb-4">Escanea el código QR para pagar</h4>

                  <!-- 🔑 TRANSACTION ID -->
                  <div v-if="pagoActual.transaction_id" class="mb-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">🔑 Transaction ID:</p>
                    <p class="font-mono text-lg font-bold text-blue-600 select-all">{{ pagoActual.transaction_id }}</p>
                    <p class="text-xs text-gray-500 mt-2">📋 ID del Pago: {{ pagoActual.id_pago }}</p>
                    <p class="text-xs text-gray-500 mt-1">🔗 Endpoint: <code class="bg-gray-100 px-2 py-1 rounded">GET /api/pagos/{{ pagoActual.id_pago }}/consultar</code></p>
                  </div>

                  <!-- Código QR -->
                  <div class="mb-4">
                    <img :src="pagoActual.qr_image" alt="Código QR" class="mx-auto bg-white p-2 rounded border max-w-xs">
                  </div>

                  <!-- Tiempo restante -->
                  <div v-if="tiempoRestante > 0 && pagoActual.estado === 'pendiente'" class="mb-4">
                    <p class="text-sm text-gray-600">Tiempo restante para pagar:</p>
                    <p class="font-mono text-lg font-semibold text-orange-600">{{ formatTime(tiempoRestante) }}</p>
                  </div>

                  <div class="space-y-3">
                    <button 
                      @click="consultarEstado" 
                      :disabled="consultando"
                      class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                      <span v-if="consultando">Consultando...</span>
                      <span v-else>Verificar Pago</span>
                    </button>
                    <button 
                      v-if="pagoActual.estado === 'pendiente'"
                      @click="cancelarPago" 
                      class="w-full bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                      Cancelar Pago
                    </button>
                  </div>
                  <div v-if="pagoActual.estado === 'completado'" class="mt-3 text-green-700 font-semibold">
                    El pago fue completado automáticamente por el sistema.
                  </div>
                </div>
              </div>
            </div>

            <!-- Formulario para generar QR -->
            <div v-if="!pagoActual || pagoActual.estado !== 'completado'">
              <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Datos para el Pago</h3>
                
                <form @submit.prevent="generarQR" class="space-y-4">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo *</label>
                      <input 
                        v-model="form.client_name" 
                        type="text" 
                        required 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ej: Juan Pérez">
                    </div>

                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Documento *</label>
                      <select v-model="form.document_type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1">Cédula de Identidad</option>
                        <option value="2">Pasaporte</option>
                      </select>
                    </div>

                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Número de Documento *</label>
                      <input 
                        v-model="form.document_id" 
                        type="text" 
                        required 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ej: 12345678">
                    </div>

                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono *</label>
                      <input 
                        v-model="form.phone_number" 
                        type="tel" 
                        required 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ej: 70123456">
                    </div>

                    <div class="md:col-span-2">
                      <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                      <input 
                        v-model="form.email" 
                        type="email" 
                        required 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ej: juan@email.com">
                    </div>
                  </div>

                  <button 
                    type="submit" 
                    :disabled="procesando"
                    class="w-full bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white font-medium py-3 px-6 rounded-lg transition duration-200">
                    <span v-if="procesando">Generando QR...</span>
                    <span v-else>Generar Código QR para Pagar</span>
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'

// Props
const props = defineProps({
  pedido: Object,
  pagoPendiente: Object
})

// Estado reactivo
const pagoActual = ref(props.pagoPendiente)
const procesando = ref(false)
const consultando = ref(false)
const tiempoRestante = ref(0)
let intervalTimer = null

// Formulario
const form = ref({
  client_name: '',
  document_type: '1',
  document_id: '',
  phone_number: '',
  email: ''
})

// Computed
const total = computed(() => props.pedido.total || 0)

// Métodos
const formatMoney = (amount) => {
  return new Intl.NumberFormat('es-BO', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount || 0)
}

const formatTime = (segundos) => {
  const mins = Math.floor(segundos / 60)
  const secs = segundos % 60
  return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
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

const generarQR = async () => {
  procesando.value = true

  try {
    const response = await fetch(`/pago/generar-qr/${props.pedido.id_pedido}`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      credentials: 'same-origin',
      body: JSON.stringify(form.value)
    })

    const data = await response.json()

    console.log('Respuesta generarQR:', data)

    if (data.success) {
      // Construir el objeto pagoActual con todos los datos necesarios
      pagoActual.value = {
        id_pago: data.pago.id_pago,
        estado: data.pago.estado,
        monto: data.pago.monto,
        qr_image: data.qr?.qr_image_url || null,
        transaction_id: data.qr?.transaction_id || null,
        expires_at: data.qr?.expiration_date || null
      }

      console.log('Pago actualizado:', pagoActual.value)

      // ✅ LOG ESPECIAL PARA POSTMAN
      console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
      console.log('📋 DATOS PARA PRUEBA EN POSTMAN:')
      console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
      console.log('🆔 ID del Pago:', data.pago.id_pago)
      console.log('🔑 Transaction ID:', data.qr?.transaction_id)
      console.log('🔗 Endpoint de consulta:', `GET http://localhost:8000/api/pagos/${data.pago.id_pago}/consultar`)
      console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━')

      // Si el pago ya está completado (fue pagado inmediatamente)
      if (data.pagado || data.pago.estado === 'completado') {
        alert('¡El pago ya fue completado! Redirigiendo...')
        setTimeout(() => {
          router.visit(`/pedidos/${props.pedido.id_pedido}`)
        }, 1500)
      } else {
        // Iniciar el timer para el QR pendiente
        iniciarTimer()
        alert('Código QR generado. Escanea para pagar.')
      }
    } else {
      console.error('Error en la respuesta:', data)
      alert(data.message || 'Error al generar el código QR')
    }
  } catch (error) {
    console.error('Error generando QR:', error)
    alert('Error al generar el código QR')
  } finally {
    procesando.value = false
  }
}

const consultarEstado = async () => {
  if (!pagoActual.value) return

  consultando.value = true

  try {
    const response = await fetch(`/pago/consultar/${pagoActual.value.id_pago}`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      credentials: 'same-origin'
    })

    const data = await response.json()

    console.log('Respuesta consultarEstado:', data)

    if (data.success) {
      // Actualizar estado del pago
      if (data.pago) {
        pagoActual.value = {
          ...pagoActual.value,
          estado: data.pago.estado,
          fecha_pago: data.pago.fecha_pago
        }

        console.log('Estado del pago:', data.pago.estado)
        console.log('Transaction data:', data.transaction_data)

        // Si el pago está completado, mostrar mensaje y redirigir
        if (data.pago.completado || data.pago.estado === 'completado') {
          detenerTimer()

          alert('¡Pago completado exitosamente! Redirigiendo...')

          // Redirigir al detalle del pedido
          setTimeout(() => {
            router.visit(`/pedidos/${props.pedido.id_pedido}`)
          }, 1500)
        } else {
          // Mostrar estado actual
          alert(`Estado del pago: ${data.pago.estado}`)
        }
      }
    } else {
      console.error('Error en la respuesta:', data)
      alert('Error al consultar el estado del pago')
    }
  } catch (error) {
    console.error('Error consultando estado:', error)
    alert('Error al consultar el estado del pago')
  } finally {
    consultando.value = false
  }
}

const cancelarPago = () => {
  if (!pagoActual.value) return
  
  if (confirm('¿Estás seguro de que deseas cancelar este pago?')) {
    router.post(`/pago/cancelar/${pagoActual.value.id_pago}`, {}, {
      onSuccess: () => {
        pagoActual.value = null
        detenerTimer()
      }
    })
  }
}

const calcularTiempoRestante = () => {
  if (!pagoActual.value?.expires_at) return 0
  
  const ahora = new Date()
  const expira = new Date(pagoActual.value.expires_at)
  const diff = Math.floor((expira - ahora) / 1000)
  
  return Math.max(0, diff)
}

const iniciarTimer = () => {
  detenerTimer()
  tiempoRestante.value = calcularTiempoRestante()
  
  intervalTimer = setInterval(() => {
    tiempoRestante.value = calcularTiempoRestante()
    
    if (tiempoRestante.value <= 0) {
      detenerTimer()
      // Auto-consultar estado cuando expire
      consultarEstado()
    }
  }, 1000)
}

const detenerTimer = () => {
  if (intervalTimer) {
    clearInterval(intervalTimer)
    intervalTimer = null
  }
}

// Lifecycle
onMounted(() => {
  if (pagoActual.value && pagoActual.value.estado === 'pendiente') {
    iniciarTimer()
  }
})

onUnmounted(() => {
  detenerTimer()
})
</script>