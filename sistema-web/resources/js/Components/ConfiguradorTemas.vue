<template>
    <div class="configuracion-wrapper">
        <!-- Botón flotante para abrir configuración -->
        <button 
            @click="mostrarConfig = !mostrarConfig"
            class="btn-config-float"
            :class="{ 'active': mostrarConfig }"
            title="Configuración de Tema"
        >
            <i class="fas fa-cog"></i>
        </button>

        <!-- Panel de configuración -->
        <div 
            v-if="mostrarConfig" 
            class="config-panel"
            @click.stop
        >
            <div class="config-header">
                <h5>
                    <i class="fas fa-palette"></i>
                    Personalizar Experiencia
                </h5>
                <button 
                    @click="mostrarConfig = false"
                    class="btn-cerrar"
                    title="Cerrar"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="config-body">
                <!-- Selector de público objetivo -->
                <div class="config-group">
                    <label class="config-label">
                        <i class="fas fa-users"></i>
                        Público Objetivo
                    </label>
                    <div class="btn-group-vertical w-100">
                        <button 
                            @click="cambiarTema('ninos')"
                            class="btn btn-outline-primary btn-tema"
                            :class="{ 'active': configuracion.tema === 'ninos' }"
                        >
                            <i class="fas fa-child"></i>
                            Niños
                            <small>Colores vibrantes y divertidos</small>
                        </button>
                        <button 
                            @click="cambiarTema('jovenes')"
                            class="btn btn-outline-primary btn-tema"
                            :class="{ 'active': configuracion.tema === 'jovenes' }"
                        >
                            <i class="fas fa-gamepad"></i>
                            Jóvenes
                            <small>Diseño moderno y tecnológico</small>
                        </button>
                        <button 
                            @click="cambiarTema('adultos')"
                            class="btn btn-outline-primary btn-tema"
                            :class="{ 'active': configuracion.tema === 'adultos' }"
                        >
                            <i class="fas fa-briefcase"></i>
                            Adultos
                            <small>Estilo clásico y profesional</small>
                        </button>
                    </div>
                </div>

                <!-- Selector modo día/noche -->
                <div class="config-group">
                    <label class="config-label">
                        <i class="fas fa-moon"></i>
                        Modo de Visualización
                    </label>
                    <div class="modo-toggle">
                        <button 
                            @click="cambiarModo('dia')"
                            class="btn btn-toggle"
                            :class="{ 'active': configuracion.modo === 'dia' }"
                        >
                            <i class="fas fa-sun"></i>
                            Día
                        </button>
                        <button 
                            @click="cambiarModo('noche')"
                            class="btn btn-toggle"
                            :class="{ 'active': configuracion.modo === 'noche' }"
                        >
                            <i class="fas fa-moon"></i>
                            Noche
                        </button>
                        <button 
                            @click="cambiarModo('auto')"
                            class="btn btn-toggle"
                            :class="{ 'active': configuracion.modo === 'auto' }"
                        >
                            <i class="fas fa-magic"></i>
                            Auto
                        </button>
                    </div>
                </div>

                <!-- Configuración de accesibilidad -->
                <div class="config-group">
                    <label class="config-label">
                        <i class="fas fa-universal-access"></i>
                        Accesibilidad
                    </label>
                    
                    <!-- Tamaño de fuente -->
                    <div class="config-item">
                        <label class="config-sublabel">
                            <i class="fas fa-font"></i>
                            Tamaño de Fuente
                        </label>
                        <div class="btn-group w-100">
                            <button 
                                @click="cambiarTamanoLetra('pequeno')"
                                class="btn btn-outline-secondary"
                                :class="{ 'active': configuracion.tamano_letra === 'pequeno' }"
                            >
                                A-
                            </button>
                            <button 
                                @click="cambiarTamanoLetra('normal')"
                                class="btn btn-outline-secondary"
                                :class="{ 'active': configuracion.tamano_letra === 'normal' }"
                            >
                                A
                            </button>
                            <button 
                                @click="cambiarTamanoLetra('grande')"
                                class="btn btn-outline-secondary"
                                :class="{ 'active': configuracion.tamano_letra === 'grande' }"
                            >
                                A+
                            </button>
                        </div>
                    </div>

                    <!-- Alto contraste -->
                    <div class="config-item">
                        <div class="form-check form-switch">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="contrasteAlto"
                                :checked="configuracion.contraste === 'alto'"
                                @change="cambiarContraste"
                            >
                            <label class="form-check-label" for="contrasteAlto">
                                <i class="fas fa-adjust"></i>
                                Alto Contraste
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="config-actions">
                    <button 
                        @click="guardarConfiguracion"
                        class="btn btn-primary"
                        :disabled="guardando"
                    >
                        <i class="fas fa-save" v-if="!guardando"></i>
                        <i class="fas fa-spinner fa-spin" v-else></i>
                        {{ guardando ? 'Guardando...' : 'Guardar Cambios' }}
                    </button>
                    <button 
                        @click="restaurarDefecto"
                        class="btn btn-outline-secondary"
                    >
                        <i class="fas fa-undo"></i>
                        Restaurar
                    </button>
                </div>
            </div>
        </div>

        <!-- Overlay para cerrar al hacer clic fuera -->
        <div 
            v-if="mostrarConfig"
            class="config-overlay"
            @click="mostrarConfig = false"
        ></div>
    </div>
