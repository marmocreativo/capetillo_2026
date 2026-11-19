@extends('layouts.admin')

@section('admin-content')
<h1 class="text-2xl font-bold mb-1">Dashboard</h1>
<p class="opacity-60 mb-6">Bienvenido, {{ auth()->user()->name }}.</p>

{{-- FILA 1: Nuevos mensajes (scroll) + KPIs --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">

    <div class="card lg:col-span-2">
        <div class="card-body">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold">Últimos mensajes en espera de cotización</h3>
                <a href="{{ route('admin.contact-messages.index', ['status' => 'en_espera_cotizacion']) }}" class="text-xs link link-hover">Ver todos</a>
            </div>

            <div class="max-h-72 overflow-y-auto -mx-2 px-2">
                @forelse ($recentInitialMessages as $message)
                    <a href="{{ route('admin.contact-messages.edit', $message) }}"
                       class="flex items-center justify-between py-2.5 border-b border-white/5 last:border-0 hover:bg-primary/5 -mx-2 px-2 rounded-lg transition-colors">
                        <div class="min-w-0">
                            <p class="text-sm truncate {{ $message->isUnread() ? 'font-semibold' : 'font-medium' }}">
                                {{ $message->name }}
                                @if ($message->isUnread())
                                    <span class="badge badge-primary badge-xs ml-1">Nuevo</span>
                                @endif
                            </p>
                            <p class="text-xs opacity-60 truncate">{{ $message->talent_name ?? 'Consulta general' }} · {{ $message->email }}</p>
                        </div>
                        <span class="text-xs opacity-50 shrink-0 ml-3">{{ $message->created_at->diffForHumans() }}</span>
                    </a>
                @empty
                    <p class="text-sm opacity-60">No hay mensajes en espera de cotización.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-1 gap-4">
        @foreach ([
            ['Total de mensajes', number_format($totalMessages), 'Desde siempre', null],
            ['Mensajes este mes', number_format($monthMessages), now()->translatedFormat('F Y'), $monthComparison],
            ['Promedio por día', $dailyAverage, 'En lo que va del mes', $averageComparison],
        ] as [$label, $value, $hint, $cmp])
            <div class="card">
                <div class="card-body py-4 justify-center">
                    <p class="text-xs uppercase tracking-widest opacity-60">{{ $label }}</p>
                    <div class="flex items-baseline gap-2 flex-wrap">
                        <p class="text-3xl font-bold text-primary leading-tight">{{ $value }}</p>
                        @if ($cmp)
                            @php
                                $color = ['up' => 'text-success', 'down' => 'text-error', 'flat' => 'opacity-60'][$cmp['direction']];
                                $arrow = ['up' => '▲', 'down' => '▼', 'flat' => '■'][$cmp['direction']];
                            @endphp
                            <span class="text-sm font-semibold {{ $color }}">
                                {{ $arrow }} {{ $cmp['pct'] === null ? ($cmp['direction'] === 'up' ? 'Nuevo' : 'Sin cambio') : abs($cmp['pct']) . '%' }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs opacity-50">{{ $cmp ? $cmp['hint'] : $hint }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- FILA 2: Mensajes por día con filtro --}}
<div class="card mb-4" x-data="{ range: 30 }" x-init="$watch('range', v => window.setDailyRange(v))">
    <div class="card-body">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
            <div>
                <h3 class="font-semibold">Mensajes recibidos por día</h3>
                <p class="text-xs opacity-60">Últimos <span x-text="range"></span> días <span class="opacity-70">· <span class="inline-block w-4 border-t-2 border-dashed border-white/50 align-middle"></span> periodo anterior</span></p>
                <p class="text-xs mt-1"><span id="daily-summary" class="text-primary font-semibold"></span></p>
            </div>
            <div class="join">
                @foreach ([30, 15, 7] as $days)
                    <button type="button" class="btn btn-xs join-item"
                            :class="range === {{ $days }} ? 'btn-primary' : ''"
                            @click="range = {{ $days }}">{{ $days }} días</button>
                @endforeach
            </div>
        </div>
        <div class="h-64"><canvas id="dailyMessagesChart"></canvas></div>
    </div>
</div>

{{-- FILA 3: Artistas populares --}}
<div class="card mb-4">
    <div class="card-body">
        <h3 class="font-semibold">Artistas más populares</h3>
        <p class="text-xs opacity-60 mb-3">Con más solicitudes de contratación. Da clic en un artista para ver sus estadísticas.</p>

        @if ($talentCards->isEmpty())
            <p class="text-sm opacity-60 py-4">Aún no hay datos suficientes.</p>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-4 xl:grid-cols-8 gap-3">
                @foreach ($talentCards as $index => $talent)
                    <button type="button" onclick="openArtistModal({{ $index }})"
                            class="group relative aspect-[3/4] rounded-xl overflow-hidden text-left border border-white/10 hover:border-primary/60 transition-all hover:-translate-y-0.5 hover:shadow-[0_10px_30px_-10px_rgba(220,165,74,.5)]">
                        <img src="{{ $talent['photo'] }}" alt="{{ $talent['name'] }}" loading="lazy"
                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
                        <span class="absolute top-2 left-2 badge badge-primary badge-sm font-semibold">#{{ $index + 1 }}</span>
                        <div class="absolute bottom-0 inset-x-0 p-2.5">
                            <p class="text-sm font-semibold leading-tight line-clamp-2">{{ $talent['name'] }}</p>
                            <p class="text-xs text-primary">{{ $talent['total'] }} solicitudes</p>
                        </div>
                    </button>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- FILA 4: Mapa (desktop) --}}
<div class="card mb-4 hidden lg:block">
    <div class="card-body">
        <h3 class="font-semibold mb-1">Contactos de contratación por estado</h3>
        <p class="text-xs opacity-60 mb-3">El color indica el número de contactos. Pasa el mouse para ver un resumen y da clic en un estado para ver sus estadísticas.</p>

        @include('admin.partials.mexico-map')
    </div>
</div>

{{-- Lista por estado (móvil) --}}
<div class="card mb-4 lg:hidden">
    <div class="card-body">
        <h3 class="font-semibold mb-1">Contactos de contratación por estado</h3>
        <p class="text-xs opacity-60 mb-3">Toca un estado para ver sus estadísticas.</p>

        <div class="divide-y divide-white/5">
            @foreach (collect($mexicoMapData)->sortByDesc('count') as $svgId => $state)
                <button type="button" onclick="openStateModal('{{ $svgId }}')" class="w-full flex items-center justify-between py-2 text-left">
                    <div class="min-w-0">
                        <p class="text-sm font-medium truncate">{{ $state['name'] }}</p>
                        <p class="text-xs opacity-60 truncate">{{ $state['topTalent'] }}</p>
                    </div>
                    <span class="badge {{ $state['count'] > 0 ? 'badge-primary' : 'badge-ghost' }} badge-sm shrink-0 ml-3">{{ $state['count'] }}</span>
                </button>
            @endforeach
        </div>
    </div>
</div>

@include('admin.partials.stats-modal')

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    const dailyData = {!! json_encode($dailyMessages->values()) !!};
    const talentCards = {!! json_encode($talentCards->values()) !!};
    const stateData = {!! json_encode($mexicoMapData) !!};

    /* ---------- Gráfica diaria con filtro 30 / 15 / 7 y periodo anterior ---------- */
    // dailyData trae 60 días: el periodo actual son los últimos N y el anterior los N previos.
    function dailyPeriods(days) {
        const current = dailyData.slice(-days);
        const previous = dailyData.slice(-days * 2, -days);
        return { current, previous };
    }

    const dailyCtx = document.getElementById('dailyMessagesChart').getContext('2d');
    const grad = dailyCtx.createLinearGradient(0, 0, 0, 260);
    grad.addColorStop(0, 'rgba(220,165,74,.45)');
    grad.addColorStop(1, 'rgba(220,165,74,0)');

    let { current: curInit, previous: prevInit } = dailyPeriods(30);

    const dailyChart = new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: curInit.map(d => d.label),
            datasets: [
                {
                    label: 'Periodo actual',
                    data: curInit.map(d => d.total),
                    borderColor: '#e8c170',
                    backgroundColor: grad,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                    pointBackgroundColor: '#e8c170',
                    pointBorderColor: '#15130f',
                    pointBorderWidth: 2,
                    order: 1,
                },
                {
                    label: 'Periodo anterior',
                    data: prevInit.map(d => d.total),
                    borderColor: 'rgba(243,237,226,.55)',
                    borderDash: [6, 5],
                    borderWidth: 1.5,
                    fill: false,
                    tension: 0.35,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    order: 2,
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: items => items.length ? 'Día ' + (items[0].dataIndex + 1) + ' del periodo' : '',
                        label: item => {
                            const src = item.datasetIndex === 0 ? window.__periods.current : window.__periods.previous;
                            const d = src[item.dataIndex];
                            return ` ${item.dataset.label}: ${item.parsed.y} (${d ? d.date : ''})`;
                        }
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, ticks: { color: '#a3a3a3', precision: 0 }, grid: { color: 'rgba(220,165,74,.08)' } },
                x: { ticks: { color: '#a3a3a3', maxRotation: 0, autoSkip: true, maxTicksLimit: 10 }, grid: { display: false } }
            }
        }
    });

    window.setDailyRange = function (days) {
        const p = dailyPeriods(days);
        window.__periods = p;
        dailyChart.data.labels = p.current.map(d => d.label);
        dailyChart.data.datasets[0].data = p.current.map(d => d.total);
        dailyChart.data.datasets[1].data = p.previous.map(d => d.total);
        dailyChart.update();

        const sum = a => a.reduce((t, d) => t + d.total, 0);
        const cur = sum(p.current), prev = sum(p.previous);
        const pct = prev > 0 ? Math.round((cur - prev) / prev * 100) : null;
        const arrow = cur > prev ? '▲' : (cur < prev ? '▼' : '■');
        document.getElementById('daily-summary').textContent =
            `${cur} mensajes ${pct === null ? '' : `${arrow} ${Math.abs(pct)}% `}vs ${prev} del periodo anterior`;
    };
    window.setDailyRange(30);
    /* ---------- Modales de estadísticas (renderStats viene del parcial) ---------- */
    window.openArtistModal = function (index) {
        const t = talentCards[index];
        renderStats({ title: t.name, subtitle: 'Artista', photo: t.photo, link: t.edit_url, insightsUrl: t.insights_url }, t.stats, 'Estados que más lo solicitan');
    };

    window.openStateModal = function (svgId) {
        const s = stateData[svgId];
        if (!s) return;
        renderStats({ title: s.name, subtitle: 'Estado de la República' }, s.stats, 'Artistas más solicitados');
    };
</script>
@endsection
