@extends('layouts.app')

@section('content')
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title mb-30">
                    <h2>{{ __('Bitácora del Sistema') }}</h2>
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
        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="filtroFechaInicio">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="filtroFechaFin">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tipo de Evento</label>
                                <select class="form-select" id="filtroTipo">
                                    <option value="">Todos</option>
                                    <option value="login">Inicio de Sesión</option>
                                    <option value="logout">Cierre de Sesión</option>
                                    <option value="create">Creación</option>
                                    <option value="update">Actualización</option>
                                    <option value="delete">Eliminación</option>
                                    <option value="venta">Venta</option>
                                    <option value="pedido">Pedido</option>
                                    <option value="reserva">Reserva</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary w-100" onclick="filtrarBitacora()">
                                    <i class="lni lni-search-alt"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas de Actividad -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="lni lni-users" style="font-size: 2.5rem; color: #5A2828;"></i>
                        <h3 class="mt-2" style="color: #5A2828;">{{ $usuariosActivos ?? 0 }}</h3>
                        <p class="text-muted mb-0">Usuarios Activos Hoy</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="lni lni-checkbox" style="font-size: 2.5rem; color: #28a745;"></i>
                        <h3 class="mt-2 text-success">{{ $eventosHoy ?? 0 }}</h3>
                        <p class="text-muted mb-0">Eventos Hoy</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="lni lni-calendar" style="font-size: 2.5rem; color: #ffc107;"></i>
                        <h3 class="mt-2 text-warning">{{ $eventosSemana ?? 0 }}</h3>
                        <p class="text-muted mb-0">Eventos Esta Semana</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="lni lni-archive" style="font-size: 2.5rem; color: #17a2b8;"></i>
                        <h3 class="mt-2 text-info">{{ $eventosMes ?? 0 }}</h3>
                        <p class="text-muted mb-0">Eventos Este Mes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registro de Actividades -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="background-color: #5A2828;">
                        <h5 class="mb-0 text-white">
                            <i class="lni lni-list"></i> Registro de Actividades del Sistema
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(isset($actividades) && count($actividades) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fecha y Hora</th>
                                            <th>Usuario</th>
                                            <th>Rol</th>
                                            <th>Tipo de Evento</th>
                                            <th>Módulo</th>
                                            <th>Descripción</th>
                                            <th>IP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($actividades as $actividad)
                                            <tr>
                                                <td>{{ $actividad->created_at->format('d/m/Y H:i:s') }}</td>
                                                <td>
                                                    <strong>{{ $actividad->usuario->name ?? 'Sistema' }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        {{ $actividad->usuario->rol->nombre ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $badgeClass = match($actividad->tipo) {
                                                            'login' => 'bg-success',
                                                            'logout' => 'bg-secondary',
                                                            'create' => 'bg-primary',
                                                            'update' => 'bg-warning',
                                                            'delete' => 'bg-danger',
                                                            'venta' => 'bg-info',
                                                            default => 'bg-dark'
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">
                                                        {{ ucfirst($actividad->tipo) }}
                                                    </span>
                                                </td>
                                                <td>{{ $actividad->modulo ?? '-' }}</td>
                                                <td>{{ $actividad->descripcion }}</td>
                                                <td><small>{{ $actividad->ip_address ?? 'N/A' }}</small></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginación -->
                            @if(method_exists($actividades, 'links'))
                                <div class="mt-3">
                                    {{ $actividades->links() }}
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info">
                                <i class="lni lni-information"></i>
                                No hay actividades registradas en el período seleccionado
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Actividad por Usuario (Top 10) -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header" style="background-color: #5A2828;">
                        <h5 class="mb-0 text-white">
                            <i class="lni lni-stats-up"></i> Usuarios Más Activos
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(isset($usuariosMasActivos) && count($usuariosMasActivos) > 0)
                            @foreach($usuariosMasActivos as $index => $usuario)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>
                                            <strong>{{ $index + 1 }}.</strong> {{ $usuario->name }}
                                            <small class="text-muted">({{ $usuario->rol->nombre ?? 'N/A' }})</small>
                                        </span>
                                        <strong>{{ $usuario->total_actividades }} actividades</strong>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar" style="width: {{ ($usuario->total_actividades / $usuariosMasActivos[0]->total_actividades) * 100 }}%; background-color: #5A2828;"></div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No hay datos disponibles</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header" style="background-color: #5A2828;">
                        <h5 class="mb-0 text-white">
                            <i class="lni lni-pie-chart"></i> Eventos por Tipo
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(isset($eventosPorTipo) && count($eventosPorTipo) > 0)
                            @foreach($eventosPorTipo as $evento)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-capitalize">{{ $evento->tipo }}</span>
                                        <strong>{{ $evento->total }}</strong>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        @php
                                            $totalEventos = collect($eventosPorTipo)->sum('total');
                                            $porcentaje = ($evento->total / $totalEventos) * 100;
                                        @endphp
                                        <div class="progress-bar" style="width: {{ $porcentaje }}%; background-color: #5A2828;"></div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No hay datos disponibles</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
function filtrarBitacora() {
    const fechaInicio = document.getElementById('filtroFechaInicio').value;
    const fechaFin = document.getElementById('filtroFechaFin').value;
    const tipo = document.getElementById('filtroTipo').value;

    let url = '/reportes/bitacora?';
    if (fechaInicio) url += `fecha_inicio=${fechaInicio}&`;
    if (fechaFin) url += `fecha_fin=${fechaFin}&`;
    if (tipo) url += `tipo=${tipo}&`;

    window.location.href = url;
}
</script>
@endsection
