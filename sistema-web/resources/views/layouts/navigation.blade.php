@php
    use App\Models\MenuNavegacion;
    use Illuminate\Support\Facades\Auth;

    // Obtener el rol del usuario autenticado
    $idRol = Auth::user()->id_rol ?? null;

    // Obtener el menú jerárquico para sidebar filtrado por rol
    $menus = MenuNavegacion::getMenuJerarquico('sidebar', $idRol);
@endphp

<ul>
    @foreach ($menus as $menu)
        @if ($menu->hijos->count() > 0)
            {{-- Menú con submenús --}}
            <li class="nav-item nav-item-has-children">
                <a href="#{{ $menu->id_menu }}"
                   class="{{ request()->is(ltrim($menu->url ?? '', '/') . '*') ? 'active collapsed' : 'collapsed' }}"
                   data-bs-toggle="collapse"
                   aria-expanded="{{ request()->is(ltrim($menu->url ?? '', '/') . '*') ? 'true' : 'false' }}"
                   aria-controls="{{ $menu->id_menu }}">
                    <span class="icon">
                        <i class="{{ $menu->icono ?? 'lni lni-menu' }}"></i>
                    </span>
                    <span class="text">{{ $menu->nombre }}</span>
                </a>
                <ul id="{{ $menu->id_menu }}"
                    class="collapse dropdown-nav {{ request()->is(ltrim($menu->url ?? '', '/') . '*') ? 'show' : '' }}">
                    @foreach ($menu->hijos as $submenu)
                        <li>
                            <a href="{{ $submenu->url ? url($submenu->url) : '#' }}"
                               class="{{ request()->is(ltrim($submenu->url ?? '', '/') . '*') ? 'active' : '' }}">
                                @if ($submenu->icono)
                                    <i class="{{ $submenu->icono }} me-2"></i>
                                @endif
                                {{ $submenu->nombre }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @else
            {{-- Menú simple sin submenús --}}
            <li class="nav-item {{ request()->is(ltrim($menu->url ?? '', '/') . '*') ? 'active' : '' }}">
                <a href="{{ $menu->url ? url($menu->url) : '#' }}">
                    <span class="icon">
                        <i class="{{ $menu->icono ?? 'lni lni-circle' }}"></i>
                    </span>
                    <span class="text">{{ $menu->nombre }}</span>
                </a>
            </li>
        @endif
    @endforeach
</ul>
