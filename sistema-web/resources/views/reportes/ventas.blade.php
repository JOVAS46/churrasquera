@extends('layouts.app')

@section('content')
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title mb-30">
                    <h2>{{ __('Estadísticas de Visitas y Accesos') }}</h2>
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
        <!-- Tarjetas de estadísticas principales -->
        <div class="row mb-4">
            <!-- Visitas Totales -->
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title" style="color: #5A2828;">Visitas Totales</h6>
                                <h3 class="mb-0" style="color: #5A2828;">{{ number_format($visitasTotales) }}</h3>
                                <p class="text-muted mb-0 small">Total acumulado</p>
                            </div>
                            <div>
                                <i class="lni lni-graph" style="font-size: 3rem; color: #5A2828; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visitas Hoy -->
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title text-success">Visitas Hoy</h6>
                                <h3 class="mb-0 text-success">{{ number_format($visitasHoy) }}</h3>
                                <p class="text-muted mb-0 small">{{ \Carbon\Carbon::today()->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <i class="lni lni-calendar" style="font-size: 3rem; color: #28a745; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visitas Esta Semana -->
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title text-warning">Visitas Esta Semana</h6>
                                <h3 class="mb-0 text-warning">{{ number_format($visitasSemana) }}</h3>
                                <p class="text-muted mb-0 small">Últimos 7 días</p>
                            </div>
                            <div>
                                <i class="lni lni-bar-chart" style="font-size: 3rem; color: #ffc107; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visitas Este Mes -->
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title text-info">Visitas Este Mes</h6>
                                <h3 class="mb-0 text-info">{{ number_format($visitasMes) }}</h3>
                                <p class="text-muted mb-0 small">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
                            </div>
                            <div>
                                <i class="lni lni-pie-chart" style="font-size: 3rem; color: #17a2b8; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card de Páginas Únicas -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="lni lni-files" style="font-size: 3rem; color: #5A2828;"></i>
                        <h3 class="mt-2" style="color: #5A2828;">{{ $paginasUnicas }}</h3>
                        <p class="text-muted mb-0">Páginas Únicas Visitadas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Páginas Más Visitadas -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="background-color: #5A2828;">
                        <h5 class="mb-0 text-white">
                            <i class="lni lni-stats-up"></i> Top 10 - Páginas Más Visitadas
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($paginasMasVisitadas) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Página</th>
                                            <th class="text-end">Total Visitas</th>
                                            <th style="width: 40%;">Distribución</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($paginasMasVisitadas as $index => $pagina)
                                            <tr>
                                                <td><strong>{{ $index + 1 }}</strong></td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $pagina->pagina }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <strong>{{ number_format($pagina->total_visitas) }}</strong>
                                                </td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar"
                                                             style="width: {{ ($pagina->total_visitas / $paginasMasVisitadas[0]->total_visitas) * 100 }}%; background-color: #5A2828;"
                                                             role="progressbar">
                                                            {{ round(($pagina->total_visitas / $visitasTotales) * 100, 1) }}%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="lni lni-information"></i>
                                No hay datos de visitas disponibles
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Visitas por Día (Últimos 7 días) -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header" style="background-color: #5A2828;">
                        <h5 class="mb-0 text-white">
                            <i class="lni lni-calendar"></i> Visitas por Día (Últimos 7 días)
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($visitasPorDia) > 0)
                            @foreach($visitasPorDia as $dia)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>{{ \Carbon\Carbon::parse($dia->fecha)->format('d/m/Y - l') }}</span>
                                        <strong>{{ number_format($dia->total_visitas) }} visitas</strong>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        @php
                                            $maxVisitas = $visitasPorDia->max('total_visitas');
                                            $porcentaje = $maxVisitas > 0 ? ($dia->total_visitas / $maxVisitas) * 100 : 0;
                                        @endphp
                                        <div class="progress-bar"
                                             style="width: {{ $porcentaje }}%; background-color: #5A2828;">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No hay datos de visitas en los últimos 7 días</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Visitas por Mes (Últimos meses) -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header" style="background-color: #5A2828;">
                        <h5 class="mb-0 text-white">
                            <i class="lni lni-pie-chart"></i> Visitas por Mes
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($visitasPorMes) > 0)
                            @foreach($visitasPorMes as $mes)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>{{ \Carbon\Carbon::parse($mes->mes)->translatedFormat('F Y') }}</span>
                                        <strong>{{ number_format($mes->total_visitas) }} visitas</strong>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        @php
                                            $maxVisitasMes = $visitasPorMes->max('total_visitas');
                                            $porcentajeMes = $maxVisitasMes > 0 ? ($mes->total_visitas / $maxVisitasMes) * 100 : 0;
                                        @endphp
                                        <div class="progress-bar bg-info"
                                             style="width: {{ $porcentajeMes }}%;">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No hay datos de visitas mensuales</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <h5 class="alert-heading">
                        <i class="lni lni-information"></i> Información
                    </h5>
                    <p class="mb-0">
                        Las estadísticas de visitas se actualizan automáticamente cada vez que un usuario accede a una página del sistema.
                        El contador de visitas está activo en todas las páginas del sitio web.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    @media print {
        .no-print, .btn {
            display: none !important;
        }
    }
</style>
@endsection
