<template>
    <div class="app-layout">
        <!-- ======== sidebar-nav start =========== -->
        <aside class="sidebar-nav-wrapper" :class="{ 'collapsed': !sidebarOpen }">
            <div class="navbar-logo">
                <Link href="/home" class="logo-text">
                    <span class="logo-icon">🔥</span>
                    <span class="brand-text">CHURRASQUERIA ROBERTO</span>
                </Link>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <template v-for="menu in $page.props.menus" :key="menu.id_menu">
                        <!-- Menú con submenús -->
                        <li v-if="menu.hijos && menu.hijos.length > 0" 
                            class="nav-item nav-item-has-children"
                            :class="{ 'active': isMenuActive(menu) }">
                            <a href="#" 
                               @click.prevent="toggleSubmenu(menu.id_menu)"
                               :aria-expanded="activeSubmenu === menu.id_menu">
                                <span class="icon">
                                    <i :class="menu.icono || 'lni lni-circle'"></i>
                                </span>
                                <span class="text">{{ menu.nombre }}</span>
                            </a>
                            <ul class="dropdown-nav" :class="{ 'show': activeSubmenu === menu.id_menu }">
                                <li v-for="submenu in menu.hijos" :key="submenu.id_menu">
                                    <Link :href="submenu.url || '#'" 
                                          :class="{ 'active': isUrlActive(submenu.url) }">
                                        <i :class="submenu.icono || 'lni lni-circle'" class="me-2"></i>
                                        {{ submenu.nombre }}
                                    </Link>
                                </li>
                            </ul>
                        </li>
                        
                        <!-- Menú simple sin submenús -->
                        <li v-else 
                            class="nav-item" 
                            :class="{ 'active': isUrlActive(menu.url) }">
                            <Link :href="menu.url || '#'">
                                <span class="icon">
                                    <i :class="menu.icono || 'lni lni-circle'"></i>
                                </span>
                                <span class="text">{{ menu.nombre }}</span>
                            </Link>
                        </li>
                    </template>
                </ul>
            </nav>
        </aside>
        <div class="overlay" @click="toggleSidebar" v-show="sidebarOpen && isMobile"></div>
        <!-- ======== sidebar-nav end =========== -->

        <!-- ======== main-wrapper start =========== -->
        <main class="main-wrapper" :class="{ 'sidebar-collapsed': !sidebarOpen }">
            <!-- ========== header start ========== -->
            <header class="header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-5 col-md-5 col-6">
                            <div class="header-left d-flex align-items-center">
                                <div class="menu-toggle-btn mr-20">
                                    <button @click="toggleSidebar" class="main-btn primary-btn btn-hover">
                                        <i class="lni lni-chevron-left me-2"></i> Menú
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-7 col-md-7 col-6">
                            <div class="header-right d-flex align-items-center justify-content-end">
                                <!-- Búsqueda Global -->
                                <div class="busqueda-wrapper me-3">
                                    <BusquedaGlobal :busquedas-recientes="$page.props.busquedas_recientes || []" />
                                </div>
                                
                                <!-- profile start -->
                                <div class="profile-box ml-15">
                                    <button class="dropdown-toggle bg-transparent border-0" 
                                            type="button" 
                                            @click="profileDropdownOpen = !profileDropdownOpen">
                                        <div class="profile-info">
                                            <div class="info">
                                                <h6 v-if="$page.props.auth?.user">
                                                    {{ $page.props.auth.user.nombre }} {{ $page.props.auth.user.apellido }}
                                                </h6>
                                            </div>
                                        </div>
                                        <i class="lni lni-chevron-down"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" 
                                        :class="{ 'show': profileDropdownOpen }"
                                        @click.stop>
                                        <li>
                                            <Link href="/profile" @click="profileDropdownOpen = false">
                                                <i class="lni lni-user"></i>
                                                Mi Perfil
                                            </Link>
                                        </li>
                                        <li>
                                            <Link href="/logout" method="post" @click="profileDropdownOpen = false">
                                                <i class="lni lni-exit"></i>
                                                Cerrar Sesión
                                            </Link>
                                        </li>
                                    </ul>
                                </div>
                                <!-- profile end -->
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- ========== header end ========== -->

            <!-- ========== section start ========== -->
            <section class="section">
                <div class="container-fluid">
                    <!-- Flash Messages -->
                    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible mb-4">
                        {{ $page.props.flash.success }}
                    </div>
                    <div v-if="$page.props.flash?.error" class="alert alert-danger alert-dismissible mb-4">
                        {{ $page.props.flash.error }}
                    </div>
                    <div v-if="$page.props.flash?.warning" class="alert alert-warning alert-dismissible mb-4">
                        {{ $page.props.flash.warning }}
                    </div>
                    <div v-if="$page.props.flash?.info" class="alert alert-info alert-dismissible mb-4">
                        {{ $page.props.flash.info }}
                    </div>
                    
                    <slot />
                </div>
            </section>
            <!-- ========== section end ========== -->

            <!-- ========== footer start =========== -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12 text-center mb-2">
                            <div class="copyright">
                                <p class="text-sm mb-0">
                                    Sistema de Gestión - CHURRASQUERIA ROBERTO © {{ new Date().getFullYear() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- ========== footer end =========== -->

            <!-- Contador de Visitas -->
            <ContadorVisitas />
            <!-- Contador de Visitas End -->
        </main>
        <!-- ======== main-wrapper end =========== -->
        
        <!-- ======== configurador de temas =========== -->
        <ConfiguradorTemas :configuracion-inicial="$page.props.configuracion || {}" />
        <!-- ======== configurador de temas end =========== -->
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ConfiguradorTemas from '@/Components/ConfiguradorTemas.vue';
import BusquedaGlobal from '@/Components/BusquedaGlobal.vue';
import ContadorVisitas from '@/Components/ContadorVisitas.vue';

const page = usePage();
const sidebarOpen = ref(true);
const profileDropdownOpen = ref(false);
const activeSubmenu = ref(null);
const isMobile = ref(false);

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const toggleSubmenu = (submenu) => {
    if (activeSubmenu.value === submenu) {
        activeSubmenu.value = null;
    } else {
        activeSubmenu.value = submenu;
    }
};

const isUrlActive = (url) => {
    if (!url) return false;
    const currentPath = window.location.pathname;
    if (url === '/home' && currentPath === '/home') return true;
    if (url !== '/home' && currentPath.startsWith(url)) return true;
    return false;
};

const isMenuActive = (menu) => {
    if (!menu.hijos || menu.hijos.length === 0) {
        return isUrlActive(menu.url);
    }
    return menu.hijos.some(submenu => isUrlActive(submenu.url));
};

const checkScreenSize = () => {
    isMobile.value = window.innerWidth <= 768;
    if (isMobile.value) {
        sidebarOpen.value = false;
    }
};

const handleClickOutside = (event) => {
    if (!event.target.closest('.profile-box')) {
        profileDropdownOpen.value = false;
    }
};

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
    document.addEventListener('click', handleClickOutside);
    
    // Auto-expandir menú activo basado en la URL actual
    const currentUrl = window.location.pathname;
    const menus = page.props.menus;
    if (menus) {
        menus.forEach(menu => {
            if (menu.hijos && menu.hijos.length > 0) {
                const hasActiveChild = menu.hijos.some(submenu => isUrlActive(submenu.url));
                if (hasActiveChild) {
                    activeSubmenu.value = menu.id_menu;
                }
            }
        });
    }
});

