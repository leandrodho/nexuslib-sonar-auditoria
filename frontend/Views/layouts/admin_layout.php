<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexusLib - Panel de Administración</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Scrollbar personalizada para el panel oscuro */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #1e293b; }
        ::-webkit-scrollbar-thumb { background: #475569; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #64748b; }
    </style>
</head>
<body class="bg-slate-900 text-gray-200 font-sans antialiased overflow-hidden flex h-screen">

    <aside class="w-64 bg-slate-800 border-r border-slate-700 flex flex-col flex-shrink-0">
        <div class="h-16 flex items-center px-6 border-b border-slate-700">
            <div class="w-8 h-8 bg-gradient-to-br from-cyan-400 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                <span class="text-white font-bold text-sm">NL</span>
            </div>
            <span class="text-xl font-bold text-white tracking-wider">Admin<span class="text-cyan-400">Panel</span></span>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            <a href="?view=admin/index" class="flex items-center px-3 py-2.5 rounded-lg text-gray-300 hover:bg-slate-700 hover:text-cyan-400 transition-colors group">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard
            </a>
            <a href="?view=admin/users" class="flex items-center px-3 py-2.5 rounded-lg text-gray-300 hover:bg-slate-700 hover:text-cyan-400 transition-colors group">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Gestión de Usuarios
            </a>
            <a href="?view=admin/inventory" class="flex items-center px-3 py-2.5 rounded-lg text-gray-300 hover:bg-slate-700 hover:text-cyan-400 transition-colors group">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Inventario UPT
            </a>
            <a href="?view=admin/saved_books" class="flex items-center px-3 py-2.5 rounded-lg text-gray-300 hover:bg-slate-700 hover:text-cyan-400 transition-colors group">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                Libros Guardados
            </a>
            <a href="?view=admin/reserved_books" class="flex items-center px-3 py-2.5 rounded-lg text-gray-300 hover:bg-slate-700 hover:text-cyan-400 transition-colors group">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Libros Reservados
            </a>
        </nav>

        <div class="p-4 border-t border-slate-700">
            <a href="?view=home/index" class="flex items-center justify-center w-full px-4 py-2 text-sm text-slate-300 bg-slate-700 hover:bg-slate-600 rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Salir del Panel
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden bg-slate-900">
        <header class="h-16 bg-slate-800/50 border-b border-slate-700 flex items-center px-8 justify-between flex-shrink-0">
            <h2 class="text-xl font-semibold text-white" id="page-title">Cargando...</h2>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-cyan-900 flex items-center justify-center text-cyan-400 font-bold border border-cyan-700">
                    A
                </div>
                <span class="text-sm font-medium text-slate-300">Administrador</span>
            </div>
        </header>

        <div class="flex-1 overflow-auto p-8 relative" id="main-content">
            <?php 
                // Aquí se inyecta la vista solicitada (users.php, inventory.php, etc.)
                if (isset($contentView) && file_exists($contentView)) {
                    require $contentView;
                } else {
                    echo "<p class='text-red-400'>Error: Vista no encontrada.</p>";
                }
            ?>
        </div>
    </main>

    <div id="admin-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/60 backdrop-blur-sm">
        <div class="bg-slate-800 rounded-xl border border-slate-700 shadow-2xl p-6 w-full max-w-md transform scale-95 transition-all">
            <h3 id="modal-title" class="text-xl font-bold text-white mb-2">Confirmación</h3>
            <p id="modal-desc" class="text-slate-400 mb-6">¿Estás seguro de realizar esta acción?</p>
            <div class="flex justify-end gap-3">
                <button onclick="closeModal()" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition">Cancelar</button>
                <button id="modal-confirm-btn" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition font-bold">Confirmar</button>
            </div>
        </div>
    </div>

    <script>
        // Shared helpers required by admin.js
        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            const baseClasses = [
                'fixed', 'bottom-5', 'right-5', 'px-6', 'py-3', 'rounded-lg', 'shadow-xl', 'z-50', 'text-white', 'font-medium'
            ];
            toast.className = baseClasses.join(' ') + ' ' + (type === 'success' ? 'bg-emerald-500' : 'bg-red-500');
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => { toast.classList.add('opacity-0'); setTimeout(() => toast.remove(), 300); }, 3000);
        }
    </script>
    <script src="js/admin.js"></script>
</body>
</html>