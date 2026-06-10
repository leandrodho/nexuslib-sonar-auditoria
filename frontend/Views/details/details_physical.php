<main class="min-h-screen pt-24 pb-12 px-6">
    <div id="loading-spinner" class="max-w-6xl mx-auto px-4 py-8 flex flex-col items-center justify-center gap-4 text-gray-300">
        <div class="h-12 w-12 animate-spin rounded-full border-4 border-slate-600 border-t-cyan-400"></div>
        <p class="text-sm md:text-base">Cargando detalles del libro...</p>
    </div>

    <div id="content-container" class="max-w-6xl mx-auto hidden">

        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-12">
            <h1 id="det-titulo" class="text-4xl md:text-5xl font-bold text-white flex-1">
                Desarrollo Web Moderno con React y Node.js
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
                <div class="w-full bg-slate-800 border-2 border-slate-700 rounded-lg flex flex-col items-center justify-center relative shadow-xl aspect-[3/4] overflow-hidden">
                    <img id="det-portada" src="/nexuslib/frontend/public/images/logo-upt.png" alt="Portada UPT" class="w-full h-full object-cover">
                </div>
            </div>

            <div class="lg:col-span-2 space-y-8">
                
                <div class="bg-slate-800 bg-opacity-50 border border-slate-700 rounded-lg p-8 shadow-md">
                    <h2 class="text-2xl font-bold text-white mb-8 flex items-center gap-3 border-b border-slate-700 pb-4">
                        <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                        Ficha Técnica del Catálogo Físico
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8">
                        <div>
                            <p class="text-cyan-400 text-sm font-medium uppercase tracking-wider mb-1">Autor</p>
                            <p id="det-autor" class="text-white font-semibold text-lg">Kyle Simpson</p>
                        </div>
                        <div>
                            <p class="text-cyan-400 text-sm font-medium uppercase tracking-wider mb-1">Código</p>
                            <p id="det-codigo" class="text-gray-300 font-mono text-lg">005.133/S55</p>
                        </div>
                        <div>
                            <p class="text-cyan-400 text-sm font-medium uppercase tracking-wider mb-1">Tipo</p>
                            <p id="det-tipo" class="text-white font-semibold text-lg">Libro</p>
                        </div>
                        <div>
                            <p class="text-cyan-400 text-sm font-medium uppercase tracking-wider mb-1">Procedencia</p>
                            <p id="det-procedencia" class="text-white font-semibold text-lg">Compra</p>
                        </div>
                        <div>
                            <p class="text-cyan-400 text-sm font-medium uppercase tracking-wider mb-1">Fecha de Registro</p>
                            <p id="det-fecha" class="text-gray-300 font-mono text-lg">2023-08-12 14:30:00</p>
                        </div>
                        <div class="sm:col-span-2 pt-2">
                            <p class="text-cyan-400 text-sm font-medium uppercase tracking-wider mb-2">Origen / Biblioteca</p>
                            <span id="det-origen" class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold bg-blue-900 text-blue-200 border border-blue-700 shadow-sm">
                                Inventario UPT
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-900 to-green-950 border-2 border-green-600 rounded-lg p-8 shadow-lg shadow-green-600/30">
                    <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.5m0 0H9m0 0H3.5m0 0H1"></path>
                        </svg>
                        Detalles de Disponibilidad
                    </h2>

                    <div class="space-y-4">
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-green-800 bg-opacity-40 border border-green-600 rounded-lg p-4">
                                <p class="text-gray-400 text-sm">Ubicación de Biblioteca</p>
                                <p id="det-biblioteca" class="text-white font-semibold">FAING</p>
                            </div>
                            
                            <div class="bg-green-800 bg-opacity-40 border border-green-600 rounded-lg p-4">
                                <p class="text-gray-400 text-sm">Registro</p>
                                <p id="det-registro" class="text-white font-semibold">14092</p>
                            </div>

                            <div class="bg-green-800 bg-opacity-40 border border-green-600 rounded-lg p-4">
                                <p class="text-gray-400 text-sm">Estado</p>
                                <p id="det-estado" class="text-white font-semibold">Disponible</p>
                            </div>
                            
                            <div class="bg-green-800 bg-opacity-40 border border-green-600 rounded-lg p-4">
                                <p class="text-gray-400 text-sm">Copias</p>
                                <p id="det-copias" class="text-white font-semibold">Cargando...</p>
                            </div>
                        </div>

                        <button onclick="reservarLibroUPT()" class="w-full mt-6 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-4 px-6 rounded-lg transition-all transform hover:scale-105 shadow-lg text-lg flex items-center justify-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Reservar Libro Físico
                        </button>
                    </div>
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

