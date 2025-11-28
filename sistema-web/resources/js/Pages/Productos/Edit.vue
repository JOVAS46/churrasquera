<template>
    <Create :producto="producto" :categorias="categorias" :insumos="insumos" :errors="errors" />
</template>

<script setup>
import Create from './Create.vue'

defineProps({
    producto: Object,
    categorias: Array,
    insumos: Array,
    errors: Object
})
</script>
