<main class="min-h-screen pt-24 pb-12 px-6">
    <div class="max-w-6xl mx-auto">
        
        <div class="mb-8 border-b border-slate-700 pb-6">
            <h1 class="text-3xl md:text-4xl font-bold text-white flex items-center gap-3">
                <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h6a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V5z"></path>
                </svg>
                Mis Libros Guardados
            </h1>
            <p class="text-gray-400 mt-2 text-lg">Tu colección personal de recursos favoritos.</p>
        </div>

        <div class="bg-slate-800 bg-opacity-50 border border-slate-700 rounded-lg p-8 shadow-xl">
            <div id="saved-books-grid" class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
            </div>
        </div>

    </div>
</main>

<script>
const colorMap = {
    'Alpha Cloud': { hover: 'hover:border-cyan-500', shadow: 'shadow-cyan-500/10', border: 'border-cyan-500', text: 'text-cyan-400', bg: 'bg-slate-800' },
    'e-Libro': { hover: 'hover:border-orange-500', shadow: 'shadow-orange-500/10', border: 'border-orange-500', text: 'text-orange-400', bg: 'bg-slate-800' },
    'Inventario UPT': { hover: 'hover:border-blue-500', shadow: 'shadow-blue-500/10', border: 'border-blue-700', text: 'text-white', badgeBg: 'bg-blue-800', bg: 'bg-slate-800' }
};

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function escapeJs(value) {
    return String(value ?? '')
        .replace(/\\/g, '\\\\')
        .replace(/'/g, "\\'");
}

async function loadSavedBooks() {
    const grid = document.getElementById('saved-books-grid');
    if (!grid) {
        return;
    }

    const userUuid = (localStorage.getItem('user_uuid') || '').trim();
    if (userUuid === '') {
        grid.innerHTML = '<div class="col-span-full rounded-lg border border-slate-700 bg-slate-800/60 p-6 text-center text-gray-300">Inicia sesión para ver tus libros guardados.</div>';
        return;
    }

    try {
        const response = await fetch(`/nexuslib/gateway-service/public/index.php/api/user-library/saved?user_uuid=${encodeURIComponent(userUuid)}`);
        const payload = await response.json();
        const books = Array.isArray(payload?.data) ? payload.data : [];

        if (!response.ok) {
            throw new Error(payload?.error || 'No se pudieron cargar los guardados');
        }

        if (books.length === 0) {
            grid.innerHTML = '<div class="col-span-full rounded-lg border border-slate-700 bg-slate-800/60 p-6 text-center text-gray-300">Aún no tienes libros guardados.</div>';
            return;
        }

        grid.innerHTML = books.map((libro) => {
            const origenRaw = String(libro?.origen || 'Desconocido').trim();
            const tituloRaw = String(libro?.titulo || 'Sin título').trim();
            const codigoRaw = String(libro?.codigo || '').trim();
            const portadaRaw = String(libro?.portada_url || '').trim();
            const colors = colorMap[origenRaw] || { hover: 'hover:border-slate-500', shadow: 'shadow-slate-500/10', border: 'border-slate-500', text: 'text-slate-300', badgeBg: 'bg-slate-800', bg: 'bg-slate-800' };
            const isPhysical = origenRaw === 'Inventario UPT' || origenRaw === 'Biblioteca UPT';
            const detailsView = isPhysical ? 'details/details_physical' : 'details/details_digital';
            const detailsHref = `${window.location.pathname}?view=${encodeURIComponent(detailsView)}&id=${encodeURIComponent(codigoRaw)}&titulo=${encodeURIComponent(tituloRaw)}&origen=${encodeURIComponent(origenRaw)}`;

            const title = escapeHtml(tituloRaw);
            const origin = escapeHtml(origenRaw);
            const portada = escapeHtml(portadaRaw);
            const codigoJs = escapeJs(codigoRaw);
            const origenJs = escapeJs(origenRaw);
            const hasCover = portadaRaw !== '';
            const coverHtml = hasCover
                ? `<img src="${portada}" alt="Portada de ${title}" class="w-20 h-28 rounded flex-shrink-0 shadow-md object-cover">`
                : (origenRaw === 'Inventario UPT'
                    ? `<img src="/nexuslib/frontend/public/images/logo-upt.png" alt="Portada UPT" class="w-20 h-28 rounded flex-shrink-0 shadow-md object-cover">`
                    : `<div class="w-20 h-28 bg-slate-800 border-2 border-slate-700 rounded-lg flex flex-col items-center justify-center text-gray-500 flex-shrink-0 shadow-md">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span class="text-[10px] font-semibold uppercase tracking-wider">Sin Portada</span>
                    </div>`
                );

            return `
                <div class="bg-slate-700 bg-opacity-40 border border-slate-600 rounded-lg p-5 ${colors.hover} transition-all hover:shadow-lg ${colors.shadow} group h-full flex flex-col">
                    <div class="flex gap-6">
                        ${coverHtml}
                        <div class="flex-1 flex flex-col justify-center">
                            <h4 class="text-lg text-white font-semibold transition-colors">${title}</h4>
                            <span class="inline-block mt-2 w-max ${colors.badgeBg || 'bg-slate-800'} border ${colors.border} ${colors.text} text-xs px-3 py-1 rounded-full">${origin}</span>
                            <div class="flex gap-3 mt-4">
                                <a href="${detailsHref}" class="bg-slate-700 hover:bg-slate-600 px-4 py-2 rounded-lg text-sm transition-colors text-white">Ver detalles</a>
                                <button type="button" onclick="removeSavedBook('${codigoJs}', '${origenJs}')" class="text-red-400 hover:text-red-300 text-sm font-medium flex items-center gap-1 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    } catch (_error) {
        grid.innerHTML = '<div class="col-span-full rounded-lg border border-red-400/60 bg-red-500/10 p-6 text-center text-red-200">No se pudieron cargar tus libros guardados.</div>';
    }
}

window.removeSavedBook = async function(codigo, origen) {
    const userUuid = (localStorage.getItem('user_uuid') || '').trim();
    if (userUuid === '') {
        if (typeof window.showToast === 'function') {
            window.showToast('Debes iniciar sesión para gestionar guardados', 'error');
        }
        return;
    }

    try {
        const response = await fetch('/nexuslib/gateway-service/public/index.php/api/user-library/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_uuid: userUuid,
                codigo: codigo,
                origen: origen,
                titulo: 'N/A'
            })
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok || data.action !== 'removed') {
            throw new Error(data.error || 'No se pudo eliminar el libro');
        }

        await loadSavedBooks();
        if (typeof window.showToast === 'function') {
            window.showToast('Libro eliminado de guardados', 'success');
        }
    } catch (_error) {
        if (typeof window.showToast === 'function') {
            window.showToast('No se pudo eliminar el libro', 'error');
        }
    }
};

document.addEventListener('DOMContentLoaded', loadSavedBooks);
window.addEventListener('pageshow', function(event) {
    if (event.persisted) { loadSavedBooks(); }
});
</script>