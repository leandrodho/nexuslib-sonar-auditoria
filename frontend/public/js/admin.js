// --- USERS will be loaded from backend via gateway-service ---


// Inventory data will be retrieved from the backend via gateway

// saved/reserved lists will be loaded from backend via gateway

// --- RENDERIZADO DE TABLAS SEGÚN LA PÁGINA ---

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const view = urlParams.get('view') || '';

    // Dashboard
    if(view === 'admin/index') {
        loadDashboardStats();
    }

    // Tabla Usuarios
    if(view === 'admin/users') loadUsers();

    // Tabla Inventario Agrupado
    if(view === 'admin/inventory') loadInventory();

    // Tabla Registros (Detalle)
    if(view === 'admin/inventory_records') {
        const codigo = urlParams.get('codigo');
        document.getElementById('records-title').innerText = `Ejemplares del Código: ${codigo}`;
        renderRecords(codigo);
    }

    // Tablas de Lectura
    if(view === 'admin/saved_books') loadSavedBooks();
    if(view === 'admin/reserved_books') loadReservedBooks();
});

// --- FUNCIONES RENDER ---

function formatDateTime(value) {
    if (!value) return '';
    // Accept timestamps or ISO strings
    const d = new Date(value);
    if (isNaN(d.getTime())) return value;
    const dd = String(d.getDate()).padStart(2, '0');
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const yyyy = d.getFullYear();
    const hh = String(d.getHours()).padStart(2, '0');
    const min = String(d.getMinutes()).padStart(2, '0');
    return `${dd}/${mm}/${yyyy} ${hh}:${min}`;
}

async function renderUsers(users) {
    const tbody = document.getElementById('users-tbody');
    if(!tbody) return;
    if (!Array.isArray(users)) users = [];
    tbody.innerHTML = users.map(user => {
        const id = user.id_user ?? user.id ?? '';
        const name = user.name ?? '';
        const email = user.email ?? '';
        const role = (user.role ?? 'user').toLowerCase();
        const status = user.status ?? '';

        return `
        <tr class="border-b border-slate-700 hover:bg-slate-800/50 transition">
            <td class="p-4 font-mono text-xs text-slate-400">#${id}</td>
            <td class="p-4 font-medium text-white">${escapeHtml(name)}</td>
            <td class="p-4">${escapeHtml(email)}</td>
            <td class="p-4"><span class="px-2 py-1 rounded-full text-xs font-bold ${role==='admin' ? 'bg-cyan-500/20 text-cyan-400' : 'bg-slate-600 text-slate-300'}">${role.toUpperCase()}</span></td>
            <td class="p-4"><span class="px-2 py-1 rounded-full text-xs ${status==='active' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'}">${escapeHtml(status)}</span></td>
            <td class="p-4 text-right">
                <button onclick="confirmDeleteUser(${id})" class="text-red-400 hover:text-red-300 p-2 hover:bg-red-500/10 rounded-lg transition" title="Eliminar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </td>
        </tr>
    `; }).join('');
}

async function loadUsers() {
    const tbody = document.getElementById('users-tbody');
    if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="p-6 text-center text-slate-400">Cargando usuarios...</td></tr>';
    try {
        const res = await fetch('/nexuslib/gateway-service/public/index.php/api/admin/users', {
            method: 'GET',
            credentials: 'include'
        });

        if (res.status === 401 || res.status === 403) {
            const msg = res.status === 403 ? 'Acceso denegado. Requiere permisos de administrador.' : 'No autenticado.';
            if (typeof window.showToast === 'function') window.showToast(msg, 'error'); else alert(msg);
            return;
        }

        const data = await res.json();
        if (res.ok && data && data.success) {
            await renderUsers(data.data || []);
        } else {
            const err = (data && data.error) ? data.error : 'Error al cargar usuarios';
            if (typeof window.showToast === 'function') window.showToast(err, 'error'); else alert(err);
            if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="p-6 text-center text-slate-400">No se pudieron cargar los usuarios.</td></tr>';
        }
    } catch (e) {
        console.error('loadUsers error', e);
        if (typeof window.showToast === 'function') window.showToast('Error de red al cargar usuarios', 'error'); else alert('Error de red al cargar usuarios');
        if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="p-6 text-center text-slate-400">Error de red.</td></tr>';
    }
}

