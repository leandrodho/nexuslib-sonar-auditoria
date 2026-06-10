<main class="min-h-screen pt-24 pb-12 px-6">
    <div id="loading-spinner" class="max-w-6xl mx-auto px-4 py-8 flex flex-col items-center justify-center gap-4 text-gray-300">
        <div class="h-12 w-12 animate-spin rounded-full border-4 border-slate-600 border-t-cyan-400"></div>
        <p class="text-sm md:text-base">Cargando detalles del libro...</p>
    </div>

    <div id="content-container" class="max-w-6xl mx-auto hidden">

        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-12">
            <h1 id="det-titulo" class="text-4xl md:text-5xl font-bold text-white flex-1">
                Analítica de Datos para la Toma de Decisiones
            </h1>

            <button id="btn-favorite-details" onclick="toggleFavoriteDetailsUI(this)" class="flex flex-shrink-0 items-center gap-2 px-4 py-2 mt-1 bg-slate-800 border border-slate-600 rounded-lg text-gray-400 hover:text-cyan-400 hover:border-cyan-400 transition-colors duration-200 shadow-sm">
                <svg class="w-5 h-5 favorite-icon fill-none transition-colors duration-200" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                </svg>
                <span class="btn-text font-medium text-sm md:text-base">Guardar Libro</span>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1">
                <div class="w-full bg-slate-800 rounded-lg overflow-hidden flex items-center justify-center shadow-xl relative aspect-[3/4] border border-slate-700">
                    <img id="det-portada" src="https://via.placeholder.com/400x600/1e293b/06b6d4?text=Portada+Mock" alt="Portada de Prueba" class="w-full h-full object-cover">
                </div>
            </div>

            <div class="lg:col-span-2 space-y-8">
                
                <div class="bg-slate-800 bg-opacity-50 border border-slate-700 rounded-lg p-8 shadow-md">
                    <h2 class="text-2xl font-bold text-white mb-8 flex items-center gap-3 border-b border-slate-700 pb-4">
                        <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                        Ficha Técnica del Catálogo
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8">
                        <div>
                            <p class="text-cyan-400 text-sm font-medium uppercase tracking-wider mb-1">Autor</p>
                            <p id="det-autor" class="text-white font-semibold text-lg">Autor de Prueba</p>
                        </div>
                        <div id="det-editorial-row">
                            <p class="text-cyan-400 text-sm font-medium uppercase tracking-wider mb-1">Editorial</p>
                            <p id="det-editorial" class="text-white font-semibold text-lg">Editorial Universitaria Mock</p>
                        </div>
                        <div>
                            <p class="text-cyan-400 text-sm font-medium uppercase tracking-wider mb-1">Año de publicación</p>
                            <p id="det-ano-publicacion" class="text-white font-semibold text-lg">2024</p>
                        </div>
                        <div>
                            <p class="text-cyan-400 text-sm font-medium uppercase tracking-wider mb-1">ISBN</p>
                            <p id="det-isbn" class="text-gray-300 font-mono text-lg">978-0000000000</p>
                        </div>
                        <div class="sm:col-span-2 pt-2">
                            <p class="text-cyan-400 text-sm font-medium uppercase tracking-wider mb-2">Origen / Biblioteca</p>
                            <span id="det-origen" class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold bg-slate-700 text-cyan-300 border border-cyan-800 shadow-sm">
                                e-Libro
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-cyan-900 to-blue-950 border-2 border-cyan-600 rounded-lg p-8 shadow-lg shadow-cyan-600/30">
                    <h2 class="text-2xl font-bold text-white mb-6">Acceso al recurso digital</h2>

                    <a id="det-url-acceso" href="#" target="_blank" rel="noopener noreferrer" class="w-full mt-2 bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-600 hover:to-cyan-700 text-white font-bold py-4 px-6 rounded-lg transition-all transform hover:scale-105 shadow-lg text-lg flex items-center justify-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.868v4.264a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Leer Libro en Línea
                    </a>
                </div>

            </div>

        </div>
    </div>
</main>

<script>
window.detailFavoriteContext = {
    codigo: '',
    origen: '',
    titulo: '',
    portadaUrl: '',
};

window.setFavoriteDetailsState = function(button, isSaved) {
    if (!button) {
        return;
    }

    const svg = button.querySelector('.favorite-icon');
    const span = button.querySelector('.btn-text');

    button.classList.toggle('text-gray-400', !isSaved);
    button.classList.toggle('text-yellow-400', isSaved);

    if (svg) {
        svg.classList.toggle('text-yellow-400', isSaved);
        svg.classList.toggle('fill-current', isSaved);
        svg.classList.toggle('fill-none', !isSaved);
    }

    if (span) {
        span.textContent = isSaved ? 'Libro Guardado' : 'Guardar Libro';
    }
};