window.reservarLibroUPT = async function() {
    const userUuid = localStorage.getItem('user_uuid');
    if (!userUuid) {
        if (typeof window.showToast === 'function') {
            window.showToast('Debes iniciar sesión', 'error');
        } else {
            alert('Debes iniciar sesión');
        }
        return;
    }

    const codigo = (document.getElementById('det-codigo')?.textContent || '').trim();
    if (codigo === '' || codigo === 'No disponible') {
        if (typeof window.showToast === 'function') {
            window.showToast('No se pudo identificar el código del libro', 'error');
        } else {
            alert('No se pudo identificar el código del libro');
        }
        return;
    }

    try {
        const response = await fetch('/nexuslib/gateway-service/public/index.php/api/user-library/reserve', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_uuid: userUuid,
                codigo: codigo
            })
        });

        let data = {};
        try {
            data = await response.json();
        } catch (_error) {
            data = {};
        }

        if (response.ok) {
            if (typeof window.showToast === 'function') {
                window.showToast('¡Libro reservado con éxito!', 'success');
            } else {
                alert('¡Libro reservado con éxito!');
            }

            // Actualizar el DOM dinámicamente: decrementar copias
            const copiasEl = document.getElementById('det-copias');
            const estadoEl = document.getElementById('det-estado');

            if (copiasEl) {
                const raw = (copiasEl.textContent || '').trim();
                const n = Number.parseInt(raw, 10);
                if (!Number.isNaN(n)) {
                    const nueva = Math.max(0, n - 1);
                    copiasEl.textContent = String(nueva);
                    copiasEl.classList.remove('text-white', 'text-red-400', 'text-emerald-400');
                    if (nueva === 0) {
                        copiasEl.classList.add('text-red-400');
                        if (estadoEl) {
                            estadoEl.textContent = 'Agotado';
                            estadoEl.classList.remove('text-emerald-400');
                            estadoEl.classList.add('text-red-400');
                        }
                    } else {
                        copiasEl.classList.add('text-emerald-400');
                    }
                }
            }

            return;
        }

        const errorMessage = data.error || 'No se pudo reservar el libro.';
        if (typeof window.showToast === 'function') {
            window.showToast(errorMessage, 'error');
        } else {
            alert(errorMessage);
        }
    } catch (_error) {
        if (typeof window.showToast === 'function') {
            window.showToast('Error de conexión al reservar el libro', 'error');
        } else {
            alert('Error de conexión al reservar el libro');
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
            const codigoEl = document.getElementById('det-codigo');
            const autorEl = document.getElementById('det-autor');
            const tipoEl = document.getElementById('det-tipo');
            const procedenciaEl = document.getElementById('det-procedencia');
            const fechaEl = document.getElementById('det-fecha');
            const origenEl = document.getElementById('det-origen');
            const copiasEl = document.getElementById('det-copias');
            const bibliotecaEl = document.getElementById('det-biblioteca');
            const registroEl = document.getElementById('det-registro');
            const estadoEl = document.getElementById('det-estado');

            if (tituloEl) {
                tituloEl.textContent = data.titulo || 'Sin título';
            }
            if (codigoEl) {
                codigoEl.textContent = extra.codigo || 'No disponible';
            }
            if (autorEl) {
                autorEl.textContent = data.autor || 'Desconocido';
            }
            if (tipoEl) {
                tipoEl.textContent = extra.tipo || 'No disponible';
            }
            if (procedenciaEl) {
                procedenciaEl.textContent = extra.procedencia || 'No disponible';
            }
            if (fechaEl) {
                fechaEl.textContent = extra.fecha || 'No disponible';
            }
            if (origenEl) {
                origenEl.textContent = data.origen || origen;
            }
            if (copiasEl) {
                const copias = extra.copias_disponibles;
                copiasEl.textContent = extra.copias_disponibles !== undefined ? String(extra.copias_disponibles) : 'No disponible';
                copiasEl.classList.remove('text-white', 'text-red-400', 'text-emerald-400');

                if (typeof copias === 'number' && copias === 0) {
                    copiasEl.classList.add('text-red-400');
                } else if (typeof copias === 'number' && copias > 0) {
                    copiasEl.classList.add('text-emerald-400');
                } else {
                    copiasEl.classList.add('text-white');
                }
            }
            if (bibliotecaEl) {
                bibliotecaEl.textContent = extra.biblioteca || 'No disponible';
            }
            if (registroEl) {
                registroEl.textContent = data.id_recurso || id;
            }
            if (estadoEl) {
                estadoEl.textContent = extra.estado || 'No disponible';
            }

            const favoriteButton = document.getElementById('btn-favorite-details');
            const codigoFavorite = String(data.id_recurso || id || extra.codigo || '').trim();
            const origenFavorite = String(data.origen || origen || '').trim();
            const tituloFavorite = String(data.titulo || titulo || '').trim();
            const portadaFavorite = String(data.portada_url || '').trim();

            window.detailFavoriteContext = {
                codigo: codigoFavorite,
                origen: origenFavorite,
                titulo: tituloFavorite,
                portadaUrl: portadaFavorite,
            };

            if (favoriteButton && localStorage.getItem('user_uuid')) {
                const statusUrl = new URL('/nexuslib/gateway-service/public/index.php/api/user-library/check-status', window.location.origin);
                statusUrl.searchParams.set('user_uuid', localStorage.getItem('user_uuid') || '');
                statusUrl.searchParams.set('codigo', codigoFavorite);
                statusUrl.searchParams.set('origen', origenFavorite);

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