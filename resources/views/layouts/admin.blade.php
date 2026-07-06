@extends('layouts.app')

@section('content')
<div class="drawer lg:drawer-open" x-data="{ collapsed: localStorage.getItem('admin-sidebar-collapsed') === 'true' }" x-init="$watch('collapsed', v => localStorage.setItem('admin-sidebar-collapsed', v))">
    <input id="admin-drawer" type="checkbox" class="drawer-toggle" />

    <div class="drawer-content flex flex-col">
        <div class="navbar bg-base-100 border-b border-base-300 lg:hidden">
            <label for="admin-drawer" class="btn btn-square btn-ghost">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="4" y1="6" x2="20" y2="6"></line>
                    <line x1="4" y1="12" x2="20" y2="12"></line>
                    <line x1="4" y1="18" x2="20" y2="18"></line>
                </svg>
            </label>
            <span class="text-lg font-semibold ml-2">Administración</span>
        </div>

        <main class="p-6">
            @yield('admin-content')
        </main>
    </div>

    <div class="drawer-side z-20">
        <label for="admin-drawer" aria-label="close sidebar" class="drawer-overlay"></label>

        <aside
            class="bg-base-100 min-h-full flex flex-col border-r border-base-300 transition-all duration-200"
            :class="collapsed ? 'w-20' : 'w-64'"
        >
            {{-- Header / Logo --}}
            <div class="h-24 flex items-center px-4 border-b border-base-300 shrink-0">
                <a href="{{ url('/admin') }}" class="flex items-center gap-2 overflow-hidden">
                    <img src="{{ asset('images/logo_capetillo_blanco.png') }}" alt="{{ config('app.name') }}" class="h-16 w-auto shrink-0">
                </a>
            </div>

            {{-- Navegación --}}
            <ul class="menu flex-1 p-3 gap-1">
                <li>
                    <a href="{{ url('/admin') }}" class="{{ request()->is('admin') ? 'menu-active' : '' }}" :class="collapsed && 'tooltip tooltip-right'" :data-tip="collapsed ? 'Dashboard' : null">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="9"></rect>
                            <rect x="14" y="3" width="7" height="5"></rect>
                            <rect x="14" y="12" width="7" height="9"></rect>
                            <rect x="3" y="16" width="7" height="5"></rect>
                        </svg>
                        <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.home-slides.index') }}" class="{{ request()->routeIs('admin.home-slides.*') ? 'menu-active' : '' }}" :class="collapsed && 'tooltip tooltip-right'" :data-tip="collapsed ? 'Slides del Home' : null">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <path d="m21 15-5-5L5 21"></path>
                        </svg>
                        <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Slides del Home</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'menu-active' : '' }}" :class="collapsed && 'tooltip tooltip-right'" :data-tip="collapsed ? 'Categorías' : null">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.59 13.41 11 3.83A2 2 0 0 0 9.59 3.24H4a1 1 0 0 0-1 1v5.59a2 2 0 0 0 .59 1.41l9.58 9.59a2 2 0 0 0 2.82 0l4.6-4.6a2 2 0 0 0 0-2.82Z"></path>
                            <circle cx="7.5" cy="7.5" r="1.5"></circle>
                        </svg>
                        <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Categorías</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.talents.index') }}" class="{{ request()->routeIs('admin.talents.*') ? 'menu-active' : '' }}" :class="collapsed && 'tooltip tooltip-right'" :data-tip="collapsed ? 'Talentos' : null">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Talentos</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.rosters.index') }}" class="{{ request()->routeIs('admin.home-slides.*') ? 'menu-active' : '' }}" :class="collapsed && 'tooltip tooltip-right'" :data-tip="collapsed ? 'Slides del Home' : null">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <path d="m21 15-5-5L5 21"></path>
                        </svg>
                        <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Rosters</span>
                    </a>
                </li>

                
            </ul>

            {{-- Footer: volver al sitio + colapsar + usuario --}}
            <div class="border-t border-base-300 p-3 shrink-0 flex flex-col gap-1">
                <a href="{{ url('/') }}" target="_blank" class="btn btn-ghost btn-sm justify-start gap-3 px-3" :class="collapsed && 'tooltip tooltip-right'" :data-tip="collapsed ? 'Volver al sitio' : null">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                    <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Volver al sitio</span>
                </a>

                <button type="button" @click="collapsed = !collapsed" class="btn btn-ghost btn-sm justify-start gap-3 px-3 hidden lg:flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 transition-transform" :class="collapsed && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="11 17 6 12 11 7"></polyline>
                        <polyline points="18 17 13 12 18 7"></polyline>
                    </svg>
                    <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Colapsar</span>
                </button>

                <div class="divider my-1"></div>

                {{-- Tarjeta de usuario --}}
                <div class="flex items-center gap-3 px-1 py-1 overflow-hidden">
                    <div class="avatar avatar-placeholder shrink-0">
                        <div class="bg-neutral text-neutral-content w-9 rounded-full">
                            <span class="text-sm">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                        </div>
                    </div>

                    <div x-show="!collapsed" x-transition.opacity class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name ?? 'Usuario' }}</p>
                        <p class="text-xs text-base-content/50 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" x-show="!collapsed" x-transition.opacity>
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-square btn-sm" :class="collapsed && 'tooltip tooltip-right'" data-tip="Cerrar sesión">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                        </button>
                    </form>
                </div>

                {{-- Botón de logout cuando está colapsado --}}
                <form method="POST" action="{{ route('logout') }}" x-show="collapsed" x-transition.opacity>
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm w-full justify-center tooltip tooltip-right" data-tip="Cerrar sesión">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                    </button>
                </form>
            </div>
        </aside>
    </div>
</div>
@endsection