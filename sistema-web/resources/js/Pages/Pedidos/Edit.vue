<template>
  <div class="container py-4">
    <h2>Editar Pedido #{{ pedido.id_pedido }}</h2>
    <form @submit.prevent="submit">
      <div class="mb-3">
        <label class="form-label">Mesa</label>
        <select v-model="form.id_mesa" class="form-select" disabled>
          <option :value="pedido.mesa?.id_mesa">Mesa #{{ pedido.mesa?.numero_mesa }}</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Mesero</label>
        <select v-model="form.id_mesero" class="form-select" required>
          <option value="">Seleccione un mesero</option>
          <option v-for="mesero in meseros" :key="mesero.id_usuario" :value="mesero.id_usuario">
            {{ mesero.nombre }}
          </option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Productos</label>
        <div v-for="(item, idx) in form.items" :key="idx" class="row g-2 mb-2 align-items-end">
          <div class="col-md-5">
            <select v-model="item.id_producto" class="form-select" required>
              <option value="">Producto</option>
              <option v-for="(productos, categoria) in productosPorCategoria" :key="categoria" disabled>--- {{ categoria }} ---</option>
              <option v-for="producto in productosPorCategoria[item.categoria] || []" :key="producto.id_producto" :value="producto.id_producto">
                {{ producto.nombre }}
              </option>
            </select>
          </div>
          <div class="col-md-2">
            <input v-model.number="item.cantidad" type="number" min="1" class="form-control" placeholder="Cantidad" required />
          </div>
          <div class="col-md-2">
            <input v-model.number="item.precio_unitario" type="number" min="0" step="0.01" class="form-control" placeholder="Precio" required />
          </div>
          <div class="col-md-2">
            <input v-model="item.observaciones" type="text" class="form-control" placeholder="Obs." />
          </div>
          <div class="col-md-1">
            <button type="button" class="btn btn-danger btn-sm" @click="removeItem(idx)"><i class="bi bi-x"></i></button>
          </div>
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm" @click="addItem"><i class="bi bi-plus"></i> Agregar producto</button>
      </div>
      <div class="mb-3">
        <label class="form-label">Observaciones</label>
        <textarea v-model="form.observaciones" class="form-control" rows="2"></textarea>
      </div>
      <button type="submit" class="btn btn-success">Actualizar Pedido</button>
      <inertia-link :href="route('pedidos.show', pedido.id_pedido)" class="btn btn-secondary ms-2">Cancelar</inertia-link>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  pedido: Object,
  productos: Object,
  meseros: Array,
  cocineros: Array
});

const productosPorCategoria = props.productos;

const form = useForm({
  id_mesa: props.pedido.mesa?.id_mesa || '',
  id_mesero: props.pedido.mesero?.id_usuario || '',
  observaciones: props.pedido.observaciones || '',
  items: props.pedido.detalles.map(det => ({
    id_producto: det.producto?.id_producto || '',
    cantidad: det.cantidad,
    precio_unitario: det.precio_unitario,
    observaciones: det.observaciones || '',
    categoria: det.producto?.categoria?.nombre || ''
  }))
});

function addItem() {
  form.items.push({ id_producto: '', cantidad: 1, precio_unitario: 0, observaciones: '', categoria: '' });
}
function removeItem(idx) {
  form.items.splice(idx, 1);
}
function submit() {
  form.put(route('pedidos.update', props.pedido.id_pedido));
}
</script>