</template>

<script>
import { ref, reactive, onMounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'

export default {
    name: 'ConfiguradorTemas',
    props: {
        configuracionInicial: {
            type: Object,
            default: () => ({
                tema: 'adultos',
                modo: 'dia',
                tamano_letra: 'normal',
                contraste: 'normal'
            })
        }
    },
    setup(props) {
        const mostrarConfig = ref(false)
        const guardando = ref(false)
        
        const configuracion = reactive({
            ...props.configuracionInicial
        })

        // Aplicar tema inicial
        onMounted(() => {
            aplicarTema()
        })

        // Vigilar cambios en la configuración
        watch(configuracion, () => {
            aplicarTema()
        }, { deep: true })

        // Métodos para cambiar configuración
        const cambiarTema = (tema) => {
            configuracion.tema = tema
        }

        const cambiarModo = (modo) => {
            configuracion.modo = modo
        }

        const cambiarTamanoLetra = (tamano) => {
            configuracion.tamano_letra = tamano
        }

        const cambiarContraste = () => {
            configuracion.contraste = configuracion.contraste === 'alto' ? 'normal' : 'alto'
            aplicarTema()
        }

        // Aplicar tema al DOM
        const aplicarTema = () => {
            const { tema, modo, tamano_letra, contraste } = configuracion
            
            // Determinar si es modo nocturno
            let esModoNocturno = modo === 'noche'
            if (modo === 'auto') {
                // Detectar preferencia del sistema
                esModoNocturno = window.matchMedia('(prefers-color-scheme: dark)').matches
            }
            
            // Construir nombre del archivo CSS
            const sufijo = esModoNocturno ? 'noche' : 'dia'
            const archivoTema = `${tema}-${sufijo}.css`
            
            // Remover temas anteriores
            const temasAnteriores = document.querySelectorAll('link[data-tema]')
            temasAnteriores.forEach(link => link.remove())
            
            // Agregar nuevo tema
            const linkTema = document.createElement('link')
            linkTema.rel = 'stylesheet'
            linkTema.href = `/inf513/grupo09sa/churrasquera/sistema-web/public/css/themes/${archivoTema}`
            linkTema.setAttribute('data-tema', tema)
            document.head.appendChild(linkTema)
            
            // Aplicar clases de configuración al body
            const body = document.body
            
            // Limpiar clases anteriores
            body.classList.remove('font-pequeno', 'font-normal', 'font-grande')
            body.classList.remove('contraste-alto')
            
            // Aplicar nuevas clases
            body.classList.add(`font-${tamano_letra}`)
            
            if (contraste === 'alto') {
                body.classList.add('contraste-alto')
            }
            
            // Atributos de datos para CSS
            body.setAttribute('data-tema', tema)
            body.setAttribute('data-modo', esModoNocturno ? 'noche' : 'dia')
        }

        // Guardar configuración en el servidor
        const guardarConfiguracion = async () => {
            guardando.value = true
            
            try {
                await router.post('/configuracion/guardar', configuracion, {
                    preserveState: true,
                    onSuccess: () => {
                        // Mostrar notificación de éxito
                        mostrarNotificacion('Configuración guardada correctamente', 'success')
                        mostrarConfig.value = false
                    },
                    onError: (errors) => {
                        console.error('Error al guardar configuración:', errors)
                        mostrarNotificacion('Error al guardar la configuración', 'error')
                    }
                })
            } catch (error) {
                console.error('Error:', error)
                mostrarNotificacion('Error de conexión', 'error')
            } finally {
                guardando.value = false
            }
        }

        // Restaurar configuración por defecto
        const restaurarDefecto = () => {
            Object.assign(configuracion, {
                tema: 'adultos',
                modo: 'dia',
                tamano_letra: 'normal',
                contraste: 'normal'
            })
        }

        // Función auxiliar para mostrar notificaciones
        const mostrarNotificacion = (mensaje, tipo) => {
            // Aquí puedes implementar tu sistema de notificaciones preferido
            // Por ejemplo, usando toast notifications
            const evento = new CustomEvent('mostrarNotificacion', {
                detail: { mensaje, tipo }
            })
            window.dispatchEvent(evento)
        }

        // Cerrar panel con tecla Escape
        const manejarTecla = (evento) => {
            if (evento.key === 'Escape') {
                mostrarConfig.value = false
            }
        }

        onMounted(() => {
            document.addEventListener('keydown', manejarTecla)
        })

        return {
            mostrarConfig,
            configuracion,
            guardando,
            cambiarTema,
            cambiarModo,
            cambiarTamanoLetra,
            cambiarContraste,
            guardarConfiguracion,
            restaurarDefecto
        }
    }
}
</script>

<style scoped>
.configuracion-wrapper {
    position: relative;
}

.btn-config-float {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(45deg, #3498db, #2980b9);
    color: white;
    border: none;
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
    transition: all 0.3s ease;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.btn-config-float:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
}

.btn-config-float.active {
    transform: rotate(45deg);
    background: linear-gradient(45deg, #e74c3c, #c0392b);
}

.config-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1001;
    animation: fadeIn 0.3s ease;
}

.config-panel {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 400px;
    max-width: 90vw;
    max-height: 80vh;
    background: var(--color-fondo-carta, white);
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    z-index: 1002;
    animation: slideIn 0.3s ease;
    overflow-y: auto;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { 
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.9);
    }
    to { 
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
}

.config-header {
    padding: 1.5rem;
    border-bottom: 2px solid var(--color-borde, #dee2e6);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(45deg, #3498db, #2980b9);
    color: white;
    border-radius: 12px 12px 0 0;
}

.config-header h5 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
}

.btn-cerrar {
    background: none;
    border: none;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    transition: background 0.2s ease;
}

.btn-cerrar:hover {
    background: rgba(255, 255, 255, 0.2);
}

.config-body {
    padding: 1.5rem;
}

.config-group {
    margin-bottom: 2rem;
}

.config-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--color-texto-primario, #2c3e50);
}

.config-sublabel {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    color: var(--color-texto-secundario, #5a6c7d);
}

.btn-tema {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem;
    margin-bottom: 0.5rem;
    text-align: center;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-tema small {
    opacity: 0.8;
    font-size: 0.8rem;
    margin-top: 0.25rem;
}

.btn-tema.active {
    background-color: var(--color-primario, #3498db);
    color: white;
    border-color: var(--color-primario, #3498db);
}

.modo-toggle {
    display: flex;
    border: 1px solid var(--color-borde, #dee2e6);
    border-radius: 8px;
    overflow: hidden;
}

.btn-toggle {
    flex: 1;
    border: none;
    padding: 0.75rem;
    background: var(--color-fondo-carta, white);
    color: var(--color-texto-primario, #2c3e50);
    transition: all 0.3s ease;
}

.btn-toggle:hover {
    background: var(--color-fondo-secundario, #f8f9fa);
}

.btn-toggle.active {
    background: var(--color-primario, #3498db);
    color: white;
}

.config-item {
    margin-bottom: 1rem;
}

.form-check {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: 6px;
    transition: background 0.2s ease;
}

.form-check:hover {
    background: var(--color-fondo-secundario, #f8f9fa);
}

.form-check-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
    font-size: 0.9rem;
    cursor: pointer;
}

.config-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 2rem;
}

.config-actions .btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

/* Responsive */
@media (max-width: 768px) {
    .config-panel {
        width: 95vw;
        max-height: 95vh;
    }
    
    .btn-config-float {
        bottom: 15px;
        right: 15px;
        width: 50px;
        height: 50px;
        font-size: 1rem;
    }
}
</style>