function renderInventory(items) {
    const tbody = document.getElementById('inventory-tbody');
    if(!tbody) return;
    if (!Array.isArray(items) || items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="p-6 text-center text-slate-400">No hay títulos en el inventario.</td></tr>';
        return;
    }

    tbody.innerHTML = items.map(item => {
        const codigo = item.codigo ?? '';
        const titulo = item.titulo ?? '';
        const autor = item.autor ?? '';
        const total = item.total ?? 0;

        return `
        <tr class="border-b border-slate-700 hover:bg-slate-800/50 transition">
            <td class="p-4 font-mono text-cyan-400 font-bold">${escapeHtml(codigo)}</td>
            <td class="p-4 text-white font-medium">${escapeHtml(titulo)}</td>
            <td class="p-4 text-slate-400">${escapeHtml(autor)}</td>
            <td class="p-4"><span class="bg-slate-700 px-3 py-1 rounded-full text-sm">${total} físicos</span></td>
            <td class="p-4 text-right">
                <a href="?view=admin/inventory_records&codigo=${encodeURIComponent(codigo)}" class="inline-block px-4 py-2 bg-slate-700 hover:bg-cyan-600 hover:text-white text-cyan-400 text-sm font-semibold rounded-lg transition">Ver registros</a>
            </td>
        </tr>
    `; }).join('');
}

async function loadInventory() {
    const tbody = document.getElementById('inventory-tbody');
    if (tbody) tbody.innerHTML = '<tr><td colspan="5" class="p-6 text-center text-slate-400">Cargando inventario...</td></tr>';
    try {
        const res = await fetch('/nexuslib/gateway-service/public/index.php/api/admin/inventory/grouped', {
            method: 'GET',
            credentials: 'include'
        });

        if (res.status === 401 || res.status === 403) {
            const msg = res.status === 403 ? 'Acceso denegado. Requiere permisos de administrador.' : 'No autenticado.';
            if (typeof window.showToast === 'function') window.showToast(msg, 'error'); else alert(msg);
            return;
        }

        const data = await res.json();
        if (res.ok && data && data.success) {
            renderInventory(data.data || []);
        } else {
            const err = (data && data.error) ? data.error : 'Error al cargar inventario';
            if (typeof window.showToast === 'function') window.showToast(err, 'error'); else alert(err);
            if (tbody) tbody.innerHTML = '<tr><td colspan="5" class="p-6 text-center text-slate-400">No se pudo cargar el inventario.</td></tr>';
        }
    } catch (e) {
        console.error('loadInventory error', e);
        if (typeof window.showToast === 'function') window.showToast('Error de red al cargar inventario', 'error'); else alert('Error de red al cargar inventario');
        if (tbody) tbody.innerHTML = '<tr><td colspan="5" class="p-6 text-center text-slate-400">Error de red.</td></tr>';
    }
}

