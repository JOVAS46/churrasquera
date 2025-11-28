@extends('layouts.app')

@section('styles')
<style>
    .periodo-btn {
        background-color: #5A2828;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 5px;
        margin-right: 8px;
        cursor: pointer;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 14px;
        transition: background-color 0.3s ease;
    }
    .periodo-btn:hover {
        background-color: #8B4513;
        color: white;
    }
    .periodo-btn.active {
        background-color: #8B4513;
        color: white;
    }
    .card-reporte {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .btn-group {
        display: flex;
        gap: 8px;
    }
    .stats-card {
        background-color: #5A2828;
        color: white !important;
    }
    .stats-card .card-title,
    .stats-card h3,
    .stats-card p,
    .stats-card span {
        color: white !important;
    }
    .variation-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.875rem;
    }
    .variation-positive {
        background-color: rgba(76, 175, 80, 0.2);
        color: #4CAF50 !important;
    }
    .variation-negative {
        background-color: rgba(244, 67, 54, 0.2);
        color: #F44336 !important;
    }

    @media print {
    .no-print {
        display: none !important;
    }
}
</style>
@endsection

@section('content')
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title mb-30">
                    <h2>{{ __('Reportes de Ventas') }}</h2>
                </div>
            </div>
            <div class="col-md-6 text-end no-print">
                <button type="button" class="btn btn-primary" onclick="window.print()">Imprimir</button>
            </div>

        </div>
    </div>
    <!-- ========== title-wrapper end ========== -->

    <div class="container-fluid">
        <div class="row">
            <!-- Selector de Fechas -->
            <div class="col-md-12 mb-3">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div>
                        <label for="fechaInicio" class="form-label mb-0 me-2">Fecha inicio:</label>
                        <input type="date" id="fechaInicio" class="form-control" style="display:inline-block; width:auto;" placeholder="yyyy-mm-dd">
                    </div>
                    <div>
                        <label for="fechaFin" class="form-label mb-0 me-2">Fecha fin:</label>
                        <input type="date" id="fechaFin" class="form-control" style="display:inline-block; width:auto;" placeholder="yyyy-mm-dd">
                    </div>
                </div>
            </div>
            <!-- Tarjetas de estadísticas -->
            <div class="col-md-12 mb-4">
                <div class="row">
                    <!-- Total de Ventas -->
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title" style="color: #5A2828;">Total de Ventas</h5>
                                <h3 class="mb-0" id="totalVentas" style="color: #5A2828;">0</h3>
                                <p class="text-muted">Ventas realizadas en el periodo</p>
                            </div>
                        </div>
                    </div>
                    <!-- Ingresos Totales -->
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title" style="color: #5A2828;">Ingresos Totales</h5>
                                <h3 class="mb-0" id="ingresosTotales" style="color: #5A2828;">Bs. 0.00</h3>
                                <p class="text-muted">Total de ingresos del periodo</p>
                            </div>
                        </div>
                    </div>
                    <!-- Promedio por Venta -->
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title" style="color: #5A2828;">Promedio por Venta</h5>
                                <h3 class="mb-0" id="promedioVenta" style="color: #5A2828;">Bs. 0.00</h3>
                                <p class="text-muted">Valor promedio por venta</p>
                            </div>
                        </div>
                    </div>
                    <!-- Platos Vendidos -->
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title" style="color: #5A2828;">Platos Vendidos</h5>
                                <h3 class="mb-0" id="platosVendidos" style="color: #5A2828;">0</h3>
                                <p class="text-muted">Cantidad de platos vendidos en el periodo</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Ventas -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4>Gráfico de Ventas</h4>
                            <div class="btn-group no-print">
                                <button type="button" class="main-btn dark-btn btn-hover active" data-periodo="semana" style="background-color: #5A2828; border-color: #5A2828; font-size: 14px; padding: 5px 15px; border-radius: 6px; color: white;">SEMANAL</button>
                                <button type="button" class="main-btn dark-btn btn-hover" data-periodo="mes" style="background-color: #5A2828; border-color: #5A2828; font-size: 14px; padding: 5px 15px; border-radius: 6px; color: white;">MENSUAL</button>
                                <button type="button" class="main-btn dark-btn btn-hover" data-periodo="año" style="background-color: #5A2828; border-color: #5A2828; font-size: 14px; padding: 5px 15px; border-radius: 6px; color: white;">ANUAL</button>
                            </div>
                            <div class="ms-3">
                                <select id="tipoGrafico" class="form-select" style="width: 140px;">
                                    <option value="bar">Barras</option>
                                    <option value="line">Líneas</option>
                                    <option value="pie">Pastel</option>
                                </select>
                            </div>
                        </div>
                        <div id="chart-error" class="alert" style="display: none;"></div>
                        <div class="chart-container" style="position: relative; height:400px; background-color: #f9f9f9; border-radius: 8px; padding: 15px;">
                            <canvas id="ventasChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles del Periodo -->
            <div class="col-md-12 mt-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="color: #5A2828;">Detalles del Periodo</h5>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p><strong>Venta más alta:</strong> <span id="ventaMaxima">Bs. 0.00</span> <span id="platoMaxima"></span></p>
                                <p><strong>Venta más baja:</strong> <span id="ventaMinima">Bs. 0.00</span> <span id="platoMinima"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let ventasChart = null;
    let ultimoPeriodo = 'semana';
    let ultimoData = null;
    let ultimoLabels = [];
    let ultimoVentasData = [];
    let ultimoCantidadData = [];
    let tipoGrafico = 'bar';

    function mostrarError(mensaje) {
        const errorDiv = document.getElementById('chart-error');
        errorDiv.textContent = mensaje;
        errorDiv.style.display = 'block';
        errorDiv.style.backgroundColor = '#f8d7da';
        errorDiv.style.color = '#721c24';
        errorDiv.style.padding = '10px';
        errorDiv.style.marginBottom = '20px';
        errorDiv.style.borderRadius = '4px';
    }
    function formatearMoneda(valor) {
        return 'Bs. ' + parseFloat(valor || 0).toLocaleString('es-BO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
    function formatearFecha(fecha, periodo) {
        try {
            if (periodo === 'año') {
                // Si es anual, la fecha es solo el año
                return fecha;
            }
            if (periodo === 'mes' && /^\d{4}-\d{2}$/.test(fecha)) {
                // Si es mensual y formato YYYY-MM
                const [anio, mes] = fecha.split('-');
                const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                return `${meses[parseInt(mes, 10) - 1]} ${anio}`;
            }
            // Para semanal o fechas completas
            const date = new Date(fecha);
            if (isNaN(date.getTime())) {
                return fecha;
            }
            switch(periodo) {
                case 'semana':
                    return date.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric' });
                default:
                    return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
            }
        } catch (error) {
            return fecha;
        }
    }
    async function cargarDatos(periodo = 'semana') {
        const errorDiv = document.getElementById('chart-error');
        const canvas = document.getElementById('ventasChart');
        const fechaInicio = document.getElementById('fechaInicio').value;
        const fechaFin = document.getElementById('fechaFin').value;
        tipoGrafico = document.getElementById('tipoGrafico').value;
        try {
            errorDiv.style.display = 'none';
            let url = `/reportes/ventas-data?periodo=${periodo}`;
            if (fechaInicio) url += `&fecha_inicio=${fechaInicio}`;
            if (fechaFin) url += `&fecha_fin=${fechaFin}`;
            const response = await fetch(url);
            if (!response.ok) throw new Error('Error al cargar los datos. Estado: ' + response.status);
            const data = await response.json();
            console.log('Datos recibidos del backend:', data);
            if (!data || !data.datos || !data.estadisticas) throw new Error('Formato de datos inválido');
            if (data.datos.length === 0) {
                mostrarError('No hay datos de ventas para mostrar en el período seleccionado');
                canvas.style.display = 'none';
                return;
            }
            // Mostrar mensaje de éxito en consola
            console.log('Procesando datos para el gráfico...');
            document.getElementById('totalVentas').textContent = data.estadisticas.total_ventas || '0';
            document.getElementById('ingresosTotales').textContent = formatearMoneda(data.estadisticas.ingresos_totales);
            document.getElementById('promedioVenta').textContent = formatearMoneda(data.estadisticas.promedio_venta);
            document.getElementById('platosVendidos').textContent = data.estadisticas.platos_vendidos || '0';
            document.getElementById('ventaMaxima').textContent = data.venta_maxima ? formatearMoneda(data.venta_maxima.total) : 'Bs. 0.00';
            document.getElementById('platoMaxima').textContent = data.venta_maxima ? `(${data.venta_maxima.plato})` : '';
            document.getElementById('ventaMinima').textContent = data.venta_minima ? formatearMoneda(data.venta_minima.total) : 'Bs. 0.00';
            document.getElementById('platoMinima').textContent = data.venta_minima ? `(${data.venta_minima.plato})` : '';
            // Gráfico
            const labels = [];
            const ventasData = [];
            const cantidadData = [];
            data.datos.forEach((item, idx) => {
                console.log(`Item[${idx}]:`, item);
                labels.push(formatearFecha(item.fecha, periodo));
                ventasData.push(parseFloat(item.total_ventas) || 0);
                cantidadData.push(parseInt(item.cantidad_ventas) || 0);
            });
            console.log('Labels:', labels);
            console.log('VentasData:', ventasData);
            console.log('CantidadData:', cantidadData);
            ultimoPeriodo = periodo;
            ultimoData = data;
            ultimoLabels = labels;
            ultimoVentasData = ventasData;
            ultimoCantidadData = cantidadData;
            if (ventasChart) ventasChart.destroy();
            if (labels.length > 0) {
                const ctx = canvas.getContext('2d');
                ventasChart = new Chart(ctx, getChartConfig(tipoGrafico, labels, ventasData, cantidadData));
                canvas.style.display = 'block';
                canvas.style.opacity = '1';
                errorDiv.style.display = 'none';
            } else {
                mostrarError('No hay datos de ventas para mostrar en el período seleccionado');
                canvas.style.display = 'none';
            }
        } catch (error) {
            mostrarError('Error al cargar los datos: ' + error.message);
            canvas.style.opacity = '0.5';
            console.error('Error en cargarDatos:', error);
        }
    }
    function getChartConfig(tipo, labels, ventasData, cantidadData) {
        if (tipo === 'pie') {
            return {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Ventas (Bs)',
                        data: ventasData,
                        backgroundColor: [
                            '#5A2828', '#8B4513', '#A0522D', '#CD853F', '#D2B48C', '#F4A460', '#DEB887', '#FFE4C4', '#BC8F8F', '#FFDAB9', '#E9967A', '#FFB6C1'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        title: { display: true, text: 'Distribución de Ventas' }
                    }
                }
            };
        }
        // Barras o líneas
        return {
            type: tipo,
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total Ventas (Bs)',
                        data: ventasData,
                        borderColor: '#5A2828',
                        backgroundColor: 'rgba(90, 40, 40, 0.7)',
                        yAxisID: 'y',
                        fill: true
                    },
                    {
                        label: 'Cantidad de Ventas',
                        data: cantidadData,
                        borderColor: '#FF9800',
                        backgroundColor: 'rgba(255, 152, 0, 0.5)',
                        yAxisID: 'y1',
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Total Ventas (Bs)' },
                        ticks: { callback: value => formatearMoneda(value) }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'Cantidad de Ventas' },
                        grid: { drawOnChartArea: false }
                    }
                }
            }
        };
    }
    document.querySelectorAll('[data-periodo]').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('[data-periodo]').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');
            cargarDatos(this.dataset.periodo);
        });
    });
    document.getElementById('fechaInicio').addEventListener('change', function() {
        const periodoActivo = document.querySelector('[data-periodo].active').dataset.periodo;
        cargarDatos(periodoActivo);
    });
    document.getElementById('fechaFin').addEventListener('change', function() {
        const periodoActivo = document.querySelector('[data-periodo].active').dataset.periodo;
        cargarDatos(periodoActivo);
    });
    document.getElementById('tipoGrafico').addEventListener('change', function() {
        tipoGrafico = this.value;
        if (ultimoLabels.length > 0) {
            if (ventasChart) ventasChart.destroy();
            const canvas = document.getElementById('ventasChart');
            ventasChart = new Chart(canvas.getContext('2d'), getChartConfig(tipoGrafico, ultimoLabels, ultimoVentasData, ultimoCantidadData));
        }
    });
    cargarDatos('semana');
});
</script>
@endsection