window.toggleFavoriteDetailsUI = async function(button) {
    if (!localStorage.getItem('user_uuid')) {
        if (typeof window.showToast === 'function') {
            window.showToast('Debes iniciar sesión para guardar libros', 'error');
        } else {
            alert('Debes iniciar sesión para guardar libros');
        }
        return;
    }

    const userUuid = localStorage.getItem('user_uuid') || '';
    const context = window.detailFavoriteContext || {};
    const codigo = String(context.codigo || '').trim();
    const origen = String(context.origen || '').trim();
    const titulo = String(context.titulo || '').trim();
    const portadaUrl = String(context.portadaUrl || '').trim();

    if (codigo === '' || origen === '' || titulo === '') {
        if (typeof window.showToast === 'function') {
            window.showToast('No se pudo identificar el libro', 'error');
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
                titulo: titulo,
                portada_url: portadaUrl === '' ? null : portadaUrl
            })
        });

        let data = {};
        try {
            data = await response.json();
        } catch (_error) {
            data = {};
        }

        if (!response.ok) {
            if (typeof window.showToast === 'function') {
                window.showToast(data.error || 'No se pudo actualizar favoritos', 'error');
            }
            return;
        }

        if (data.action === 'added') {
            window.setFavoriteDetailsState(button, true);
        } else if (data.action === 'removed') {
            window.setFavoriteDetailsState(button, false);
        }
    } catch (_error) {
        if (typeof window.showToast === 'function') {
            window.showToast('Error de conexión al actualizar favoritos', 'error');
        }
    }
};

(function () {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id') || '';
    const titulo = params.get('titulo') || '';
    const origen = params.get('origen') || '';

    if (!id || !titulo || !origen) {
        return;
    }

    fetch(`/nexuslib/gateway-service/public/index.php/api/details?id=${encodeURIComponent(id)}&titulo=${encodeURIComponent(titulo)}&origen=${encodeURIComponent(origen)}`)
        .then((response) => response.json())
        .then((data) => {
            if (data.error) {
                return;
            }

            const extra = data.detalles_extra || {};
            const tituloEl = document.getElementById('det-titulo');
            const portadaEl = document.getElementById('det-portada');
            const autorEl = document.getElementById('det-autor');
            const editorialRowEl = document.getElementById('det-editorial-row');
            const editorialEl = document.getElementById('det-editorial');
            const anoEl = document.getElementById('det-ano-publicacion');
            const isbnEl = document.getElementById('det-isbn');
            const origenEl = document.getElementById('det-origen');
            const sinopsisEl = document.getElementById('det-sinopsis');
            const urlAccesoEl = document.getElementById('det-url-acceso');

            if (tituloEl) {
                tituloEl.textContent = data.titulo || 'Sin título';
            }
            if (portadaEl && data.portada_url) {
                portadaEl.src = data.portada_url;
                portadaEl.alt = `Portada de ${data.titulo || 'Recurso digital'}`;
            }
            if (autorEl) {
                autorEl.textContent = data.autor || 'Desconocido';
            }
            if (origenEl) {
                origenEl.textContent = data.origen || origen;
            }
            if (anoEl) {
                anoEl.textContent = extra.ano_publicacion || 'No disponible';
            }
            if (isbnEl) {
                isbnEl.textContent = extra.isbn || 'No disponible';
            }
            if (sinopsisEl) {
                sinopsisEl.textContent = extra.sinopsis || 'Sin sinopsis disponible.';
            }

            const editorialRaw = (extra.editorial || '').trim();
            if (editorialEl) {
                editorialEl.textContent = editorialRaw;
            }
            if (editorialRowEl) {
                if (editorialRaw === '') {
                    editorialRowEl.classList.add('hidden');
                } else {
                    editorialRowEl.classList.remove('hidden');
                }
            }

            if (urlAccesoEl && data.url_acceso) {
                urlAccesoEl.href = data.url_acceso;
                urlAccesoEl.target = '_blank';
            }

            const favoriteButton = document.getElementById('btn-favorite-details');
            const codigoActual = String(data.id_recurso || id || '').trim();
            const origenActual = String(data.origen || origen || '').trim();
            const tituloActual = String(data.titulo || titulo || '').trim();
            const portadaActual = String(data.portada_url || '').trim();

            window.detailFavoriteContext = {
                codigo: codigoActual,
                origen: origenActual,
                titulo: tituloActual,
                portadaUrl: portadaActual,
            };

            if (favoriteButton && localStorage.getItem('user_uuid')) {
                const statusUrl = new URL('/nexuslib/gateway-service/public/index.php/api/user-library/check-status', window.location.origin);
                statusUrl.searchParams.set('user_uuid', localStorage.getItem('user_uuid') || '');
                statusUrl.searchParams.set('codigo', codigoActual);
                statusUrl.searchParams.set('origen', origenActual);

                fetch(statusUrl.toString())
                    .then((response) => response.json())
                    .then((statusData) => {
                        window.setFavoriteDetailsState(favoriteButton, statusData.is_saved === true);
                    })
                    .catch(() => {
                        window.setFavoriteDetailsState(favoriteButton, false);
                    });
            }

            document.getElementById('loading-spinner').classList.add('hidden');
            document.getElementById('content-container').classList.remove('hidden');
        })
        .catch(() => {
            document.getElementById('loading-spinner').classList.add('hidden');
            document.getElementById('content-container').classList.remove('hidden');
        });
})();
</script>