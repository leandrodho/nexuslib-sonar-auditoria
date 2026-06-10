<main class="min-h-screen pt-24 pb-12 px-6">
    <div class="max-w-4xl mx-auto">
        
        <div class="mb-8 border-b border-slate-700 pb-6">
            <h1 class="text-3xl md:text-4xl font-bold text-white flex items-center gap-3">
                <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Ajustes de Perfil
            </h1>
            <p class="text-gray-400 mt-2 text-lg">Actualiza tu información personal y credenciales de acceso.</p>
        </div>

        <div class="space-y-8">
            
            <div class="bg-slate-800 bg-opacity-50 border border-slate-700 rounded-xl p-8 shadow-xl">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Datos Personales
                </h2>
                
                <form id="personal-form" onsubmit="updatePersonalInfo(event)">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-gray-300 font-semibold mb-2">Nombre Completo</label>
                            <input 
                                type="text" 
                                id="profile-name"
                                value="Nombre Apellido"
                                required
                                class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500 focus:ring-opacity-50 transition-all"
                            >
                        </div>
                        <div>
                            <label class="block text-gray-300 font-semibold mb-2">Correo Electrónico</label>
                            <input 
                                type="email" 
                                id="profile-email"
                                value="correo@ejemplo.com"
                                required
                                class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500 focus:ring-opacity-50 transition-all"
                            >
                        </div>
                    </div>
                    <div id="personal-msg" class="hidden mb-4"></div>
                    <div class="flex justify-end">
                        <button 
                            type="submit"
                            class="bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-600 hover:to-cyan-700 text-white font-bold py-2 px-6 rounded-lg transition-all shadow-lg flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-slate-800 bg-opacity-50 border border-slate-700 rounded-xl p-8 shadow-xl">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Seguridad
                </h2>
                
                <form id="password-form" onsubmit="updatePassword(event)">
                    <div class="space-y-6 mb-6">
                        <div>
                            <label class="block text-gray-300 font-semibold mb-2">Contraseña Actual</label>
                            <input 
                                type="password" 
                                id="current-password"
                                placeholder="••••••••"
                                required
                                class="w-full md:w-1/2 px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500 focus:ring-opacity-50 transition-all"
                            >
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-700">
                            <div>
                                <label class="block text-gray-300 font-semibold mb-2">Nueva Contraseña</label>
                                <input 
                                    type="password" 
                                    id="new-password"
                                    placeholder="••••••••"
                                    required
                                    class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500 focus:ring-opacity-50 transition-all"
                                >
                            </div>
                            <div>
                                <label class="block text-gray-300 font-semibold mb-2">Confirmar Nueva Contraseña</label>
                                <input 
                                    type="password" 
                                    id="confirm-new-password"
                                    placeholder="••••••••"
                                    required
                                    class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500 focus:ring-opacity-50 transition-all"
                                >
                            </div>
                        </div>
                    </div>
                    <div id="password-msg" class="hidden mb-4"></div>
                    <div class="flex justify-end">
                        <button 
                            type="submit"
                            class="bg-slate-600 hover:bg-slate-500 text-white font-bold py-2 px-6 rounded-lg transition-colors border border-slate-500 hover:border-slate-400 shadow flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            Actualizar Contraseña
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</main>
<script>
// Profile page JS: load profile, update personal info and password
let __originalProfile = null;

function setMessage(elId, text, type = 'success') {
    const el = document.getElementById(elId);
    if (!el) return;
    el.textContent = text;
    el.classList.remove('hidden');
    el.classList.remove('bg-green-500/20','text-green-400','border','border-green-500/25','bg-red-500/20','text-red-400','border-red-500/25','p-3','rounded');
    if (type === 'success') {
        el.classList.add('bg-green-500/20','text-green-400','border','border-green-500/25','p-3','rounded');
    } else {
        el.classList.add('bg-red-500/20','text-red-400','border','border-red-500/25','p-3','rounded');
    }
    // Auto-hide after 6s
    setTimeout(() => { try { el.classList.add('hidden'); } catch(e){} }, 6000);
}

async function loadProfile() {
    try {
        const res = await fetch('/nexuslib/gateway-service/public/index.php/api/auth/profile', { credentials: 'include' });
        if (res.status === 401) {
            window.location.href = 'index.php?view=auth/login';
            return;
        }
        const data = await res.json();
        if (data && data.success && data.user) {
            const user = data.user;
            document.getElementById('profile-name').value = user.name || '';
            document.getElementById('profile-email').value = user.email || '';
            __originalProfile = { name: user.name || '', email: user.email || '' };
        }
    } catch (e) {
        console.error('loadProfile error', e);
    }
}

async function updatePersonalInfo(event) {
    event.preventDefault();
    const nameEl = document.getElementById('profile-name');
    const emailEl = document.getElementById('profile-email');
    if (!nameEl || !emailEl) return;
    const name = nameEl.value.trim();
    const email = emailEl.value.trim();

    if (__originalProfile && __originalProfile.name === name && __originalProfile.email === email) {
        setMessage('personal-msg', 'Sin cambios', 'success');
        return;
    }

    try {
        const res = await fetch('/nexuslib/gateway-service/public/index.php/api/auth/profile', {
            method: 'PUT',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name: name, email: email })
        });

        if (res.status === 401) {
            window.location.href = 'index.php?view=auth/login';
            return;
        }

        if (res.status === 409) {
            setMessage('personal-msg', 'El correo ya está en uso.', 'error');
            return;
        }

        const data = await res.json();
        if (res.ok && data.success) {
            __originalProfile = { name: data.user.name, email: data.user.email };
            setMessage('personal-msg', data.changed ? 'Perfil actualizado' : 'Sin cambios', 'success');
        } else {
            setMessage('personal-msg', data.error || 'Error al actualizar perfil', 'error');
        }
    } catch (e) {
        console.error(e);
        setMessage('personal-msg', 'Error de red. Intenta nuevamente.', 'error');
    }
}

async function updatePassword(event) {
    event.preventDefault();
    const current = document.getElementById('current-password')?.value || '';
    const nw = document.getElementById('new-password')?.value || '';
    const confirm = document.getElementById('confirm-new-password')?.value || '';

    if (!current || !nw || !confirm) {
        setMessage('password-msg', 'Completa todos los campos.', 'error');
        return;
    }
    if (nw !== confirm) {
        setMessage('password-msg', 'Las nuevas contraseñas no coinciden.', 'error');
        return;
    }

    try {
        const res = await fetch('/nexuslib/gateway-service/public/index.php/api/auth/change-password', {
            method: 'PUT',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ current_password: current, new_password: nw, confirm_password: confirm })
        });

        if (res.status === 401) {
            window.location.href = 'index.php?view=auth/login';
            return;
        }

        if (res.status === 403) {
            setMessage('password-msg', 'Contraseña actual incorrecta.', 'error');
            return;
        }

        const data = await res.json();
        if (res.ok && data.success) {
            setMessage('password-msg', 'Contraseña actualizada.', 'success');
            // clear password fields
            document.getElementById('current-password').value = '';
            document.getElementById('new-password').value = '';
            document.getElementById('confirm-new-password').value = '';
        } else {
            // Map known backend error tokens to friendly messages
            if (data && data.error === 'current_password_incorrect') {
                setMessage('password-msg', 'Contraseña actual incorrecta.', 'error');
            } else {
                setMessage('password-msg', data.error || 'Error al actualizar la contraseña', 'error');
            }
        }
    } catch (e) {
        console.error(e);
        setMessage('password-msg', 'Error de red. Intenta nuevamente.', 'error');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    loadProfile();
});
</script>