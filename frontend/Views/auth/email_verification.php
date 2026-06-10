<main class="min-h-screen pt-24 pb-12 px-6 flex items-center justify-center">
    <div class="max-w-md w-full bg-slate-800 bg-opacity-50 backdrop-blur-md border border-slate-700 rounded-2xl p-10 shadow-2xl text-center transform transition-all">

        <!-- Estado: Cargando -->
        <div id="ev-loading" class="ev-state">
            <div class="mx-auto w-16 h-16 border-4 border-t-cyan-400 border-slate-700 rounded-full animate-spin mb-6"></div>
            <h2 class="text-2xl font-semibold text-white mb-2">Verificando tu cuenta...</h2>
            <p class="text-slate-400">Un momento, estamos confirmando tu correo electrónico.</p>
        </div>

        <!-- Estado: Éxito -->
        <div id="ev-success" class="ev-state hidden">
            <div class="mx-auto w-24 h-24 bg-gradient-to-br from-cyan-400 to-blue-600 rounded-full flex items-center justify-center mb-8 shadow-lg shadow-cyan-500/30">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-white mb-4">¡Cuenta Verificada!</h1>
            <p class="text-gray-300 mb-6 leading-relaxed">
                Tu dirección de correo electrónico ha sido confirmada exitosamente. Ya puedes acceder a todas las funciones y recursos de <span class="text-cyan-400 font-bold">NexusLib</span>.
            </p>

            <a href="index.php?view=auth/login" class="inline-flex w-full items-center justify-center gap-2 bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-600 hover:to-cyan-700 text-white font-bold py-3.5 px-6 rounded-xl transition-all transform hover:scale-[1.02] shadow-lg text-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                Iniciar Sesión
            </a>
        </div>

        <!-- Estado: Error -->
        <div id="ev-error" class="ev-state hidden">
            <div class="mx-auto w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-semibold text-white mb-2">Enlace inválido o expirado</h2>
            <p class="text-slate-400 mb-6">No pudimos verificar tu cuenta con este enlace. Intenta solicitar un reenvío desde la página de inicio de sesión.</p>
            <a href="index.php?view=auth/login" class="inline-flex w-full items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold py-3.5 px-6 rounded-xl transition-all shadow-lg text-lg">Volver al inicio de sesión</a>
        </div>

    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const loading = document.getElementById('ev-loading');
    const success = document.getElementById('ev-success');
    const error = document.getElementById('ev-error');

    function showState(node) {
        [loading, success, error].forEach(n => n.classList.add('hidden'));
        if (node) node.classList.remove('hidden');
    }

    const params = new URLSearchParams(window.location.search);
    const token = params.get('token');

    if (!token) {
        showState(error);
        return;
    }

    showState(loading);

    fetch('/nexuslib/gateway-service/public/index.php/api/auth/verify?token=' + encodeURIComponent(token), {
        method: 'GET',
        credentials: 'include'
    }).then(async (resp) => {
        if (resp.ok) {
            showState(success);
            return;
        }
        showState(error);
    }).catch(() => {
        showState(error);
    });
});
</script>
</main>