async function renderRecords(codigo) {
    const tbody = document.getElementById('records-tbody');
    if(!tbody) return;

    tbody.innerHTML = '<tr><td colspan="4" class="p-6 text-center text-slate-400">Cargando ejemplares...</td></tr>';

    try {
        const res = await fetch(`/nexuslib/gateway-service/public/index.php/api/admin/inventory/records?codigo=${encodeURIComponent(codigo)}`, {
            method: 'GET',
            credentials: 'include'
        });

        if (res.status === 401 || res.status === 403) {
            const msg = res.status === 403 ? 'Acceso denegado. Requiere permisos de administrador.' : 'No autenticado.';
            if (typeof window.showToast === 'function') window.showToast(msg, 'error'); else alert(msg);
            return;
        }

        const data = await res.json();
        if (!res.ok || !data || !data.success) {
            const err = (data && data.error) ? data.error : 'Error al cargar ejemplares';
            if (typeof window.showToast === 'function') window.showToast(err, 'error'); else alert(err);
            tbody.innerHTML = `<tr><td colspan="4" class="p-6 text-center text-slate-400">No se encontraron ejemplares físicos para este código.</td></tr>`;
            return;
        }

        const records = data.data || [];
        if (records.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="p-6 text-center text-slate-400">No se encontraron ejemplares físicos para este código.</td></tr>`;
            return;
        }

        tbody.innerHTML = records.map(r => {
            const registro = r.registro ?? r.registro ?? '';
            const biblioteca = r.biblioteca ?? '';
            const estado = r.estado ?? '';

            let colorClass = 'text-green-400';
            if (estado === 'Reservado') colorClass = 'text-purple-400';
            if (estado === 'Prestado') colorClass = 'text-orange-400';

            const options = `
                <option value="Disponible" ${estado === 'Disponible' ? 'selected' : ''}>Disponible</option>
                ${estado === 'Reservado' ? `<option value="Reservado" selected disabled>Reservado (Solo sistema)</option>` : ''}
                <option value="Prestado" ${estado === 'Prestado' ? 'selected' : ''}>Prestado</option>
            `;

            return `
            <tr class="border-b border-slate-700 hover:bg-slate-800/50 transition">
                <td class="p-4 font-mono text-white font-bold">${registro}</td>
                <td class="p-4 text-slate-300">${escapeHtml(biblioteca)}</td>
                <td class="p-4 font-semibold ${colorClass}">${escapeHtml(estado)}</td>
                <td class="p-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <select id="select-${registro}" class="bg-slate-900 border border-slate-600 text-sm rounded-lg px-3 py-2 text-slate-200 focus:outline-none focus:border-cyan-500">
                            ${options}
                        </select>
                        <button onclick="changeRecordState(${registro}, '${encodeURIComponent(codigo)}')" class="bg-cyan-600 hover:bg-cyan-500 text-white px-3 py-2 rounded-lg text-sm font-medium transition">
                            Guardar
                        </button>
                    </div>
                </td>
            </tr>
            `;
        }).join('');

    } catch (e) {
        console.error('renderRecords error', e);
        if (typeof window.showToast === 'function') window.showToast('Error de red al cargar ejemplares', 'error'); else alert('Error de red al cargar ejemplares');
        tbody.innerHTML = `<tr><td colspan="4" class="p-6 text-center text-slate-400">Error de red.</td></tr>`;
    }
}

function renderSaved(items) {
    const tbody = document.getElementById('saved-tbody');
    if(!tbody) return;
    if (!Array.isArray(items) || items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="p-6 text-center text-slate-400">No hay libros guardados.</td></tr>';
        return;
    }

    tbody.innerHTML = items.map(s => {
        const fecha = formatDateTime(s.fecha_guardado ?? s.fecha ?? '');
        return `
        <tr class="border-b border-slate-700">
            <td class="p-4 text-slate-400">${escapeHtml(s.id)}</td>
            <td class="p-4 font-mono text-xs">${escapeHtml(s.user_uuid)}</td>
            <td class="p-4 text-sm text-slate-400">${escapeHtml(s.user_email ?? '')}</td>
            <td class="p-4 text-cyan-400">${escapeHtml(s.codigo)}</td>
            <td class="p-4">${escapeHtml(s.origen)}</td>
            <td class="p-4 text-white">${escapeHtml(s.titulo ?? '')}</td>
            <td class="p-4 text-slate-400">${escapeHtml(fecha)}</td>
        </tr>
    `; }).join('');
}

function renderReserved(items) {
    const tbody = document.getElementById('reserved-tbody');
    if(!tbody) return;
    if (!Array.isArray(items) || items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="p-6 text-center text-slate-400">No hay reservas.</td></tr>';
        return;
    }

    tbody.innerHTML = items.map(r => {
        const fecha = formatDateTime(r.fecha_reserva ?? r.fecha ?? '');
        return `
        <tr class="border-b border-slate-700">
            <td class="p-4 text-slate-400">${escapeHtml(r.id)}</td>
            <td class="p-4 font-mono text-xs">${escapeHtml(r.user_uuid)}</td>
            <td class="p-4 text-sm text-slate-400">${escapeHtml(r.user_email ?? '')}</td>
            <td class="p-4 text-cyan-400">${escapeHtml(r.codigo)}</td>
            <td class="p-4 font-bold text-white">${escapeHtml(r.registro ?? '')}</td>
            <td class="p-4 text-purple-400">${escapeHtml(r.estado ?? '')}</td>
            <td class="p-4 text-slate-400">${escapeHtml(fecha)}</td>
        </tr>
    `; }).join('');
}

// --- ACCIONES (MODAL Y LÓGICA MOCK) ---

let currentAction = null;

function showModal(title, desc, confirmCallback, isDanger = false) {
    document.getElementById('modal-title').innerText = title;
    document.getElementById('modal-desc').innerText = desc;
    const btn = document.getElementById('modal-confirm-btn');
    
    if(isDanger) {
        btn.className = "px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition font-bold";
    } else {
        btn.className = "px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white rounded-lg transition font-bold";
    }

    currentAction = confirmCallback;
    document.getElementById('admin-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('admin-modal').classList.add('hidden');
    currentAction = null;
}

document.getElementById('modal-confirm-btn').addEventListener('click', () => {
    if(currentAction) currentAction();
    closeModal();
});

// Borrar Usuario
function confirmDeleteUser(id) {
    showModal("Eliminar Usuario", `¿Estás seguro de que deseas eliminar permanentemente al usuario #${id}? Esta acción no se puede deshacer.`, async () => {
        try {
            const res = await fetch(`/nexuslib/gateway-service/public/index.php/api/admin/users/${id}`, {
                method: 'DELETE',
                credentials: 'include'
            });

            if (res.status === 401 || res.status === 403) {
                const msg = res.status === 403 ? 'Acceso denegado. Requiere permisos de administrador.' : 'No autenticado.';
                if (typeof window.showToast === 'function') window.showToast(msg, 'error'); else alert(msg);
                return;
            }

            if (res.ok) {
                if (typeof window.showToast === 'function') window.showToast('Usuario eliminado correctamente', 'success'); else alert('Usuario eliminado correctamente');
                await loadUsers();
            } else {
                const data = await res.json().catch(() => ({}));
                const err = data.error || 'Error al eliminar usuario';
                if (typeof window.showToast === 'function') window.showToast(err, 'error'); else alert(err);
            }
        } catch (e) {
            console.error('delete user error', e);
            if (typeof window.showToast === 'function') window.showToast('Error de red al eliminar usuario', 'error'); else alert('Error de red al eliminar usuario');
        }
    }, true);
}

