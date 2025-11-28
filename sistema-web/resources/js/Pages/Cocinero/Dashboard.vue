<template>
  <Head title="Panel Cocinero" />
  
  <Layout>
    <div class="cocinero-dashboard">
      <!-- Header del Dashboard -->
      <div class="dashboard-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="h2 mb-0">
              <i class="fas fa-utensils me-2"></i>
              Panel de Cocina
            </h1>
            <p class="text-muted mb-0">Gestión de pedidos y preparación de alimentos</p>
          </div>
          <div class="header-actions">
            <button class="btn btn-info" @click="actualizarPedidos">
              <i class="fas fa-sync me-2"></i>
              Actualizar
            </button>
          </div>
        </div>
      </div>

      <!-- Estadísticas de Cocina -->
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="stat-card card border-0 shadow-sm bg-danger text-white">
            <div class="card-body text-center">
              <i class="fas fa-clock fa-2x mb-2"></i>
              <h4>{{ estadisticas.pedidosPendientes }}</h4>
              <p class="mb-0">Pedidos Pendientes</p>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="stat-card card border-0 shadow-sm bg-warning text-white">
            <div class="card-body text-center">
              <i class="fas fa-fire fa-2x mb-2"></i>
              <h4>{{ estadisticas.enPreparacion }}</h4>
              <p class="mb-0">En Preparación</p>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="stat-card card border-0 shadow-sm bg-success text-white">
            <div class="card-body text-center">
              <i class="fas fa-check-circle fa-2x mb-2"></i>
              <h4>{{ estadisticas.completadosHoy }}</h4>
              <p class="mb-0">Completados Hoy</p>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="stat-card card border-0 shadow-sm bg-info text-white">
            <div class="card-body text-center">
              <i class="fas fa-stopwatch fa-2x mb-2"></i>
              <h4>{{ estadisticas.tiempoPromedio }}min</h4>
              <p class="mb-0">Tiempo Promedio</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel de Pedidos -->
      <div class="row">
        <!-- Cola de Pedidos -->
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                  <i class="fas fa-list me-2"></i>
                  Cola de Pedidos
                </h5>
                <div class="filtros">
                  <button 
                    class="btn btn-sm"
                    :class="filtroActivo === 'todos' ? 'btn-primary' : 'btn-outline-primary'"
                    @click="cambiarFiltro('todos')"
                  >
                    Todos
                  </button>
                  <button 
                    class="btn btn-sm"
                    :class="filtroActivo === 'urgente' ? 'btn-danger' : 'btn-outline-danger'"
                    @click="cambiarFiltro('urgente')"
                  >
                    Urgentes
                  </button>
                </div>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="pedidos-queue">
                <div 
                  v-for="pedido in pedidosFiltrados" 
                  :key="pedido.id"
                  class="pedido-card"
                  :class="pedido.prioridad"
                  @click="seleccionarPedido(pedido)"
                >
                  <div class="pedido-header">
                    <div class="d-flex justify-content-between align-items-center">
                      <div class="pedido-info">
                        <h6 class="mb-0">
                          <i class="fas fa-table me-1"></i>
                          Mesa {{ pedido.mesa }}
                          <span v-if="pedido.esLlevar" class="badge bg-info ms-2">
                            <i class="fas fa-box me-1"></i>Para Llevar
                          </span>
                        </h6>
                        <small class="text-muted">{{ pedido.hora }} - {{ pedido.tiempoEspera }}min</small>
                      </div>
                      <div class="pedido-estado">
                        <span :class="`badge bg-${pedido.estadoColor} fs-6`">
                          {{ pedido.estado }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <div class="pedido-items">
                    <div 
                      v-for="item in pedido.items" 
                      :key="item.id"
                      class="item-row"
                      :class="{ 'item-listo': item.listo }"
                    >
                      <div class="d-flex justify-content-between align-items-center">
                        <div class="item-info">
                          <span class="cantidad-badge">{{ item.cantidad }}x</span>
                          <strong class="item-nombre">{{ item.nombre }}</strong>
                          <div v-if="item.observaciones" class="item-observaciones">
                            <small class="text-muted">
                              <i class="fas fa-comment me-1"></i>
                              {{ item.observaciones }}
                            </small>
                          </div>
                        </div>
                        <div class="item-acciones">
                          <button 
                            v-if="!item.listo && pedido.estado !== 'completado'"
                            @click.stop="marcarItemListo(pedido.id, item.id)"
                            class="btn btn-sm btn-outline-success"
                          >
                            <i class="fas fa-check"></i>
                          </button>
                          <span v-if="item.listo" class="text-success">
                            <i class="fas fa-check-circle"></i>
                          </span>
                          <span class="tiempo-estimado ms-2">
                            <small>{{ item.tiempoEstimado }}min</small>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="pedido-acciones mt-3">
                    <div class="d-flex justify-content-between align-items-center">
                      <div class="tiempo-info">
                        <small class="text-muted">
                          <i class="fas fa-clock me-1"></i>
                          Tiempo estimado: {{ pedido.tiempoEstimadoTotal }}min
                        </small>
                      </div>
                      <div class="acciones">
                        <button 
                          v-if="pedido.estado === 'pendiente'"
                          @click.stop="iniciarPreparacion(pedido.id)"
                          class="btn btn-warning btn-sm me-2"
                        >
                          <i class="fas fa-play me-1"></i>
                          Iniciar
                        </button>
                        <button 
                          v-if="pedido.estado === 'preparando' && todosItemsListos(pedido)"
                          @click.stop="completarPedido(pedido.id)"
                          class="btn btn-success btn-sm me-2"
                        >
                          <i class="fas fa-check me-1"></i>
                          Completar
                        </button>
                        <button 
                          @click.stop="verDetallePedido(pedido.id)"
                          class="btn btn-outline-info btn-sm"
                        >
                          <i class="fas fa-eye"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-if="pedidosFiltrados.length === 0" class="text-center py-5 text-muted">
                  <i class="fas fa-utensils fa-3x mb-3"></i>
                  <h5>No hay pedidos pendientes</h5>
                  <p>¡Excelente trabajo! La cocina está al día.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Panel de Control -->
        <div class="col-lg-4">
          <!-- Inventario Crítico -->
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
              <h6 class="card-title mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Inventario Crítico
              </h6>
            </div>
            <div class="card-body">
              <div v-if="inventarioCritico.length === 0" class="text-center text-success">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <p class="mb-0">Todo en orden</p>
              </div>
              <div v-else class="inventario-alerts">
                <div 
                  v-for="item in inventarioCritico" 
                  :key="item.id"
                  class="alert alert-warning d-flex justify-content-between align-items-center p-2 mb-2"
                >
                  <div>
                    <strong>{{ item.nombre }}</strong><br>
                    <small>Stock: {{ item.stock }} {{ item.unidad }}</small>
                  </div>
                  <button @click="solicitarReposicion(item.id)" class="btn btn-sm btn-warning">
                    <i class="fas fa-plus"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Tiempos de Preparación -->
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
              <h6 class="card-title mb-0">
                <i class="fas fa-clock me-2"></i>
                Tiempos de Preparación
              </h6>
            </div>
            <div class="card-body">
              <div class="tiempos-list">
                <div 
                  v-for="tiempo in tiemposPreparacion" 
                  :key="tiempo.plato"
                  class="tiempo-item d-flex justify-content-between align-items-center mb-2"
                >
                  <span class="plato-nombre">{{ tiempo.plato }}</span>
                  <span class="badge bg-secondary">{{ tiempo.minutos }}min</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Estadísticas del Día -->
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
              <h6 class="card-title mb-0">
                <i class="fas fa-chart-bar me-2"></i>
                Rendimiento del Día
              </h6>
            </div>
            <div class="card-body">
              <div class="estadistica-item d-flex justify-content-between mb-2">
                <span>Platos preparados:</span>
                <strong>{{ rendimiento.platosPreparados }}</strong>
              </div>
              <div class="estadistica-item d-flex justify-content-between mb-2">
                <span>Tiempo promedio:</span>
                <strong>{{ rendimiento.tiempoPromedio }}min</strong>
              </div>
              <div class="estadistica-item d-flex justify-content-between mb-2">
                <span>Eficiencia:</span>
                <strong class="text-success">{{ rendimiento.eficiencia }}%</strong>
              </div>
              <div class="progress mt-3">
                <div 
                  class="progress-bar bg-success" 
                  role="progressbar" 
                  :style="`width: ${rendimiento.eficiencia}%`"
                >
                  {{ rendimiento.eficiencia }}%
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
import { Head } from '@inertiajs/vue3'
import Layout from '@/Layouts/AppLayout.vue'

export default {
  name: 'CocineroDashboard',
  components: {
    Head,
    Layout
  },
  props: {
    estadisticas: {
      type: Object,
      default: () => ({
        pedidosPendientes: 4,
        enPreparacion: 2,
        completadosHoy: 28,
        tiempoPromedio: 18
      })
    }
  },
  data() {
    return {
      filtroActivo: 'todos',
      pedidoSeleccionado: null,
      // Datos de ejemplo
      pedidos: [
        {
          id: 1,
          mesa: 5,
          hora: '14:30',
          tiempoEspera: 8,
          estado: 'pendiente',
          estadoColor: 'danger',
          prioridad: 'urgente',
          esLlevar: false,
          tiempoEstimadoTotal: 25,
          items: [
            { id: 1, cantidad: 2, nombre: 'Asado de Tira', observaciones: 'Término medio', listo: false, tiempoEstimado: 20 },
            { id: 2, cantidad: 1, nombre: 'Ensalada Mixta', observaciones: null, listo: false, tiempoEstimado: 5 },
            { id: 3, cantidad: 3, nombre: 'Papas Fritas', observaciones: 'Bien doradas', listo: false, tiempoEstimado: 10 }
          ]
        },
        {
          id: 2,
          mesa: 8,
          hora: '14:25',
          tiempoEspera: 13,
          estado: 'preparando',
          estadoColor: 'warning',
          prioridad: 'normal',
          esLlevar: false,
          tiempoEstimadoTotal: 15,
          items: [
            { id: 4, cantidad: 1, nombre: 'Picaña', observaciones: null, listo: true, tiempoEstimado: 15 },
            { id: 5, cantidad: 2, nombre: 'Arroz', observaciones: null, listo: false, tiempoEstimado: 5 }
          ]
        },
        {
          id: 3,
          mesa: 0,
          hora: '14:35',
          tiempoEspera: 3,
          estado: 'pendiente',
          estadoColor: 'danger',
          prioridad: 'normal',
          esLlevar: true,
          tiempoEstimadoTotal: 20,
          items: [
            { id: 6, cantidad: 3, nombre: 'Hamburguesa', observaciones: 'Sin cebolla', listo: false, tiempoEstimado: 15 },
            { id: 7, cantidad: 3, nombre: 'Papas Fritas', observaciones: null, listo: false, tiempoEstimado: 10 }
          ]
        }
      ],
      inventarioCritico: [
        { id: 1, nombre: 'Carne de Res', stock: 3, unidad: 'kg' },
        { id: 2, nombre: 'Papas', stock: 5, unidad: 'kg' }
      ],
      tiemposPreparacion: [
        { plato: 'Asado de Tira', minutos: 20 },
        { plato: 'Picaña', minutos: 15 },
        { plato: 'Pollo Parrilla', minutos: 18 },
        { plato: 'Ensalada', minutos: 5 },
        { plato: 'Papas Fritas', minutos: 10 }
      ],
      rendimiento: {
        platosPreparados: 45,
        tiempoPromedio: 16.5,
        eficiencia: 92
      }
    }
  },
  computed: {
    pedidosFiltrados() {
      if (this.filtroActivo === 'urgente') {
        return this.pedidos.filter(pedido => pedido.prioridad === 'urgente' || pedido.tiempoEspera > 10)
      }
      return this.pedidos.sort((a, b) => {
        // Ordenar por prioridad y tiempo de espera
        if (a.prioridad === 'urgente' && b.prioridad !== 'urgente') return -1
        if (b.prioridad === 'urgente' && a.prioridad !== 'urgente') return 1
        return b.tiempoEspera - a.tiempoEspera
      })
    }
  },
  mounted() {
    this.iniciarActualizacionAutomatica()
  },
  beforeUnmount() {
    if (this.intervalId) {
      clearInterval(this.intervalId)
    }
  },
  methods: {
    cambiarFiltro(filtro) {
      this.filtroActivo = filtro
    },

    seleccionarPedido(pedido) {
      this.pedidoSeleccionado = pedido
    },

    iniciarPreparacion(pedidoId) {
      const pedido = this.pedidos.find(p => p.id === pedidoId)
      if (pedido) {
        pedido.estado = 'preparando'
        pedido.estadoColor = 'warning'
        
        this.$inertia.post('/cocinero/iniciar-preparacion', { 
          pedido_id: pedidoId 
        })
      }
    },

    marcarItemListo(pedidoId, itemId) {
      const pedido = this.pedidos.find(p => p.id === pedidoId)
      if (pedido) {
        const item = pedido.items.find(i => i.id === itemId)
        if (item) {
          item.listo = true
          
          this.$inertia.post('/cocinero/marcar-item-listo', {
            pedido_id: pedidoId,
            item_id: itemId
          })
        }
      }
    },

    todosItemsListos(pedido) {
      return pedido.items.every(item => item.listo)
    },

    completarPedido(pedidoId) {
      const pedido = this.pedidos.find(p => p.id === pedidoId)
      if (pedido && this.todosItemsListos(pedido)) {
        pedido.estado = 'completado'
        pedido.estadoColor = 'success'
        
        this.$inertia.post('/cocinero/completar-pedido', { 
          pedido_id: pedidoId 
        }, {
          onSuccess: () => {
            // Remover pedido de la lista después de 2 segundos
            setTimeout(() => {
              this.pedidos = this.pedidos.filter(p => p.id !== pedidoId)
            }, 2000)
          }
        })
      }
    },

    verDetallePedido(pedidoId) {
      this.$inertia.get(`/cocinero/pedidos/${pedidoId}`)
    },

    solicitarReposicion(itemId) {
      this.$inertia.post('/cocinero/solicitar-reposicion', {
        item_id: itemId
      })
    },

    actualizarPedidos() {
      this.$inertia.reload({ only: ['pedidos', 'estadisticas'] })
    },

    iniciarActualizacionAutomatica() {
      // Actualizar tiempos de espera cada minuto
      this.intervalId = setInterval(() => {
        this.pedidos.forEach(pedido => {
          if (pedido.estado !== 'completado') {
            pedido.tiempoEspera += 1
            // Marcar como urgente si pasa de 15 minutos
            if (pedido.tiempoEspera > 15) {
              pedido.prioridad = 'urgente'
            }
          }
        })
      }, 60000) // Cada minuto
    }
  }
}
</script>

<style scoped>
.cocinero-dashboard {
  padding: 20px;
}

.dashboard-header {
  background: linear-gradient(135deg, #e83e8c 0%, #fd7e14 100%);
  color: white;
  padding: 30px;
  border-radius: 10px;
  margin-bottom: 30px;
}

.stat-card {
  transition: transform 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-3px);
}

.pedidos-queue {
  max-height: 600px;
  overflow-y: auto;
  padding: 15px;
}

.pedido-card {
  border: 2px solid #dee2e6;
  border-radius: 10px;
  padding: 20px;
  margin-bottom: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
  background: white;
}

.pedido-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.pedido-card.urgente {
  border-left: 6px solid #dc3545;
  background: rgba(220, 53, 69, 0.05);
}

.pedido-card.normal {
  border-left: 6px solid #28a745;
}

.item-row {
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 8px;
  background: #f8f9fa;
  transition: all 0.2s ease;
}

.item-row:hover {
  background: #e9ecef;
}

.item-listo {
  background: rgba(40, 167, 69, 0.1) !important;
  text-decoration: line-through;
  opacity: 0.7;
}

.cantidad-badge {
  background: #007bff;
  color: white;
  padding: 2px 8px;
  border-radius: 10px;
  font-size: 0.8em;
  margin-right: 10px;
  font-weight: bold;
}

.item-nombre {
  font-size: 1.1em;
}

.item-observaciones {
  margin-top: 5px;
  padding: 5px 10px;
  background: rgba(255, 193, 7, 0.1);
  border-radius: 4px;
  border-left: 3px solid #ffc107;
}

.tiempo-estimado {
  color: #6c757d;
  font-weight: 500;
}

.inventario-alerts .alert {
  border-radius: 8px;
}

.tiempos-list {
  max-height: 200px;
  overflow-y: auto;
}

.tiempo-item {
  padding: 8px 0;
  border-bottom: 1px solid #f1f3f4;
}

.tiempo-item:last-child {
  border-bottom: none;
}

.estadistica-item {
  padding: 5px 0;
  font-size: 0.95em;
}

.filtros .btn {
  margin-left: 5px;
}

@keyframes pulse-urgente {
  0% { border-color: #dc3545; }
  50% { border-color: #ff6b7a; }
  100% { border-color: #dc3545; }
}

.pedido-card.urgente {
  animation: pulse-urgente 2s infinite;
}
</style>