<main class="min-h-screen pt-24 pb-12 px-6">
    <div class="w-full md:max-w-2xl mx-auto">
        
        <div class="mb-8 border-b border-slate-700 pb-6">
            <h1 class="text-3xl md:text-4xl font-bold text-white flex items-center gap-3">
                <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Mis Libros Reservados
            </h1>
            <p class="text-gray-400 mt-2 text-lg">Gestiona tus solicitudes y retiros en la biblioteca física.</p>
        </div>

            <div class="bg-slate-800 bg-opacity-50 border border-slate-700 rounded-lg p-8 shadow-xl">
            <div class="grid grid-cols-1 gap-6">
                <div id="reserved-books-grid"></div>
            </div>
        </div>

    </div>
</main>

<!-- Cancel modal -->
<div id="cancel-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-60">
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 max-w-sm w-full mx-4 shadow-2xl">
        <h3 class="text-white text-lg font-semibold mb-2">Cancelar Reserva</h3>
        <p class="text-gray-400 mb-4">¿Estás seguro? Perderás tu lugar para este ejemplar físico.</p>

        <div class="flex gap-3 justify-end">
            <button type="button" onclick="closeCancelModal()" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded">No, mantener</button>
            <button type="button" onclick="confirmCancelAction()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">Sí, cancelar</button>
        </div>
    </div>
</div>

<script>
let cancelCodigo = null;
let cancelRegistro = null;

const escapeHtml = (value) => String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');

const escapeJs = (value) => String(value ?? '')
    .replace(/\\/g, '\\\\')
    .replace(/'/g, "\\'");

async function loadReservedBooks() {
    const grid = document.getElementById('reserved-books-grid');
    if (!grid) return;

    const userUuid = (localStorage.getItem('user_uuid') || '').trim();
    if (userUuid === '') {
        grid.innerHTML = '<div class="col-span-full rounded-lg border border-slate-700 bg-slate-800/60 p-6 text-center text-gray-300">Inicia sesión para ver tus reservas.</div>';
        return;
    }

    try {
        const response = await fetch(`/nexuslib/gateway-service/public/index.php/api/user-library/reserved?user_uuid=${encodeURIComponent(userUuid)}`);
        const payload = await response.json().catch(() => ({}));
        const books = Array.isArray(payload?.data) ? payload.data : [];

        if (!response.ok) {
            throw new Error(payload?.error || 'No se pudieron cargar las reservas');
        }

        if (books.length === 0) {
            grid.innerHTML = '<div class="col-span-full rounded-lg border border-slate-700 bg-slate-800/60 p-6 text-center text-gray-300">No tienes reservas activas.</div>';
            return;
        }

        grid.innerHTML = books.map((libro) => {
            const tituloRaw = String(libro?.titulo || 'Sin título').trim();
            const autorRaw = String(libro?.autor || 'Desconocido').trim();
            const bibliotecaRaw = String(libro?.biblioteca || '').trim();
            const estado = String(libro?.estado || 'Reservado').trim();
            const codigoRaw = String(libro?.codigo || '').trim();
            const registroRaw = Number(libro?.registro || 0);

            const codigoJs = escapeJs(codigoRaw);
            const title = escapeHtml(tituloRaw);
            const author = escapeHtml(autorRaw);
            const ubic = escapeHtml(bibliotecaRaw || 'No disponible');

            const coverHtml = `<img src="/nexuslib/frontend/public/images/logo-upt.png" alt="Portada UPT" class="w-20 h-28 rounded-lg flex-shrink-0 object-cover shadow-md">`;

            if (estado === 'Prestado') {
                return `
                <div class="bg-blue-900 bg-opacity-20 border border-blue-600 rounded-lg p-4 hover:border-blue-500 transition-all hover:shadow-lg hover:shadow-blue-500/10 group">
                    <div class="flex flex-col sm:flex-row gap-6">
                        ${coverHtml}
                        <div class="flex-1 flex flex-col justify-between">
                            <div>
                                <h4 class="text-xl text-white font-bold mb-1">${title}</h4>
                                <p class="text-gray-400 text-sm">${author}</p>
                            </div>
                            <div class="mt-4 bg-blue-900 bg-opacity-40 border border-blue-700 rounded-lg p-4 flex flex-wrap gap-x-6 gap-y-2">
                                <div>
                                    <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Estado</p>
                                    <p class="text-blue-400 font-semibold flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Prestado
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Ubicación</p>
                                    <p class="text-white font-medium">${ubic}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }

            // Default: Reservado
                return `
                <div class="bg-yellow-900 bg-opacity-20 border border-yellow-600 rounded-lg p-4 hover:border-yellow-500 transition-all hover:shadow-lg hover:shadow-yellow-500/10 group">
                    <div class="flex flex-col sm:flex-row gap-6">
                        ${coverHtml}
                        <div class="flex-1 flex flex-col justify-between">
                            <div>
                                <h4 class="text-xl text-white font-bold mb-1">${title}</h4>
                                <p class="text-gray-400 text-sm">${author}</p>
                            </div>
                            <div class="mt-4 bg-yellow-900 bg-opacity-40 border border-yellow-700 rounded-lg p-4 flex flex-wrap gap-x-6 gap-y-2">
                                <div>
                                    <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Estado</p>
                                    <p class="text-yellow-400 font-semibold flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Reservado
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Ubicación</p>
                                    <p class="text-white font-medium">${ubic}</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3 mt-4">
                                <button type="button" onclick="openCancelModal('${codigoJs}', ${registroRaw})" class="px-4 py-2 bg-transparent border border-red-500 text-red-400 hover:bg-red-500 hover:text-white rounded transition-colors text-sm font-semibold flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Cancelar Reserva
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    } catch (_error) {
        grid.innerHTML = '<div class="col-span-full rounded-lg border border-red-400/60 bg-red-500/10 p-6 text-center text-red-200">No se pudieron cargar tus reservas.</div>';
    }
}

window.openCancelModal = function(codigo, registro) {
    cancelCodigo = String(codigo || '');
    cancelRegistro = Number(registro || 0);

    const modal = document.getElementById('cancel-modal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
};

window.closeCancelModal = function() {
    const modal = document.getElementById('cancel-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
};

window.confirmCancelAction = async function() {
    const userUuid = (localStorage.getItem('user_uuid') || '').trim();
    if (userUuid === '' || !cancelRegistro) {
        closeCancelModal();
        return;
    }

    try {
        const response = await fetch('/nexuslib/gateway-service/public/index.php/api/user-library/cancel', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_uuid: userUuid, registro: cancelRegistro })
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok || data.success !== true) {
            throw new Error(data.error || data.message || 'No se pudo cancelar la reserva');
        }

        closeCancelModal();
        if (typeof window.showToast === 'function') {
            window.showToast('Reserva cancelada', 'success');
        } else {
            alert('Reserva cancelada');
        }

        await loadReservedBooks();
    } catch (_error) {
        closeCancelModal();
        if (typeof window.showToast === 'function') {
            window.showToast('No se pudo cancelar la reserva', 'error');
        } else {
            alert('No se pudo cancelar la reserva');
        }
    }
};

document.addEventListener('DOMContentLoaded', loadReservedBooks);
window.addEventListener('pageshow', function(event) { if (event.persisted) { loadReservedBooks(); } });
</script>