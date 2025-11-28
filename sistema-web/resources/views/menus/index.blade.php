@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Gestión de Menú de Navegación</h4>
                <a href="{{ route('menus.create') }}" class="btn btn-primary">
                    <i class="lni lni-plus"></i> Nuevo Menú
                </a>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Ícono</th>
                                <th>URL</th>
                                <th>Padre</th>
                                <th>Rol</th>
                                <th>Orden</th>
                                <th>Mostrar en</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($menus as $menu)
                                <tr class="{{ $menu->id_padre ? 'table-secondary' : '' }}">
                                    <td>{{ $menu->id_menu }}</td>
                                    <td>
                                        @if ($menu->id_padre)
                                            <i class="lni lni-arrow-right me-2"></i>
                                        @endif
                                        {{ $menu->nombre }}
                                    </td>
                                    <td>
                                        @if ($menu->icono)
                                            <i class="{{ $menu->icono }}"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $menu->url ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @if ($menu->padre)
                                            <span class="badge bg-info">{{ $menu->padre->nombre }}</span>
                                        @else
                                            <span class="badge bg-secondary">Principal</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($menu->rol)
                                            <span class="badge bg-warning">{{ $menu->rol->nombre_rol }}</span>
                                        @else
                                            <span class="badge bg-success">Todos</span>
                                        @endif
                                    </td>
                                    <td>{{ $menu->orden }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ ucfirst($menu->mostrar_en) }}</span>
                                    </td>
                                    <td>
                                        <form action="{{ route('menus.toggle', $menu->id_menu) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $menu->activo ? 'btn-success' : 'btn-danger' }}">
                                                {{ $menu->activo ? 'Activo' : 'Inactivo' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('menus.edit', $menu->id_menu) }}" class="btn btn-sm btn-warning">
                                                <i class="lni lni-pencil"></i>
                                            </a>
                                            <form action="{{ route('menus.destroy', $menu->id_menu) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('¿Está seguro de eliminar este menú?')">
                                                    <i class="lni lni-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No hay menús registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
