<template>
  <Head title="Panel Mesero" />
  
  <Layout>
    <div class="mesero-dashboard">
      <!-- Header del Dashboard -->
      <div class="dashboard-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="h2 mb-0">
              <i class="fas fa-user-tie me-2"></i>
              Panel de Mesero
            </h1>
            <p class="text-muted mb-0">Gestión de mesas, pedidos y atención al cliente</p>
          </div>
          <div class="header-actions">
            <button class="btn btn-warning" @click="tomarPedido">
              <i class="fas fa-plus me-2"></i>
              Nuevo Pedido
            </button>
          </div>
        </div>
      </div>

      <!-- Estadísticas del Mesero -->
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="stat-card card border-0 shadow-sm bg-primary text-white">
            <div class="card-body text-center">
              <i class="fas fa-table fa-2x mb-2"></i>
              <h4>{{ estadisticas.mesasAsignadas }}</h4>
              <p class="mb-0">Mesas Asignadas</p>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="stat-card card border-0 shadow-sm bg-warning text-white">
            <div class="card-body text-center">
              <i class="fas fa-clock fa-2x mb-2"></i>
              <h4>{{ estadisticas.pedidosPendientes }}</h4>
              <p class="mb-0">Pedidos Pendientes</p>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="stat-card card border-0 shadow-sm bg-success text-white">
            <div class="card-body text-center">
              <i class="fas fa-check-circle fa-2x mb-2"></i>
              <h4>{{ estadisticas.pedidosCompletados }}</h4>
              <p class="mb-0">Completados Hoy</p>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="stat-card card border-0 shadow-sm bg-info text-white">
            <div class="card-body text-center">
              <i class="fas fa-dollar-sign fa-2x mb-2"></i>
              <h4>Bs. {{ estadisticas.ventasHoy }}</h4>
              <p class="mb-0">Ventas Hoy</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Vista de Mesas -->
      <div class="row">
        <!-- Plano de Mesas -->
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                  <i class="fas fa-map me-2"></i>
                  Plano del Restaurante
                </h5>
                <div class="mesa-legend">
                  <span class="badge bg-success me-2">
                    <i class="fas fa-circle me-1"></i>Disponible
                  </span>
                  <span class="badge bg-warning me-2">
                    <i class="fas fa-circle me-1"></i>Ocupada
                  </span>
                  <span class="badge bg-danger me-2">
                    <i class="fas fa-circle me-1"></i>Reservada
                  </span>
                  <span class="badge bg-info">
                    <i class="fas fa-circle me-1"></i>Limpieza
                  </span>
                </div>
              </div>
            </div>
            <div class="card-body">
              <div class="restaurant-layout">
                <!-- Área Terraza -->
                <div class="area-section mb-4">
                  <h6 class="area-title">
                    <i class="fas fa-sun me-2"></i>
                    Terraza
                  </h6>
                  <div class="mesas-grid">
                    <div 
                      v-for="mesa in mesasTerraza" 
                      :key="mesa.id"
                      @click="seleccionarMesa(mesa)"
                      class="mesa-item"
                      :class="`mesa-${mesa.estado}`"
                    >
                      <div class="mesa-numero">{{ mesa.numero }}</div>
                      <div class="mesa-info">
                        <small>{{ mesa.capacidad }} per.</small>
                        <div class="mesa-tiempo" v-if="mesa.tiempoOcupada">
                          <small>{{ mesa.tiempoOcupada }}min</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Área Interior -->
                <div class="area-section">
                  <h6 class="area-title">
                    <i class="fas fa-home me-2"></i>
                    Interior
                  </h6>
                  <div class="mesas-grid">
                    <div 
                      v-for="mesa in mesasInterior" 
                      :key="mesa.id"
                      @click="seleccionarMesa(mesa)"
                      class="mesa-item"
                      :class="`mesa-${mesa.estado}`"
                    >
                      <div class="mesa-numero">{{ mesa.numero }}</div>
                      <div class="mesa-info">
                        <small>{{ mesa.capacidad }} per.</small>
                        <div class="mesa-tiempo" v-if="mesa.tiempoOcupada">
                          <small>{{ mesa.tiempoOcupada }}min</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Panel de Acciones -->
        <div class="col-lg-4">
          <!-- Mesa Seleccionada -->
          <div v-if="mesaSeleccionada" class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-light">
              <h6 class="card-title mb-0">
                <i class="fas fa-table me-2"></i>
                Mesa {{ mesaSeleccionada.numero }}
              </h6>
            </div>
            <div class="card-body">
              <div class="mesa-details">
                <div class="detail-item d-flex justify-content-between mb-2">
                  <span>Estado:</span>
                  <span :class="`badge bg-${mesaSeleccionada.estadoColor}`">
                    {{ mesaSeleccionada.estadoTexto }}
                  </span>
                </div>
                <div class="detail-item d-flex justify-content-between mb-2">
                  <span>Capacidad:</span>
                  <span>{{ mesaSeleccionada.capacidad }} personas</span>
                </div>
                <div class="detail-item d-flex justify-content-between mb-2">
                  <span>Ubicación:</span>
                  <span>{{ mesaSeleccionada.ubicacion }}</span>
                </div>
                <div v-if="mesaSeleccionada.cliente" class="detail-item d-flex justify-content-between mb-3">
                  <span>Cliente:</span>
                  <span>{{ mesaSeleccionada.cliente }}</span>
                </div>

                <!-- Acciones de Mesa -->
                <div class="mesa-actions">
                  <button 
                    v-if="mesaSeleccionada.estado === 'disponible'"
                    @click="ocuparMesa"
                    class="btn btn-success btn-sm w-100 mb-2"
                  >
                    <i class="fas fa-user-check me-2"></i>
                    Ocupar Mesa
                  </button>

                  <button 
                    v-if="mesaSeleccionada.estado === 'ocupada'"
                    @click="tomarPedidoMesa"
                    class="btn btn-warning btn-sm w-100 mb-2"
                  >
                    <i class="fas fa-clipboard-list me-2"></i>
                    Tomar Pedido
                  </button>

                  <button 
                    v-if="mesaSeleccionada.estado === 'ocupada'"
                    @click="verPedidoActual"
                    class="btn btn-info btn-sm w-100 mb-2"
                  >
                    <i class="fas fa-eye me-2"></i>
                    Ver Pedido
                  </button>

                  <button 
                    v-if="mesaSeleccionada.estado === 'ocupada'"
                    @click="liberarMesa"
                    class="btn btn-danger btn-sm w-100 mb-2"
                  >
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Liberar Mesa
                  </button>

                  <button 
                    v-if="mesaSeleccionada.estado === 'limpieza'"
                    @click="marcarLimpia"
                    class="btn btn-success btn-sm w-100 mb-2"
                  >
                    <i class="fas fa-check me-2"></i>
                    Marcar como Limpia
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Pedidos Activos -->
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
              <h6 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>
                Mis Pedidos Activos
              </h6>
            </div>
            <div class="card-body">
              <div v-if="pedidosActivos.length === 0" class="text-center py-3 text-muted">
                <i class="fas fa-clipboard fa-2x mb-2"></i>
                <p class="mb-0">No hay pedidos activos</p>
              </div>

              <div v-else class="pedidos-list">
                <div 
                  v-for="pedido in pedidosActivos" 
                  :key="pedido.id"
                  class="pedido-item card mb-2 border-start border-4"
                  :class="`border-${pedido.prioridadColor}`"
                >
                  <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <div>
                        <strong>Mesa {{ pedido.mesa }}</strong>
                        <br>
                        <small class="text-muted">{{ pedido.hora }}</small>
                      </div>
                      <span :class="`badge bg-${pedido.estadoColor}`">
                        {{ pedido.estado }}
                      </span>
                    </div>
                    
                    <div class="pedido-items mb-2">
                      <small v-for="item in pedido.items.slice(0, 2)" :key="item.id" class="d-block">
                        {{ item.cantidad }}x {{ item.nombre }}
                      </small>
                      <small v-if="pedido.items.length > 2" class="text-muted">
                        +{{ pedido.items.length - 2 }} más...
                      </small>
                    </div>

                    <div class="pedido-actions">
                      <button @click="verDetallePedido(pedido.id)" class="btn btn-sm btn-outline-primary me-1">
                        <i class="fas fa-eye"></i>
                      </button>
                      <button @click="marcarListo(pedido.id)" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-check"></i>
                      </button>
                    </div>
                  </div>
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
  name: 'MeseroDashboard',
  components: {
    Head,
    Layout
  },
  props: {
    estadisticas: {
      type: Object,
      default: () => ({
        mesasAsignadas: 8,
        pedidosPendientes: 3,
        pedidosCompletados: 12,
        ventasHoy: '1,240'
      })
    },
    mesas: {
      type: Array,
      default: () => []
    },
    pedidos: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      mesaSeleccionada: null,
      // Datos de ejemplo - en producción vendrán de props/API
      todasLasMesas: [
        // Terraza
        { id: 1, numero: 1, capacidad: 2, ubicacion: 'Terraza', estado: 'disponible', area: 'terraza' },
        { id: 2, numero: 2, capacidad: 2, ubicacion: 'Terraza', estado: 'ocupada', area: 'terraza', tiempoOcupada: 25, cliente: 'familia López' },
        { id: 3, numero: 7, capacidad: 4, ubicacion: 'Terraza', estado: 'reservada', area: 'terraza', cliente: 'Sr. González' },
        { id: 4, numero: 8, capacidad: 4, ubicacion: 'Terraza', estado: 'disponible', area: 'terraza' },
        { id: 5, numero: 10, capacidad: 6, ubicacion: 'Terraza', estado: 'ocupada', area: 'terraza', tiempoOcupada: 45, cliente: 'Grupo empresarial' },
        
        // Interior
        { id: 6, numero: 3, capacidad: 2, ubicacion: 'Interior', estado: 'limpieza', area: 'interior' },
        { id: 7, numero: 4, capacidad: 2, ubicacion: 'Interior', estado: 'disponible', area: 'interior' },
        { id: 8, numero: 5, capacidad: 4, ubicacion: 'Interior', estado: 'ocupada', area: 'interior', tiempoOcupada: 15, cliente: 'Pareja joven' },
        { id: 9, numero: 6, capacidad: 4, ubicacion: 'Interior', estado: 'disponible', area: 'interior' },
        { id: 10, numero: 9, capacidad: 6, ubicacion: 'Interior', estado: 'ocupada', area: 'interior', tiempoOcupada: 30, cliente: 'Familia Morales' }
      ],
      pedidosActivos: [
        {
          id: 1,
          mesa: 2,
          hora: '14:30',
          estado: 'En cocina',
          estadoColor: 'warning',
          prioridadColor: 'warning',
          items: [
            { id: 1, cantidad: 2, nombre: 'Asado de Tira' },
            { id: 2, cantidad: 1, nombre: 'Ensalada Mixta' },
            { id: 3, cantidad: 3, nombre: 'Coca Cola' }
          ]
        },
        {
          id: 2,
          mesa: 10,
          hora: '14:15',
          estado: 'Listo',
          estadoColor: 'success',
          prioridadColor: 'success',
          items: [
            { id: 4, cantidad: 1, nombre: 'Picaña' },
            { id: 5, cantidad: 2, nombre: 'Papas Fritas' }
          ]
        },
        {
          id: 3,
          mesa: 5,
          hora: '14:45',
          estado: 'Preparando',
          estadoColor: 'info',
          prioridadColor: 'info',
          items: [
            { id: 6, cantidad: 2, nombre: 'Pollo a la Parrilla' }
          ]
        }
      ]
    }
  },
  computed: {
    mesasTerraza() {
      return this.todasLasMesas.filter(mesa => mesa.area === 'terraza')
        .map(mesa => this.mapearEstadoMesa(mesa))
    },
    mesasInterior() {
      return this.todasLasMesas.filter(mesa => mesa.area === 'interior')
        .map(mesa => this.mapearEstadoMesa(mesa))
    }
  },
  methods: {
    mapearEstadoMesa(mesa) {
      const estadoMapping = {
        'disponible': { color: 'disponible', texto: 'Disponible' },
        'ocupada': { color: 'ocupada', texto: 'Ocupada' },
        'reservada': { color: 'reservada', texto: 'Reservada' },
        'limpieza': { color: 'limpieza', texto: 'Limpieza' }
      }
      
      return {
        ...mesa,
        estadoColor: estadoMapping[mesa.estado].color,
        estadoTexto: estadoMapping[mesa.estado].texto
      }
    },

    seleccionarMesa(mesa) {
      this.mesaSeleccionada = mesa
    },

    ocuparMesa() {
      const nombreCliente = prompt('Nombre del cliente:')
      if (nombreCliente) {
        this.mesaSeleccionada.estado = 'ocupada'
        this.mesaSeleccionada.cliente = nombreCliente
        this.mesaSeleccionada.tiempoOcupada = 0
        
        // Actualizar en el array principal
        const mesaIndex = this.todasLasMesas.findIndex(m => m.id === this.mesaSeleccionada.id)
        if (mesaIndex !== -1) {
          this.todasLasMesas[mesaIndex] = { ...this.mesaSeleccionada }
        }

        // Enviar al backend
        this.$inertia.post('/mesero/ocupar-mesa', {
          mesa_id: this.mesaSeleccionada.id,
          cliente: nombreCliente
        })
      }
    },

    liberarMesa() {
      if (confirm('¿Está seguro de liberar la mesa?')) {
        this.mesaSeleccionada.estado = 'limpieza'
        this.mesaSeleccionada.cliente = null
        this.mesaSeleccionada.tiempoOcupada = null
        
        // Actualizar en el array principal
        const mesaIndex = this.todasLasMesas.findIndex(m => m.id === this.mesaSeleccionada.id)
        if (mesaIndex !== -1) {
          this.todasLasMesas[mesaIndex] = { ...this.mesaSeleccionada }
        }

        // Enviar al backend
        this.$inertia.post('/mesero/liberar-mesa', {
          mesa_id: this.mesaSeleccionada.id
        })
      }
    },

    marcarLimpia() {
      this.mesaSeleccionada.estado = 'disponible'
      
      // Actualizar en el array principal
      const mesaIndex = this.todasLasMesas.findIndex(m => m.id === this.mesaSeleccionada.id)
      if (mesaIndex !== -1) {
        this.todasLasMesas[mesaIndex] = { ...this.mesaSeleccionada }
      }

      // Enviar al backend
      this.$inertia.post('/mesero/marcar-limpia', {
        mesa_id: this.mesaSeleccionada.id
      })
    },

    tomarPedido() {
      this.$inertia.get('/mesero/nuevo-pedido')
    },

    tomarPedidoMesa() {
      this.$inertia.get(`/mesero/nuevo-pedido?mesa=${this.mesaSeleccionada.numero}`)
    },

    verPedidoActual() {
      // Buscar pedido activo para esta mesa
      const pedido = this.pedidosActivos.find(p => p.mesa === this.mesaSeleccionada.numero)
      if (pedido) {
        this.$inertia.get(`/mesero/pedidos/${pedido.id}`)
      }
    },

    verDetallePedido(pedidoId) {
      this.$inertia.get(`/mesero/pedidos/${pedidoId}`)
    },

    marcarListo(pedidoId) {
      this.$inertia.post('/mesero/marcar-listo', { pedido_id: pedidoId })
    }
  }
}
</script>

