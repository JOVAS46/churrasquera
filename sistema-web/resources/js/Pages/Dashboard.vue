<template>
    <AppLayout>
        <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30 text-center">
            <h2 class="page-title">Panel de Control</h2>
        </div>
        <!-- ========== title-wrapper end ========== -->
        
        <div class="container-fluid">
            <div class="row">
                <!-- Welcome Section -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="mb-4">¡Bienvenido al Sistema de Gestión!</h3>
                            <p class="text-muted">
                                Gestiona tu restaurante de manera eficiente con nuestro sistema integral.
                            </p>
                            <div v-if="$page.props.auth?.user" class="mt-4">
                                <p class="text-muted">
                                    Usuario: <strong>{{ $page.props.auth.user.nombre }} {{ $page.props.auth.user.apellido }}</strong>
                                </p>
                                <p class="text-muted" v-if="$page.props.auth.user.rol">
                                    Rol: <strong>{{ $page.props.auth.user.rol.nombre }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row">
                <div class="col-xl-4 col-lg-6 col-sm-6">
                    <div class="card stat-widget bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-1">
                                    <h5 class="text-white">Insumos</h5>
                                    <h2 class="text-white mb-0">{{ stats.insumos || 0 }}</h2>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-package"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-6 col-sm-6">
                    <div class="card stat-widget bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-1">
                                    <h5 class="text-white">Categorías</h5>
                                    <h2 class="text-white mb-0">{{ stats.categorias || 0 }}</h2>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-tag"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-6 col-sm-6">
                    <div class="card stat-widget bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-1">
                                    <h5 class="text-white">Mesas</h5>
                                    <h2 class="text-white mb-0">{{ stats.mesas || 0 }}</h2>
                                </div>
                                <div class="stat-icon">
                                    <i class="lni lni-restaurant"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Acciones Rápidas</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <Link href="/categorias" 
                                          class="quick-action-card d-flex flex-column align-items-center text-center p-4">
                                        <div class="action-icon mb-3">
                                            <i class="lni lni-tag"></i>
                                        </div>
                                        <h6 class="mb-0">Categorías</h6>
                                        <small class="text-muted">Gestionar categorías</small>
                                    </Link>
                                </div>
                                
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <Link href="/insumos" 
                                          class="quick-action-card d-flex flex-column align-items-center text-center p-4">
                                        <div class="action-icon mb-3">
                                            <i class="lni lni-package"></i>
                                        </div>
                                        <h6 class="mb-0">Insumos</h6>
                                        <small class="text-muted">Gestionar inventario</small>
                                    </Link>
                                </div>
                                
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <Link href="/recetas" 
                                          class="quick-action-card d-flex flex-column align-items-center text-center p-4">
                                        <div class="action-icon mb-3">
                                            <i class="lni lni-restaurant"></i>
                                        </div>
                                        <h6 class="mb-0">Recetas</h6>
                                        <small class="text-muted">Gestionar recetas</small>
                                    </Link>
                                </div>
                                
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <Link href="/ventas" 
                                          class="quick-action-card d-flex flex-column align-items-center text-center p-4">
                                        <div class="action-icon mb-3">
                                            <i class="lni lni-cart"></i>
                                        </div>
                                        <h6 class="mb-0">Ventas</h6>
                                        <small class="text-muted">Registrar ventas</small>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({})
    }
});
</script>

<style scoped>
.page-title {
    color: #4a1c1c;
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 30px;
}

.pt-30 {
    padding-top: 30px;
}

.stat-widget {
    border: none;
    border-radius: 10px;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}

.stat-widget.bg-primary {
    background: linear-gradient(135deg, #4a1c1c 0%, #6d2b2b 100%) !important;
}

.stat-widget.bg-success {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
}

.stat-widget.bg-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%) !important;
}

.stat-widget .card-body {
    padding: 25px;
    position: relative;
}

.stat-widget h2 {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 10px 0;
}

.stat-widget h5 {
    font-size: 1rem;
    font-weight: 500;
    margin-bottom: 5px;
}

.stat-icon {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 3rem;
    opacity: 0.3;
}

.card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    border: none;
}

.card-header {
    background: white !important;
    border-bottom: 1px solid #e5e7eb;
    padding: 20px;
    font-weight: 600;
    color: #4a1c1c;
    border-radius: 10px 10px 0 0 !important;
}

.card-body {
    padding: 20px;
}

.quick-action-card {
    background: #f8f9fa;
    border-radius: 10px;
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
    border: 1px solid #e9ecef;
    height: 100%;
    min-height: 140px;
}

.quick-action-card:hover {
    background: #4a1c1c;
    color: white !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(74, 28, 28, 0.15);
    text-decoration: none;
}

.action-icon {
    width: 60px;
    height: 60px;
    background: rgba(74, 28, 28, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.action-icon i {
    font-size: 24px;
    color: #4a1c1c;
    transition: all 0.3s ease;
}

.quick-action-card:hover .action-icon {
    background: rgba(255, 255, 255, 0.2);
}

.quick-action-card:hover .action-icon i {
    color: white;
}

.quick-action-card h6 {
    font-weight: 600;
    margin-bottom: 5px;
    transition: all 0.3s ease;
}

.quick-action-card small {
    font-size: 12px;
    opacity: 0.8;
}

/* Bootstrap grid classes */
.container-fluid {
    width: 100%;
    padding: 0;
}

.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -15px;
}

.col-12 {
    width: 100%;
    padding: 0 15px;
}

.col-xl-4 {
    width: 33.333333%;
    padding: 0 15px;
}

.col-lg-6 {
    width: 50%;
    padding: 0 15px;
}

.col-sm-6 {
    width: 50%;
    padding: 0 15px;
}

.col-md-3 {
    width: 25%;
    padding: 0 15px;
}

@media (max-width: 768px) {
    .col-sm-6 {
        width: 100%;
        margin-bottom: 15px;
    }
    
    .col-md-3 {
        width: 50%;
        margin-bottom: 15px;
    }
}

@media (max-width: 576px) {
    .col-md-3 {
        width: 100%;
    }
}

/* Utility classes */
.text-center {
    text-align: center !important;
}

.text-white {
    color: white !important;
}

.text-muted {
    color: #6c757d !important;
}

.mb-0 {
    margin-bottom: 0 !important;
}

.mb-3 {
    margin-bottom: 1rem !important;
}

.mb-4 {
    margin-bottom: 1.5rem !important;
}

.p-4 {
    padding: 1.5rem !important;
}

.flex-1 {
    flex: 1 1 0% !important;
}

.d-flex {
    display: flex !important;
}

.flex-column {
    flex-direction: column !important;
}

.align-items-center {
    align-items: center !important;
}

.justify-content-center {
    justify-content: center !important;
}
</style>
