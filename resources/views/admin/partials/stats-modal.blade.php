{{-- Modal de estadísticas reutilizable: window.renderStats(info, stats, topsTitle) --}}
<dialog id="stats-modal" class="modal">
    <div class="modal-box max-w-lg">
        <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-3 top-3">✕</button></form>
        <div id="stats-modal-body"></div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>cerrar</button></form>
</dialog>

<script>
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const money = n => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', maximumFractionDigits: 0 }).format(n);

    window.renderStats = function ({ title, subtitle, photo, link, insightsUrl }, stats, topsTitle) {
        const kpi = (label, value) => `
            <div class="rounded-xl border border-white/10 bg-black/20 p-3">
                <p class="text-[10px] uppercase tracking-widest opacity-60">${label}</p>
                <p class="text-xl font-bold text-primary">${value}</p>
            </div>`;

        const bars = (items, max) => items.length ? items.map(i => `
            <div class="mb-2">
                <div class="flex justify-between text-xs mb-1"><span class="truncate">${esc(i.name ?? i.label)}</span><span class="opacity-70">${i.count}</span></div>
                <div class="h-1.5 rounded-full bg-white/10 overflow-hidden"><div class="h-full rounded-full bg-gradient-to-r from-[#a9812f] to-[#e8c170]" style="width:${Math.max(6, i.count / max * 100)}%"></div></div>
            </div>`).join('') : '<p class="text-xs opacity-50">Sin datos.</p>';

        const maxTop = Math.max(1, ...stats.tops.map(t => t.count));
        const maxSt = Math.max(1, ...stats.statuses.map(t => t.count));

        document.getElementById('stats-modal-body').innerHTML = `
            <div class="flex items-center gap-3 mb-4 pr-8">
                ${photo ? `<img src="${esc(photo)}" class="w-14 h-14 rounded-xl object-cover border border-primary/40">` : ''}
                <div class="min-w-0">
                    <h3 class="text-lg font-bold truncate">${esc(title)}</h3>
                    <p class="text-xs opacity-60">${esc(subtitle)}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 mb-4">
                ${kpi('Solicitudes', stats.total)}
                ${kpi('Últimos 30 días', stats.last30)}
                ${kpi('Contratos cerrados', stats.closed)}
                ${kpi('Pagado', money(stats.paid))}
            </div>
            <h4 class="text-xs uppercase tracking-widest text-primary mb-2">${topsTitle}</h4>
            <div class="mb-4">${bars(stats.tops, maxTop)}</div>
            <h4 class="text-xs uppercase tracking-widest text-primary mb-2">Por estatus</h4>
            <div class="mb-3">${bars(stats.statuses, maxSt)}</div>
            <div class="flex items-center justify-between text-xs opacity-60">
                <span>Última solicitud: ${stats.lastAt ?? '—'}</span>
                ${link ? `<a href="${esc(link)}" class="link link-hover">Ver ficha</a>` : ''}
            </div>`;
        document.getElementById('stats-modal').showModal();
        if (insightsUrl) loadGoogleInsights(insightsUrl);
    };

    /* ---------- Google Analytics + Search Console (carga diferida) ---------- */
    function loadGoogleInsights(url) {
        const box = document.createElement('div');
        box.id = 'google-insights';
        box.className = 'mt-5 pt-4 border-t border-white/10';
        box.innerHTML = '<p class="text-xs opacity-60 flex items-center gap-2"><span class="loading loading-spinner loading-xs"></span> Consultando Google…</p>';
        document.getElementById('stats-modal-body').appendChild(box);

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(r => { if (!r.ok) throw new Error(); return r.json(); })
            .then(d => { if (box.isConnected) box.innerHTML = renderGoogle(d); })
            .catch(() => { if (box.isConnected) box.innerHTML = '<p class="text-xs text-error">No se pudo consultar Google.</p>'; });
    }

    const note = (txt, cls = 'opacity-60') => `<p class="text-xs ${cls}">${txt}</p>`;
    const fmt = n => new Intl.NumberFormat('es-MX').format(n);

    function renderGoogle({ analytics, searchConsole }) {
        const title = t => `<h4 class="text-xs uppercase tracking-widest text-primary mb-2">${t}</h4>`;
        const tile = (label, value, extra = '') => `
            <div class="rounded-xl border border-white/10 bg-black/20 p-3">
                <p class="text-[10px] uppercase tracking-widest opacity-60">${label}</p>
                <p class="text-xl font-bold text-primary">${value} ${extra}</p>
            </div>`;
        const delta = (cur, prev) => {
            if (!prev) return cur > 0 ? '<span class="text-xs font-semibold text-success">▲ Nuevo</span>' : '';
            const pct = Math.round((cur - prev) / prev * 100);
            const cls = pct > 0 ? 'text-success' : (pct < 0 ? 'text-error' : 'opacity-60');
            return `<span class="text-xs font-semibold ${cls}">${pct > 0 ? '▲' : (pct < 0 ? '▼' : '■')} ${Math.abs(pct)}%</span>`;
        };

        let html = '';

        // Visitas (GA4)
        html += title('Visitas a la página (últimos 30 días)');
        if (analytics.status === 'not_configured') {
            html += note('Google Analytics aún no está configurado (credenciales y GOOGLE_GA4_PROPERTY_ID).');
        } else if (analytics.status === 'error') {
            html += note('Analytics: ' + esc(analytics.message), 'text-error');
        } else {
            const { current, previous } = analytics.data;
            html += `<div class="grid grid-cols-2 gap-2 mb-1">
                ${tile('Vistas', fmt(current.views), delta(current.views, previous.views))}
                ${tile('Usuarios', fmt(current.users), delta(current.users, previous.users))}
            </div>${note('Comparado con los 30 días anteriores (' + fmt(previous.views) + ' vistas).')}`;
        }

        // Búsquedas (Search Console)
        html += '<div class="mt-4">' + title('Búsquedas de Google que mostraron la página') + '</div>';
        if (searchConsole.status === 'not_configured') {
            html += note('Search Console aún no está configurado (credenciales y GOOGLE_SEARCH_CONSOLE_SITE).');
        } else if (searchConsole.status === 'error') {
            html += note('Search Console: ' + esc(searchConsole.message), 'text-error');
        } else {
            const g = searchConsole.data;
            html += `<div class="grid grid-cols-3 gap-2 mb-3">
                ${tile('Impresiones', fmt(g.impressions))}
                ${tile('Clics', fmt(g.clicks))}
                ${tile('Posición', g.position ?? '—')}
            </div>`;
            if (!g.queries.length) {
                html += note('Sin búsquedas registradas en el periodo.');
            } else {
                html += `<div class="overflow-x-auto"><table class="table table-xs">
                    <thead><tr><th>Búsqueda</th><th class="text-right">Impr.</th><th class="text-right">Clics</th><th class="text-right">Pos.</th></tr></thead>
                    <tbody>${g.queries.map(q => `<tr>
                        <td class="max-w-[14rem] truncate" title="${esc(q.query)}">${esc(q.query)}</td>
                        <td class="text-right">${fmt(q.impressions)}</td>
                        <td class="text-right">${fmt(q.clicks)}</td>
                        <td class="text-right">${q.position}</td></tr>`).join('')}</tbody>
                </table></div>`;
            }
            html += note('Periodo: ' + esc(g.period) + ' (Search Console tarda ~3 días en reportar).');
        }
        return html;
    }

</script>
