<template>
  <Head title="Panel Administrador" />
  
  <Layout>
    <div class="admin-dashboard">
      <!-- Header del Dashboard -->
      <div class="dashboard-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="h2 mb-0">
              <i class="fas fa-user-shield me-2"></i>
              Panel de Administrador
            </h1>
            <p class="text-muted mb-0">Gestión integral del restaurante</p>
          </div>
          <div class="header-actions">
            <button class="btn btn-primary" @click="generarReporte">
              <i class="fas fa-chart-line me-2"></i>
              Generar Reporte
            </button>
          </div>
        </div>
      </div>

      <!-- Métricas Principales -->
      <div class="metrics-grid row mb-4">
        <div class="col-md-3">
          <div class="metric-card card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="metric-icon bg-success text-white rounded-circle me-3">
                  <i class="fas fa-dollar-sign"></i>
                </div>
                <div>
                  <h6 class="card-title mb-1">Ventas Hoy</h6>
                  <h4 class="mb-0">Bs. {{ metricas.ventasHoy }}</h4>
                  <small class="text-success">+15% vs ayer</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="metric-card card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="metric-icon bg-info text-white rounded-circle me-3">
                  <i class="fas fa-users"></i>
                </div>
                <div>
                  <h6 class="card-title mb-1">Clientes Atendidos</h6>
                  <h4 class="mb-0">{{ metricas.clientesAtendidos }}</h4>
                  <small class="text-info">+8% vs ayer</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="metric-card card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="metric-icon bg-warning text-white rounded-circle me-3">
                  <i class="fas fa-table"></i>
                </div>
                <div>
                  <h6 class="card-title mb-1">Mesas Ocupadas</h6>
                  <h4 class="mb-0">{{ metricas.mesasOcupadas }}/{{ metricas.totalMesas }}</h4>
                  <small class="text-warning">{{ porcentajeOcupacion }}% ocupación</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="metric-card card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="metric-icon bg-danger text-white rounded-circle me-3">
                  <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                  <h6 class="card-title mb-1">Alertas</h6>
                  <h4 class="mb-0">{{ metricas.alertas }}</h4>
                  <small class="text-danger">Stock bajo</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Gráficos y Tablas -->
      <div class="row">
        <!-- Gráfico de Ventas -->
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
              <h5 class="card-title mb-0">
                <i class="fas fa-chart-line me-2"></i>
                Ventas de la Semana
              </h5>
            </div>
            <div class="card-body">
              <canvas ref="ventasChart" height="100"></canvas>
            </div>
          </div>
        </div>

        <!-- Resumen de Personal -->
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
              <h5 class="card-title mb-0">
                <i class="fas fa-users-cog me-2"></i>
                Personal Activo
              </h5>
            </div>
            <div class="card-body">
              <div class="personal-list">
                <div class="personal-item d-flex justify-content-between align-items-center mb-3" 
                     v-for="empleado in personalActivo" :key="empleado.id">
                  <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                      <i :class="empleado.icono" class="text-white"></i>
                    </div>
                    <div>
                      <h6 class="mb-0">{{ empleado.nombre }}</h6>
                      <small class="text-muted">{{ empleado.rol }}</small>
                    </div>
                  </div>
                  <span :class="`badge ${empleado.estado === 'activo' ? 'bg-success' : 'bg-secondary'}`">
                    {{ empleado.estado }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pedidos Recientes y Stock Crítico -->
      <div class="row">
        <!-- Pedidos Recientes -->
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
              <h5 class="card-title mb-0">
                <i class="fas fa-receipt me-2"></i>
                Pedidos Recientes
              </h5>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Mesa</th>
                      <th>Hora</th>
                      <th>Total</th>
                      <th>Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="pedido in pedidosRecientes" :key="pedido.id">
                      <td>Mesa {{ pedido.mesa }}</td>
                      <td>{{ pedido.hora }}</td>
                      <td>Bs. {{ pedido.total }}</td>
                      <td>
                        <span :class="`badge bg-${pedido.estadoColor}`">
                          {{ pedido.estado }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Stock Crítico -->
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
              <h5 class="card-title mb-0">
                <i class="fas fa-boxes me-2"></i>
                Stock Crítico
              </h5>
            </div>
            <div class="card-body">
              <div class="stock-alerts">
                <div class="alert alert-warning d-flex align-items-center mb-3" 
                     v-for="item in stockCritico" :key="item.id">
                  <i class="fas fa-exclamation-triangle me-3"></i>
                  <div class="flex-grow-1">
                    <strong>{{ item.nombre }}</strong><br>
                    <small>Stock actual: {{ item.stock }} {{ item.unidad }}</small>
                  </div>
                  <button class="btn btn-sm btn-outline-primary" @click="reabastecer(item.id)">
                    Reabastecer
                  </button>
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
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

export default {
  name: 'AdminDashboard',
  components: {
    Head,
    Layout
  },
  props: {
    metricas: {
      type: Object,
      default: () => ({
        ventasHoy: '2,450',
        clientesAtendidos: 45,
        mesasOcupadas: 8,
        totalMesas: 12,
        alertas: 3
      })
    },
    personalActivo: {
      type: Array,
      default: () => [
        { id: 1, nombre: 'Juan Mesero', rol: 'Mesero', estado: 'activo', icono: 'fas fa-user' },
        { id: 2, nombre: 'María Cajera', rol: 'Cajera', estado: 'activo', icono: 'fas fa-cash-register' },
        { id: 3, nombre: 'Pedro Cocinero', rol: 'Cocinero', estado: 'activo', icono: 'fas fa-utensils' }
      ]
    },
    pedidosRecientes: {
      type: Array,
      default: () => [
        { id: 1, mesa: 5, hora: '14:30', total: '185.50', estado: 'En preparación', estadoColor: 'warning' },
        { id: 2, mesa: 3, hora: '14:25', total: '120.00', estado: 'Listo', estadoColor: 'success' },
        { id: 3, mesa: 7, hora: '14:20', total: '95.75', estado: 'Entregado', estadoColor: 'info' }
      ]
    },
    stockCritico: {
      type: Array,
      default: () => [
        { id: 1, nombre: 'Carne de Res', stock: 5, unidad: 'kg' },
        { id: 2, nombre: 'Papas', stock: 8, unidad: 'kg' },
        { id: 3, nombre: 'Cerveza', stock: 12, unidad: 'botellas' }
      ]
    }
  },
  computed: {
    porcentajeOcupacion() {
      return Math.round((this.metricas.mesasOcupadas / this.metricas.totalMesas) * 100)
    }
  },
  mounted() {
    this.inicializarGrafico()
  },
  methods: {
    inicializarGrafico() {
      const ctx = this.$refs.ventasChart.getContext('2d')
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
          datasets: [{
            label: 'Ventas (Bs.)',
            data: [1200, 1900, 3000, 2500, 2200, 3500, 2800],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      })
    },
    generarReporte() {
      this.$inertia.get('/admin/reportes')
    },
    reabastecer(itemId) {
      this.$inertia.post('/admin/reabastecer', { item_id: itemId })
    }
  }
}
</script>

<style scoped>
.admin-dashboard {
  padding: 20px;
}

.metric-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.metric-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.metric-icon {
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.personal-item {
  padding: 12px;
  border-radius: 8px;
  background-color: #f8f9fa;
  margin-bottom: 8px;
}

.avatar-sm {
  width: 35px;
  height: 35px;
}

.stock-alerts .alert {
  border-left: 4px solid #ffc107;
}

.dashboard-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 30px;
  border-radius: 10px;
  margin-bottom: 30px;
}

.dashboard-header h1 {
  color: white;
}

.dashboard-header .text-muted {
  color: rgba(255,255,255,0.8) !important;
}
</style>