<template>
  <Head title="Detalle del Pedido" />

  <AppLayout>
    <template #header>
      <div class="d-flex justify-content-between align-items-center">
        <h2 class="fw-semibold text-xl text-gray-800 leading-tight">
          Pedido #{{ pedido.id_pedido }}
        </h2>
        <Link href="/pedidos" class="btn btn-secondary">
          <i class="bi bi-arrow-left me-2"></i>Volver a Pedidos
        </Link>
      </div>
    </template>

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          
          <!-- Información del pedido -->
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="bi bi-clipboard-data me-2"></i>
                Información del Pedido
              </h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="row mb-3">
                    <div class="col-4">
                      <strong>Estado:</strong>
                    </div>
                    <div class="col-8">
                      <span :class="getBadgeClass(pedido.estado)">
                        {{ getEstadoTexto(pedido.estado) }}
                      </span>
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <div class="col-4">
                      <strong>Mesa:</strong>
                    </div>
                    <div class="col-8">
                      <span class="badge bg-secondary">
                        Mesa #{{ pedido.mesa?.numero_mesa || 'N/A' }}
                      </span>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <div class="col-4">
                      <strong>Cliente:</strong>
                    </div>
                    <div class="col-8">
                      {{ pedido.cliente?.nombre || 'Sin cliente' }} {{ pedido.cliente?.apellido || '' }}
                    </div>
                  </div>

                  <div class="row mb-3">
                    <div class="col-4">
                      <strong>Mesero:</strong>
                    </div>
                    <div class="col-8">
                      {{ pedido.mesero?.nombre }} {{ pedido.mesero?.apellido }}
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="row mb-3">
                    <div class="col-4">
                      <strong>Fecha:</strong>
                    </div>
                    <div class="col-8">
                      {{ formatFecha(pedido.fecha_pedido) }}
                    </div>
                  </div>

                  <div class="row mb-3">
                    <div class="col-4">
                      <strong>Total:</strong>
                    </div>
                    <div class="col-8">
                      <strong class="text-success fs-5">Bs. {{ formatMoney(pedido.total) }}</strong>
                    </div>
                  </div>

                  <div class="row mb-3" v-if="pedido.observaciones">
                    <div class="col-4">
                      <strong>Observaciones:</strong>
                    </div>
                    <div class="col-8">
                      {{ pedido.observaciones }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Detalles del pedido -->
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="bi bi-list-check me-2"></i>
                Detalles del Pedido
              </h5>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead class="table-light">
                    <tr>
                      <th>Producto</th>
                      <th>Cantidad</th>
                      <th>Precio Unitario</th>
                      <th>Subtotal</th>
                      <th>Observaciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="detalle in pedido.detalles" :key="detalle.id">
                      <td>
                        <div>
                          <strong>{{ detalle.producto?.nombre || 'Producto eliminado' }}</strong>
                          <small class="d-block text-muted" v-if="detalle.producto?.categoria">
                            {{ detalle.producto.categoria.nombre }}
                          </small>
                        </div>
                      </td>
                      <td>
                        <span class="badge bg-info">{{ detalle.cantidad }}</span>
                      </td>
                      <td>Bs. {{ formatMoney(detalle.precio_unitario) }}</td>
                      <td><strong>Bs. {{ formatMoney(detalle.subtotal) }}</strong></td>
                      <td>
                        <small class="text-muted">{{ detalle.observaciones || '-' }}</small>
                      </td>
                    </tr>
                  </tbody>
                  <tfoot class="table-light">
                    <tr>
                      <td colspan="3" class="text-end"><strong>Total:</strong></td>
                      <td><strong class="text-success fs-5">Bs. {{ formatMoney(pedido.total) }}</strong></td>
                      <td></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>

          <!-- Estado de pago y acciones -->
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="bi bi-credit-card me-2"></i>
                Estado de Pago
              </h5>
            </div>
            <div class="card-body">
              <!-- Estado actual del pago -->
              <div class="row mb-4">
                <div class="col-md-6">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill text-info me-2"></i>
                    <div>
                      <strong>Estado de Pago: </strong>
                      <span :class="getPagoBadgeClass(pedido.estado_pago || 'pendiente')">
                        {{ getPagoTexto(pedido.estado_pago || 'pendiente') }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Mostrar pagos existentes si los hay -->
              <div v-if="pagosExistentes && pagosExistentes.length > 0" class="mb-4">
                <h6><i class="bi bi-clock-history me-2"></i>Historial de Pagos:</h6>
                <div class="table-responsive">
                  <table class="table table-sm">
                    <thead>
                      <tr>
                        <th>ID Pago</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="pago in pagosExistentes" :key="pago.id_pago">
                        <td>#{{ pago.id_pago }}</td>
                        <td>Bs. {{ formatMoney(pago.monto) }}</td>
                        <td>
                          <span :class="getPagoBadgeClass(pago.estado)">
                            {{ getPagoTexto(pago.estado) }}
                          </span>
                        </td>
                        <td>{{ formatFecha(pago.fecha_pago) }}</td>
                        <td>
                          <Link :href="`/pagos/${pago.id_pago}`" class="btn btn-sm btn-outline-info">
                            Ver Detalle
                          </Link>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Acciones de pago -->
              <div class="d-flex gap-3">
                <!-- Botón principal de pago -->
                <div v-if="!tienePagoCompletado">
                  <Link 
                    :href="`/pago/pedido/${pedido.id_pedido}`" 
                    class="btn btn-success btn-lg">
                    <i class="bi bi-qr-code me-2"></i>
                    Pagar con QR - PagoFácil
                  </Link>
                  <small class="d-block text-muted mt-1">
                    Genera un código QR para pago instantáneo
                  </small>
                </div>

                <!-- Mensaje si ya está pagado -->
                <div v-else>
                  <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>¡Pedido Pagado!</strong> Este pedido ya ha sido pagado exitosamente.
                  </div>
                </div>

                <!-- Otras opciones de pago -->
                <div class="dropdown">
                  <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots me-2"></i>
                    Más Opciones
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" @click="marcarPagadoEfectivo">
                      <i class="bi bi-cash me-2"></i>Marcar como Pagado (Efectivo)
                    </a></li>
                    <li><a class="dropdown-item" href="#" @click="marcarPagadoTarjeta">
                      <i class="bi bi-credit-card me-2"></i>Marcar como Pagado (Tarjeta)
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><Link class="dropdown-item" href="/pagos">
                      <i class="bi bi-list-ul me-2"></i>Ver Todos los Pagos
                    </Link></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <!-- Acciones adicionales -->
          <div class="card mt-4" v-if="puedeEditar">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="bi bi-gear me-2"></i>
                Acciones del Pedido
              </h5>
            </div>
            <div class="card-body">
              <div class="d-flex gap-2">
                <button 
                  v-if="pedido.estado === 'pendiente'"
                  @click="cambiarEstado('en_preparacion')"
                  class="btn btn-warning">
                  <i class="bi bi-play-fill me-2"></i>Iniciar Preparación
                </button>

                <button 
                  v-if="pedido.estado === 'en_preparacion'"
                  @click="cambiarEstado('listo')"
                  class="btn btn-info">
                  <i class="bi bi-check-circle me-2"></i>Marcar como Listo
                </button>

                <button 
                  v-if="pedido.estado === 'listo'"
                  @click="cambiarEstado('entregado')"
                  class="btn btn-success">
                  <i class="bi bi-box-arrow-up me-2"></i>Marcar como Entregado
                </button>

                <Link 
                  v-if="pedido.estado === 'pendiente'"
                  :href="`/pedidos/${pedido.id_pedido}/edit`"
                  class="btn btn-outline-primary">
                  <i class="bi bi-pencil me-2"></i>Editar Pedido
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Props
const props = defineProps({
  pedido: Object,
  pagosExistentes: Array
})

// Computed
const tienePagoCompletado = computed(() => {
  return props.pedido.estado_pago === 'pagado' || 
         (props.pagosExistentes && props.pagosExistentes.some(pago => pago.estado === 'completado'))
})

const puedeEditar = computed(() => {
  // Solo permitir edición si es pendiente o en preparación
  return ['pendiente', 'en_preparacion'].includes(props.pedido.estado)
})

// Métodos
const formatMoney = (amount) => {
  return new Intl.NumberFormat('es-BO', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount || 0)
}

const formatFecha = (fecha) => {
  if (!fecha) return 'N/A'
  return new Date(fecha).toLocaleString('es-BO', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getBadgeClass = (estado) => {
  const classes = {
    'pendiente': 'badge bg-warning',
    'en_preparacion': 'badge bg-info',
    'listo': 'badge bg-primary',
    'entregado': 'badge bg-success',
    'cancelado': 'badge bg-danger'
  }
  return classes[estado] || 'badge bg-secondary'
}

const getEstadoTexto = (estado) => {
  const textos = {
    'pendiente': 'Pendiente',
    'en_preparacion': 'En Preparación',
    'listo': 'Listo',
    'entregado': 'Entregado',
    'cancelado': 'Cancelado'
  }
  return textos[estado] || estado
}

const getPagoBadgeClass = (estado) => {
  const classes = {
    'pendiente': 'badge bg-warning',
    'pagado': 'badge bg-success',
    'completado': 'badge bg-success',
    'cancelado': 'badge bg-danger'
  }
  return classes[estado] || 'badge bg-secondary'
}

const getPagoTexto = (estado) => {
  const textos = {
    'pendiente': 'Pendiente de Pago',
    'pagado': 'Pagado',
    'completado': 'Pagado',
    'cancelado': 'Cancelado'
  }
  return textos[estado] || estado
}

const cambiarEstado = (nuevoEstado) => {
  if (confirm(`¿Cambiar estado del pedido a "${getEstadoTexto(nuevoEstado)}"?`)) {
    router.post(`/pedidos/${props.pedido.id_pedido}/estado`, {
      estado: nuevoEstado
    }, {
      onSuccess: () => {
        router.reload()
      }
    })
  }
}

const marcarPagadoEfectivo = () => {
  if (confirm('¿Marcar este pedido como pagado en efectivo?')) {
    router.post('/pagos/marcar-pagado', {
      pedido_id: props.pedido.id_pedido,
      metodo: 'efectivo',
      monto: props.pedido.total
    }, {
      onSuccess: () => {
        router.reload()
      }
    })
  }
}

const marcarPagadoTarjeta = () => {
  if (confirm('¿Marcar este pedido como pagado con tarjeta?')) {
    router.post('/pagos/marcar-pagado', {
      pedido_id: props.pedido.id_pedido,
      metodo: 'tarjeta',
      monto: props.pedido.total
    }, {
      onSuccess: () => {
        router.reload()
      }
    })
  }
}
</script>

<style scoped>
.btn-group-sm > .btn, .btn-sm {
  --bs-btn-padding-y: 0.25rem;
  --bs-btn-padding-x: 0.5rem;
}
</style>