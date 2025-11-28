@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Crear Nuevo Menú</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('menus.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre del Menú <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="icono" class="form-label">Ícono (Lineicons)</label>
                            <input type="text" class="form-control @error('icono') is-invalid @enderror"
                                id="icono" name="icono" value="{{ old('icono') }}"
                                placeholder="Ejemplo: lni lni-home">
                            @error('icono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Ver íconos en: <a href="https://lineicons.com/icons" target="_blank">lineicons.com</a>
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="url" class="form-label">URL</label>
                            <input type="text" class="form-control @error('url') is-invalid @enderror"
                                id="url" name="url" value="{{ old('url') }}"
                                placeholder="/ruta-del-menu">
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Dejar vacío si es menú padre con submenús</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="orden" class="form-label">Orden <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('orden') is-invalid @enderror"
                                id="orden" name="orden" value="{{ old('orden', 0) }}" min="0" required>
                            @error('orden')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="id_padre" class="form-label">Menú Padre</label>
                            <select class="form-select @error('id_padre') is-invalid @enderror" id="id_padre" name="id_padre">
                                <option value="">Ninguno (Menú Principal)</option>
                                @foreach ($menusPadre as $padre)
                                    <option value="{{ $padre->id_menu }}" {{ old('id_padre') == $padre->id_menu ? 'selected' : '' }}>
                                        {{ $padre->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_padre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="mostrar_en" class="form-label">Mostrar en <span class="text-danger">*</span></label>
                            <select class="form-select @error('mostrar_en') is-invalid @enderror" id="mostrar_en" name="mostrar_en" required>
                                <option value="sidebar" {{ old('mostrar_en') == 'sidebar' ? 'selected' : '' }}>Sidebar</option>
                                <option value="header" {{ old('mostrar_en') == 'header' ? 'selected' : '' }}>Header</option>
                                <option value="ambos" {{ old('mostrar_en', 'ambos') == 'ambos' ? 'selected' : '' }}>Ambos</option>
                            </select>
                            @error('mostrar_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="id_rol" class="form-label">Rol con Acceso</label>
                            <select class="form-select @error('id_rol') is-invalid @enderror" id="id_rol" name="id_rol">
                                <option value="">Todos los roles</option>
                                @foreach ($roles as $rol)
                                    <option value="{{ $rol->id_rol }}" {{ old('id_rol') == $rol->id_rol ? 'selected' : '' }}>
                                        {{ $rol->nombre_rol }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_rol')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="activo" name="activo"
                                    value="1" {{ old('activo', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo">
                                    Activo
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="lni lni-save"></i> Guardar
                        </button>
                        <a href="{{ route('menus.index') }}" class="btn btn-secondary">
                            <i class="lni lni-close"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