onUnmounted(() => {
    window.removeEventListener('resize', checkScreenSize);
    document.removeEventListener('click', handleClickOutside);
});
</script>

<style>
/* Incluir LineIcons y FontAwesome desde CDN */
@import url('https://cdn.lineicons.com/4.0/lineicons.css');
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

/* Estilos del Layout - Copiados de la versión anterior */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background-color: #f8f9fa;
}

.app-layout {
    display: flex;
    min-height: 100vh;
}

/* Estilos del Sidebar */
.sidebar-nav-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    width: 280px;
    height: 100vh;
    background-color: #4a1c1c !important;
    color: white;
    transition: all 0.3s ease;
    z-index: 1000;
    overflow-y: auto;
}

.sidebar-nav-wrapper.collapsed {
    transform: translateX(-280px);
}

.navbar-logo {
    padding: 20px;
    text-align: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.navbar-logo .logo-text {
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    padding: 10px;
    transition: all 0.3s ease;
}

.navbar-logo .logo-icon {
    font-size: 24px;
    margin-right: 10px;
}

.navbar-logo .brand-text {
    color: #ff6633;
    font-size: 24px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-family: 'Arial', sans-serif;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

.navbar-logo .logo-text:hover .brand-text {
    color: #ff8855;
    transform: scale(1.05);
}

.navbar-logo .logo-text:hover .logo-icon {
    transform: rotate(15deg);
}

.sidebar-nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-nav ul li a {
    color: rgba(255, 255, 255, 0.8) !important;
    padding: 12px 20px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    text-decoration: none;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    cursor: pointer;
}

.sidebar-nav ul li a:hover,
.sidebar-nav ul li.active a {
    background: rgba(255, 255, 255, 0.1);
    color: #ff6633 !important;
    border-radius: 0;
}

.sidebar-nav ul li a .icon {
    width: 20px;
    margin-right: 10px;
    display: flex;
    align-items: center;
}

.sidebar-nav ul li a .icon i {
    color: rgba(255, 255, 255, 0.8);
    transition: all 0.3s ease;
    font-size: 16px;
}

.sidebar-nav ul li a:hover .icon i,
.sidebar-nav ul li.active a .icon i {
    color: #ff6633;
}

.sidebar-nav ul li a .text {
    font-weight: 500;
    font-size: 14px;
}

/* Dropdown navigation */
.dropdown-nav {
    background: rgba(0, 0, 0, 0.2);
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.dropdown-nav.show {
    max-height: 500px;
}

.dropdown-nav li a {
    padding-left: 50px;
    font-size: 13px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

/* Nav item with children */
.nav-item-has-children > a::after {
    content: '⌄';
    margin-left: auto;
    transition: transform 0.3s ease;
    font-size: 12px;
}

.nav-item-has-children > a[aria-expanded="true"]::after {
    transform: rotate(180deg);
}

/* Main wrapper */
.main-wrapper {
    margin-left: 280px;
    width: calc(100% - 280px);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.main-wrapper.sidebar-collapsed {
    margin-left: 0;
    width: 100%;
}

/* Header */
.header {
    background: #4a1c1c !important;
    padding: 15px 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 999;
}

.header .container-fluid {
    padding: 0 30px;
    max-width: none;
}

.header .row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 0;
}

.header .col-lg-5, .header .col-md-5, .header .col-6 {
    padding: 0;
}

.header .col-lg-7, .header .col-md-7 {
    padding: 0;
}

.header-left {
    display: flex;
    align-items: center;
}

.menu-toggle-btn {
    margin-right: 20px;
}

.main-btn {
    background: rgba(255, 255, 255, 0.1) !important;
    border: none !important;
    color: white !important;
    padding: 8px 16px !important;
    border-radius: 6px !important;
    font-weight: 500 !important;
    transition: all 0.3s ease !important;
    font-size: 14px;
    cursor: pointer;
}

.main-btn:hover {
    background: rgba(255, 255, 255, 0.2) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
}

/* Profile dropdown */
.header-right {
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

.profile-box {
    position: relative;
    margin-left: 15px;
}

.profile-box button {
    color: white !important;
    background: transparent !important;
    border: none !important;
    display: flex;
    align-items: center;
    padding: 5px 10px;
    border-radius: 6px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.profile-box button:hover {
    background: rgba(255, 255, 255, 0.1) !important;
}

.profile-box .info h6 {
    color: white !important;
    margin: 0;
    font-size: 14px;
    font-weight: 500;
}

.profile-box .profile-info {
    margin-right: 8px;
}

.dropdown-menu {
    background: white;
    border: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border-radius: 8px;
    padding: 8px 0;
    position: absolute;
    top: 100%;
    right: 0;
    min-width: 180px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    list-style: none;
    margin: 0;
}

.dropdown-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(5px);
}

.dropdown-menu li {
    margin: 0;
}

.dropdown-menu li a {
    color: #4a1c1c !important;
    transition: all 0.3s ease;
    padding: 10px 20px;
    display: flex;
    align-items: center;
    text-decoration: none;
    font-size: 14px;
    width: 100%;
}

.dropdown-menu li a:hover {
    background: #ff6633;
    color: white !important;
}

.dropdown-menu li a i {
    color: #4a1c1c;
    margin-right: 8px;
    font-size: 16px;
    width: 20px;
}

.dropdown-menu li a:hover i {
    color: white;
}

/* Section */
.section {
    background-color: #f8f9fa;
    flex: 1;
    padding: 30px 0;
}

.section .container-fluid {
    padding: 0 30px;
    max-width: none;
}

/* Footer */
.footer {
    background: white;
    border-top: 1px solid #e5e7eb;
    padding: 20px 0;
    margin-top: auto;
}

.footer .container-fluid {
    padding: 0 30px;
    max-width: none;
}

.footer .row {
    display: flex;
    align-items: center;
    margin: 0;
}

.footer .copyright {
    margin: 0;
}

.footer .text-sm {
    color: #6b7280;
    margin: 0;
    font-size: 13px;
}

/* Overlay for mobile */
.overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
}

/* Alert styles */
.alert {
    padding: 12px 16px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 6px;
    font-size: 14px;
}

.alert-success {
    background-color: #d1fae5;
    border-color: #10b981;
    color: #065f46;
}

.alert-danger {
    background-color: #fee2e2;
    border-color: #ef4444;
    color: #991b1b;
}

.alert-warning {
    background-color: #fef3c7;
    border-color: #f59e0b;
    color: #92400e;
}

.alert-info {
    background-color: #dbeafe;
    border-color: #3b82f6;
    color: #1e40af;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar-nav-wrapper {
        transform: translateX(-280px);
    }
    
    .sidebar-nav-wrapper:not(.collapsed) {
        transform: translateX(0);
    }
    
    .main-wrapper {
        margin-left: 0;
        width: 100%;
    }
    
    .header .container-fluid {
        padding: 0 15px;
    }
    
    .section .container-fluid {
        padding: 0 15px;
    }
    
    .footer .container-fluid {
        padding: 0 15px;
    }
    
    .section {
        padding: 20px 0;
    }
}

/* Utility classes */
.d-flex {
    display: flex !important;
}

.align-items-center {
    align-items: center !important;
}

.justify-content-between {
    justify-content: space-between !important;
}

.justify-content-end {
    justify-content: flex-end !important;
}

.mr-20 {
    margin-right: 20px !important;
}

.ml-15 {
    margin-left: 15px !important;
}

.me-2 {
    margin-right: 8px !important;
}

.mb-4 {
    margin-bottom: 1.5rem !important;
}

.text-md-start {
    text-align: start !important;
}

.order-last {
    order: 9999 !important;
}

.order-md-first {
    order: -1 !important;
}

/* Bootstrap grid simulation */
.col-md-6 {
    width: 50%;
}

@media (max-width: 768px) {
    .col-md-6 {
        width: 100%;
    }
    
    .order-last {
        order: 9999 !important;
    }
}
</style>
