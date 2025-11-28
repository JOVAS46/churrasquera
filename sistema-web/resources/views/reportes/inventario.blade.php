@extends('layouts.app')

@section('content')
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title mb-30">
                    <h2>{{ __('Reportes de Inventario') }}</h2>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="lni lni-printer"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
    <!-- ========== title-wrapper end ========== -->

    <div class="container-fluid">
        <div class="row">
            <!-- Estadísticas del Inventario -->
            <div class="col-md-12 mb-4">
                <div class="row">
                    <!-- Total de Insumos -->
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title" style="color: #5A2828;">Total de Insumos</h5>
                                        <h3 class="mb-0" style="color: #5A2828;">{{ $totalInsumos ?? 0 }}</h3>
                                        <p class="text-muted mb-0">Insumos registrados</p>
                                    </div>
                                    <div>
                                        <i class="lni lni-package" style="font-size: 3rem; color: #5A2828; opacity: 0.3;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Insumos con Stock Bajo -->
                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title text-warning">Stock Bajo</h5>
                                        <h3 class="mb-0 text-warning">{{ $insumosStockBajo ?? 0 }}</h3>
                                        <p class="text-muted mb-0">Requieren reposición</p>
                                    </div>
                                    <div>
                                        <i class="lni lni-alarm" style="font-size: 3rem; color: #ffc107; opacity: 0.3;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Valor Total del Inventario -->
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title" style="color: #5A2828;">Valor Total</h5>
                                        <h3 class="mb-0" style="color: #5A2828;">Bs. {{ number_format($valorTotal ?? 0, 2) }}</h3>
                                        <p class="text-muted mb-0">En inventario</p>
                                    </div>
                                    <div>
                                        <i class="lni lni-dollar" style="font-size: 3rem; color: #5A2828; opacity: 0.3;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Categorías de Insumos -->
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title" style="color: #5A2828;">Categorías</h5>
                                        <h3 class="mb-0" style="color: #5A2828;">{{ $totalCategorias ?? 0 }}</h3>
                                        <p class="text-muted mb-0">Tipos de insumos</p>
                                    </div>
                                    <div>
                                        <i class="lni lni-grid-alt" style="font-size: 3rem; color: #5A2828; opacity: 0.3;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Insumos con Stock Bajo - Detalle -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header" style="background-color: #5A2828;">
                        <h5 class="mb-0 text-white">
                            <i class="lni lni-alarm"></i> Insumos con Stock Bajo
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(isset($insumosStockBajoDetalle) && count($insumosStockBajoDetalle) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Categoría</th>
                                            <th>Stock Actual</th>
                                            <th>Stock Mínimo</th>
                                            <th>Unidad</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($insumosStockBajoDetalle as $insumo)
                                            <tr>
                                                <td><strong>{{ $insumo->nombre }}</strong></td>
                                                <td>{{ $insumo->categoria->nombre ?? 'Sin categoría' }}</td>
                                                <td>
                                                    <span class="badge bg-warning">{{ $insumo->stock_actual }}</span>
                                                </td>
                                                <td>{{ $insumo->stock_minimo }}</td>
                                                <td>{{ $insumo->unidadMedida->abreviatura ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-danger">Crítico</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-success">
                                <i class="lni lni-checkmark-circle"></i>
                                Todos los insumos tienen stock suficiente
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Movimientos Recientes -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header" style="background-color: #5A2828;">
                        <h5 class="mb-0 text-white">
                            <i class="lni lni-shuffle"></i> Últimos Movimientos de Inventario
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(isset($movimientosRecientes) && count($movimientosRecientes) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Tipo</th>
                                            <th>Insumo</th>
                                            <th>Cantidad</th>
                                            <th>Usuario</th>
                                            <th>Observación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($movimientosRecientes as $movimiento)
                                            <tr>
                                                <td>{{ $movimiento->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    @if($movimiento->tipo_movimiento == 'entrada')
                                                        <span class="badge bg-success">
                                                            <i class="lni lni-arrow-down"></i> Entrada
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger">
                                                            <i class="lni lni-arrow-up"></i> Salida
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>{{ $movimiento->insumo->nombre ?? 'N/A' }}</td>
                                                <td>
                                                    <strong>{{ $movimiento->cantidad }}</strong>
                                                    {{ $movimiento->insumo->unidadMedida->abreviatura ?? '' }}
                                                </td>
                                                <td>{{ $movimiento->usuario->name ?? 'Sistema' }}</td>
                                                <td>{{ $movimiento->observacion ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="lni lni-information"></i>
                                No hay movimientos recientes registrados
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Insumos por Categoría -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header" style="background-color: #5A2828;">
                        <h5 class="mb-0 text-white">
                            <i class="lni lni-pie-chart"></i> Insumos por Categoría
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(isset($insumosPorCategoria) && count($insumosPorCategoria) > 0)
                            @foreach($insumosPorCategoria as $categoria)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>{{ $categoria->nombre }}</span>
                                        <strong>{{ $categoria->total_insumos }}</strong>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar" style="width: {{ ($categoria->total_insumos / $totalInsumos) * 100 }}%; background-color: #5A2828;"></div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No hay datos disponibles</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Próximos a Vencer (si aplica) -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header" style="background-color: #5A2828;">
                        <h5 class="mb-0 text-white">
                            <i class="lni lni-timer"></i> Estado del Inventario
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-success">Con stock suficiente</span>
                                <strong>{{ ($totalInsumos ?? 0) - ($insumosStockBajo ?? 0) }}</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ $totalInsumos > 0 ? ((($totalInsumos - $insumosStockBajo) / $totalInsumos) * 100) : 0 }}%;"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-warning">Stock bajo</span>
                                <strong>{{ $insumosStockBajo ?? 0 }}</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: {{ $totalInsumos > 0 ? (($insumosStockBajo / $totalInsumos) * 100) : 0 }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
