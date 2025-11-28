<template>
  <Head title="Panel Cajero" />
  
  <Layout>
    <div class="cajero-dashboard">
      <!-- Header del Dashboard -->
      <div class="dashboard-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="h2 mb-0">
              <i class="fas fa-cash-register me-2"></i>
              Panel de Cajero
            </h1>
            <p class="text-muted mb-0">Gestión de pagos y facturación</p>
          </div>
          <div class="header-actions">
            <button class="btn btn-success" @click="abrirCaja">
              <i class="fas fa-unlock me-2"></i>
              Abrir Caja
            </button>
          </div>
        </div>
      </div>

      <!-- Estado de Caja -->
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="cash-icon me-3">
                  <i class="fas fa-wallet fa-2x"></i>
                </div>
                <div>
                  <h6 class="card-title mb-1">Efectivo en Caja</h6>
                  <h3 class="mb-0">Bs. {{ cajaEstado.efectivo }}</h3>
                  <small class="opacity-75">Última apertura: {{ cajaEstado.ultimaApertura }}</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="cash-icon me-3">
                  <i class="fas fa-chart-line fa-2x"></i>
                </div>
                <div>
                  <h6 class="card-title mb-1">Ventas del Turno</h6>
                  <h3 class="mb-0">Bs. {{ cajaEstado.ventasTurno }}</h3>
                  <small class="opacity-75">{{ cajaEstado.transacciones }} transacciones</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel de Ventas -->
      <div class="row">
        <!-- Calculadora de Venta -->
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
              <h5 class="card-title mb-0">
                <i class="fas fa-calculator me-2"></i>
                Nueva Venta
              </h5>
            </div>
            <div class="card-body">
              <!-- Búsqueda de Productos -->
              <div class="product-search mb-3">
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="fas fa-search"></i>
                  </span>
                  <input 
                    v-model="busquedaProducto" 
                    @input="buscarProductos"
                    type="text" 
                    class="form-control form-control-lg" 
                    placeholder="Buscar producto por nombre o código..."
                  >
                </div>
                
                <!-- Resultados de búsqueda -->
                <div v-if="productosEncontrados.length > 0" class="search-results mt-2">
                  <div class="list-group">
                    <button 
                      v-for="producto in productosEncontrados" 
                      :key="producto.id"
                      @click="agregarProducto(producto)"
                      class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                    >
                      <div>
                        <strong>{{ producto.nombre }}</strong><br>
                        <small class="text-muted">{{ producto.descripcion }}</small>
                      </div>
                      <div class="text-end">
                        <span class="badge bg-primary">Bs. {{ producto.precio }}</span>
                      </div>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Carrito de Compras -->
              <div class="cart-section">
                <h6 class="mb-3">
                  <i class="fas fa-shopping-cart me-2"></i>
                  Productos Seleccionados
                </h6>
                
                <div v-if="carrito.length === 0" class="text-center py-4 text-muted">
                  <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                  <p>No hay productos en el carrito</p>
                </div>

                <div v-else class="table-responsive">
                  <table class="table table-sm">
                    <thead>
                      <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unit.</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="item in carrito" :key="item.id">
                        <td>
                          <strong>{{ item.nombre }}</strong>
                        </td>
                        <td style="width: 120px;">
                          <div class="input-group input-group-sm">
                            <button class="btn btn-outline-secondary" @click="decrementarCantidad(item.id)">
                              <i class="fas fa-minus"></i>
                            </button>
                            <input 
                              v-model.number="item.cantidad" 
                              @input="actualizarCarrito"
                              type="number" 
                              class="form-control text-center" 
                              min="1"
                            >
                            <button class="btn btn-outline-secondary" @click="incrementarCantidad(item.id)">
                              <i class="fas fa-plus"></i>
                            </button>
                          </div>
                        </td>
                        <td>Bs. {{ item.precio }}</td>
                        <td>
                          <strong>Bs. {{ (item.cantidad * item.precio).toFixed(2) }}</strong>
                        </td>
                        <td>
                          <button class="btn btn-sm btn-outline-danger" @click="eliminarDelCarrito(item.id)">
                            <i class="fas fa-trash"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Panel de Pago -->
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
              <h5 class="card-title mb-0">
                <i class="fas fa-receipt me-2"></i>
                Resumen de Venta
              </h5>
            </div>
            <div class="card-body">
              <!-- Total -->
              <div class="total-section mb-4">
                <div class="d-flex justify-content-between mb-2">
                  <span>Subtotal:</span>
                  <span>Bs. {{ subtotal.toFixed(2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                  <span>Descuento:</span>
                  <span>Bs. {{ descuento.toFixed(2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                  <strong class="h5">Total:</strong>
                  <strong class="h5 text-primary">Bs. {{ total.toFixed(2) }}</strong>
                </div>
              </div>

              <!-- Método de Pago -->
              <div class="payment-method mb-3">
                <h6>Método de Pago</h6>
                <div class="btn-group w-100" role="group">
                  <input type="radio" class="btn-check" name="metodoPago" id="efectivo" value="efectivo" v-model="metodoPago">
                  <label class="btn btn-outline-primary" for="efectivo">
                    <i class="fas fa-money-bill me-1"></i>
                    Efectivo
                  </label>

                  <input type="radio" class="btn-check" name="metodoPago" id="tarjeta" value="tarjeta" v-model="metodoPago">
                  <label class="btn btn-outline-primary" for="tarjeta">
                    <i class="fas fa-credit-card me-1"></i>
                    Tarjeta
                  </label>
                </div>
              </div>

              <!-- Pago en Efectivo -->
              <div v-if="metodoPago === 'efectivo'" class="efectivo-section mb-3">
                <label class="form-label">Monto Recibido</label>
                <div class="input-group">
                  <span class="input-group-text">Bs.</span>
                  <input 
                    v-model.number="montoRecibido" 
                    @input="calcularVuelto"
                    type="number" 
                    class="form-control form-control-lg" 
                    placeholder="0.00"
                    step="0.01"
                  >
                </div>
                <div v-if="vuelto >= 0" class="mt-2 p-2 bg-success text-white rounded">
                  <strong>Vuelto: Bs. {{ vuelto.toFixed(2) }}</strong>
                </div>
                <div v-else-if="montoRecibido > 0" class="mt-2 p-2 bg-warning text-dark rounded">
                  <strong>Falta: Bs. {{ Math.abs(vuelto).toFixed(2) }}</strong>
                </div>
              </div>

              <!-- Botones de Acción -->
              <div class="action-buttons">
                <button 
                  @click="procesarVenta" 
                  :disabled="!puedeVender" 
                  class="btn btn-success btn-lg w-100 mb-2"
                >
                  <i class="fas fa-check me-2"></i>
                  Procesar Venta
                </button>
                
                <button @click="limpiarCarrito" class="btn btn-outline-secondary w-100">
                  <i class="fas fa-trash me-2"></i>
                  Limpiar Carrito
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Ventas Recientes -->
      <div class="row">
        <div class="col-12">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
              <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>
                Ventas Recientes
              </h5>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Hora</th>
                      <th>Total</th>
                      <th>Método</th>
                      <th>Estado</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="venta in ventasRecientes" :key="venta.id">
                      <td>{{ venta.numero }}</td>
                      <td>{{ venta.hora }}</td>
                      <td>Bs. {{ venta.total }}</td>
                      <td>
                        <span :class="`badge bg-${venta.metodoColor}`">
                          {{ venta.metodo }}
                        </span>
                      </td>
                      <td>
                        <span :class="`badge bg-${venta.estadoColor}`">
                          {{ venta.estado }}
                        </span>
                      </td>
                      <td>
                        <button class="btn btn-sm btn-outline-primary me-1" @click="verDetalle(venta.id)">
                          <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" @click="imprimirRecibo(venta.id)">
                          <i class="fas fa-print"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
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
  name: 'CajeroDashboard',
  components: {
    Head,
    Layout
  },
  props: {
    cajaEstado: {
      type: Object,
      default: () => ({
        efectivo: '1,250.50',
        ventasTurno: '2,840.00',
        transacciones: 28,
        ultimaApertura: '08:30'
      })
    },
    productos: {
      type: Array,
      default: () => []
    },
    ventasRecientes: {
      type: Array,
      default: () => [
        { id: 1, numero: 'V001', hora: '14:30', total: '185.50', metodo: 'Efectivo', metodoColor: 'success', estado: 'Completada', estadoColor: 'success' },
        { id: 2, numero: 'V002', hora: '14:25', total: '120.00', metodo: 'Tarjeta', metodoColor: 'primary', estado: 'Completada', estadoColor: 'success' },
        { id: 3, numero: 'V003', hora: '14:20', total: '95.75', metodo: 'Efectivo', metodoColor: 'success', estado: 'Completada', estadoColor: 'success' }
      ]
    }
  },
  data() {
    return {
      busquedaProducto: '',
      productosEncontrados: [],
      carrito: [],
      metodoPago: 'efectivo',
      montoRecibido: 0,
      descuentoPorcentaje: 0
    }
  },
  computed: {
    subtotal() {
      return this.carrito.reduce((sum, item) => sum + (item.cantidad * item.precio), 0)
    },
    descuento() {
      return (this.subtotal * this.descuentoPorcentaje) / 100
    },
    total() {
      return this.subtotal - this.descuento
    },
    vuelto() {
      return this.montoRecibido - this.total
    },
    puedeVender() {
      if (this.carrito.length === 0) return false
      if (this.metodoPago === 'efectivo') {
        return this.montoRecibido >= this.total
      }
      return true
    }
  },
  mounted() {
    this.cargarProductos()
  },
  methods: {
    cargarProductos() {
      // Simular productos disponibles
      this.productosDisponibles = [
        { id: 1, nombre: 'Asado de Tira', descripcion: 'Corte de costillas', precio: 85.00 },
        { id: 2, nombre: 'Picaña', descripcion: 'Corte premium', precio: 95.00 },
        { id: 3, nombre: 'Chorizo', descripcion: 'Chorizo parrillero', precio: 45.00 },
        { id: 4, nombre: 'Coca Cola', descripcion: 'Bebida 500ml', precio: 8.00 },
        { id: 5, nombre: 'Ensalada', descripcion: 'Ensalada mixta', precio: 25.00 }
      ]
    },
    
    buscarProductos() {
      if (this.busquedaProducto.length < 2) {
        this.productosEncontrados = []
        return
      }
      
      this.productosEncontrados = this.productosDisponibles.filter(producto => 
        producto.nombre.toLowerCase().includes(this.busquedaProducto.toLowerCase()) ||
        producto.descripcion.toLowerCase().includes(this.busquedaProducto.toLowerCase())
      ).slice(0, 5)
    },
    
    agregarProducto(producto) {
      const itemExistente = this.carrito.find(item => item.id === producto.id)
      
      if (itemExistente) {
        itemExistente.cantidad += 1
      } else {
        this.carrito.push({
          ...producto,
          cantidad: 1
        })
      }
      
      this.busquedaProducto = ''
      this.productosEncontrados = []
      this.calcularVuelto()
    },
    
    eliminarDelCarrito(productoId) {
      this.carrito = this.carrito.filter(item => item.id !== productoId)
      this.calcularVuelto()
    },
    
    incrementarCantidad(productoId) {
      const item = this.carrito.find(item => item.id === productoId)
      if (item) {
        item.cantidad += 1
        this.calcularVuelto()
      }
    },
    
    decrementarCantidad(productoId) {
      const item = this.carrito.find(item => item.id === productoId)
      if (item && item.cantidad > 1) {
        item.cantidad -= 1
        this.calcularVuelto()
      }
    },
    
    actualizarCarrito() {
      this.calcularVuelto()
    },
    
    calcularVuelto() {
      // El computed se encarga del cálculo
    },
    
    limpiarCarrito() {
      this.carrito = []
      this.montoRecibido = 0
      this.busquedaProducto = ''
      this.productosEncontrados = []
    },
    
    procesarVenta() {
      if (!this.puedeVender) return
      
      const ventaData = {
        items: this.carrito,
        total: this.total,
        metodo_pago: this.metodoPago,
        monto_recibido: this.metodoPago === 'efectivo' ? this.montoRecibido : this.total,
        vuelto: this.metodoPago === 'efectivo' ? this.vuelto : 0
      }
      
      this.$inertia.post('/cajero/procesar-venta', ventaData, {
        onSuccess: () => {
          this.limpiarCarrito()
          // Mostrar notificación de éxito
        }
      })
    },
    
    abrirCaja() {
      this.$inertia.post('/cajero/abrir-caja')
    },
    
    verDetalle(ventaId) {
      this.$inertia.get(`/cajero/ventas/${ventaId}`)
    },
    
    imprimirRecibo(ventaId) {
      window.open(`/cajero/ventas/${ventaId}/imprimir`, '_blank')
    }
  }
}
</script>

<style scoped>
.cajero-dashboard {
  padding: 20px;
}

.dashboard-header {
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
  color: white;
  padding: 30px;
  border-radius: 10px;
  margin-bottom: 30px;
}

.cash-icon {
  opacity: 0.9;
}

.search-results {
  max-height: 300px;
  overflow-y: auto;
}

.cart-section {
  min-height: 300px;
}

.total-section {
  font-size: 1.1em;
}

.payment-method .btn {
  font-size: 0.9em;
}

.efectivo-section {
  background: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
}

.action-buttons button {
  font-weight: 600;
}

.table td {
  vertical-align: middle;
}
</style>