// Cambiar estado de inventario
async function changeRecordState(registro, encodedCodigo) {
    const select = document.getElementById(`select-${registro}`);
    if (!select) return;
    const newState = select.value;
    const codigo = decodeURIComponent(encodedCodigo || '');

    showModal("Actualizar Estado", `¿Confirmas cambiar el registro ${registro} a "${newState}"?`, async () => {
        try {
            const payload = { registro: registro, estado: newState };
            const res = await fetch('/nexuslib/gateway-service/public/index.php/api/admin/inventory/state', {
                method: 'PUT',
                credentials: 'include',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            if (res.status === 401 || res.status === 403) {
                const msg = res.status === 403 ? 'Acceso denegado. Requiere permisos de administrador.' : 'No autenticado.';
                if (typeof window.showToast === 'function') window.showToast(msg, 'error'); else alert(msg);
                return;
            }

            if (res.ok) {
                if (typeof window.showToast === 'function') window.showToast('Estado actualizado', 'success'); else alert('Estado actualizado');
                // recargar registros del código
                await renderRecords(codigo);
            } else {
                const data = await res.json().catch(() => ({}));
                const err = data.error || 'Error al actualizar estado';
                if (typeof window.showToast === 'function') window.showToast(err, 'error'); else alert(err);
            }
        } catch (e) {
            console.error('changeRecordState error', e);
            if (typeof window.showToast === 'function') window.showToast('Error de red al actualizar estado', 'error'); else alert('Error de red al actualizar estado');
        }
    }, false);
}

async function loadSavedBooks() {
    const tbody = document.getElementById('saved-tbody');
    if (tbody) tbody.innerHTML = '<tr><td colspan="5" class="p-6 text-center text-slate-400">Cargando libros guardados...</td></tr>';
    try {
        const res = await fetch('/nexuslib/gateway-service/public/index.php/api/admin/saved', {
            method: 'GET',
            credentials: 'include'
        });

        if (res.status === 401 || res.status === 403) {
            const msg = res.status === 403 ? 'Acceso denegado. Requiere permisos de administrador.' : 'No autenticado.';
            if (typeof window.showToast === 'function') window.showToast(msg, 'error'); else alert(msg);
            return;
        }

        const data = await res.json();
        if (res.ok && data && data.success) {
            renderSaved(data.data || []);
        } else {
            const err = (data && data.error) ? data.error : 'Error al cargar libros guardados';
            if (typeof window.showToast === 'function') window.showToast(err, 'error'); else alert(err);
            if (tbody) tbody.innerHTML = '<tr><td colspan="5" class="p-6 text-center text-slate-400">No se pudieron cargar los libros guardados.</td></tr>';
        }
    } catch (e) {
        console.error('loadSavedBooks error', e);
        if (typeof window.showToast === 'function') window.showToast('Error de red al cargar libros guardados', 'error'); else alert('Error de red al cargar libros guardados');
        if (tbody) tbody.innerHTML = '<tr><td colspan="5" class="p-6 text-center text-slate-400">Error de red.</td></tr>';
    }
}

async function loadReservedBooks() {
    const tbody = document.getElementById('reserved-tbody');
    if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="p-6 text-center text-slate-400">Cargando reservas...</td></tr>';
    try {
        const res = await fetch('/nexuslib/gateway-service/public/index.php/api/admin/reserved', {
            method: 'GET',
            credentials: 'include'
        });

        if (res.status === 401 || res.status === 403) {
            const msg = res.status === 403 ? 'Acceso denegado. Requiere permisos de administrador.' : 'No autenticado.';
            if (typeof window.showToast === 'function') window.showToast(msg, 'error'); else alert(msg);
            return;
        }

        const data = await res.json();
        if (res.ok && data && data.success) {
            renderReserved(data.data || []);
        } else {
            const err = (data && data.error) ? data.error : 'Error al cargar reservas';
            if (typeof window.showToast === 'function') window.showToast(err, 'error'); else alert(err);
            if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="p-6 text-center text-slate-400">No se pudieron cargar las reservas.</td></tr>';
        }
    } catch (e) {
        console.error('loadReservedBooks error', e);
        if (typeof window.showToast === 'function') window.showToast('Error de red al cargar reservas', 'error'); else alert('Error de red al cargar reservas');
        if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="p-6 text-center text-slate-400">Error de red.</td></tr>';
    }
}

async function loadDashboardStats() {
    try {
        const usersRes = await fetch('/nexuslib/gateway-service/public/index.php/api/admin/users', { credentials: 'include' });
        const usersData = await usersRes.json();
        if (usersData.success) document.getElementById('dash-users').innerText = usersData.data.length;

        const invRes = await fetch('/nexuslib/gateway-service/public/index.php/api/admin/inventory/grouped', { credentials: 'include' });
        const invData = await invRes.json();
        if (invData.success) document.getElementById('dash-titles').innerText = invData.data.length;

        const resRes = await fetch('/nexuslib/gateway-service/public/index.php/api/admin/reserved', { credentials: 'include' });
        const resData = await resRes.json();
        if (resData.success) document.getElementById('dash-reserves').innerText = resData.data.length;
    } catch (e) {
        console.error('Error cargando stats del dashboard:', e);
    }
}