<style scoped>
.mesero-dashboard {
  padding: 20px;
}

.dashboard-header {
  background: linear-gradient(135deg, #6f42c1 0%, #6610f2 100%);
  color: white;
  padding: 30px;
  border-radius: 10px;
  margin-bottom: 30px;
}

.stat-card {
  transition: transform 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
}

.restaurant-layout {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 10px;
}

.area-section {
  border: 2px dashed #dee2e6;
  padding: 15px;
  border-radius: 8px;
  background: white;
}

.area-title {
  color: #495057;
  font-weight: 600;
  margin-bottom: 15px;
}

.mesas-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
  gap: 15px;
}

.mesa-item {
  width: 80px;
  height: 80px;
  border: 3px solid;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  position: relative;
  font-size: 0.8em;
}

.mesa-disponible {
  border-color: #28a745;
  background-color: rgba(40, 167, 69, 0.1);
  color: #155724;
}

.mesa-ocupada {
  border-color: #ffc107;
  background-color: rgba(255, 193, 7, 0.1);
  color: #856404;
}

.mesa-reservada {
  border-color: #dc3545;
  background-color: rgba(220, 53, 69, 0.1);
  color: #721c24;
}

.mesa-limpieza {
  border-color: #17a2b8;
  background-color: rgba(23, 162, 184, 0.1);
  color: #0c5460;
}

.mesa-item:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.mesa-numero {
  font-weight: bold;
  font-size: 1.1em;
}

.mesa-info {
  text-align: center;
  line-height: 1;
}

.mesa-tiempo {
  background: rgba(0,0,0,0.1);
  border-radius: 10px;
  padding: 1px 4px;
  margin-top: 2px;
}

.mesa-legend {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 0.9em;
}

.pedido-item {
  transition: transform 0.2s ease;
}

.pedido-item:hover {
  transform: translateX(5px);
}

.pedido-actions {
  display: flex;
  justify-content: flex-end;
  gap: 5px;
}

@media (max-width: 768px) {
  .mesas-grid {
    grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
    gap: 10px;
  }
  
  .mesa-item {
    width: 60px;
    height: 60px;
    font-size: 0.7em;
  }
}
</style>