<nav class="fixed top-0 w-full bg-slate-800 bg-opacity-95 backdrop-blur-md border-b border-slate-700 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        
        <a href="index.php" class="flex items-center gap-2 hover:opacity-80 transition-opacity cursor-pointer">
            <div class="w-10 h-10 bg-gradient-to-br from-cyan-400 to-blue-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-lg">NL</span>
            </div>
            <span class="text-white font-bold text-xl hidden sm:inline">NexusLib</span>
        </a>

        <!-- Mobile menu removed: single navbar flow used for all sizes -->

        <div class="ml-auto flex items-center gap-6">
            <div id="navbar-guest" class="hidden sm:flex items-center gap-4">
                <a href="<?php echo view_url('auth/login'); ?>" class="px-4 py-2 text-gray-300 hover:text-white transition-colors">
                    Iniciar Sesión
                </a>
                <a href="<?php echo view_url('auth/register'); ?>" class="px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg transition-colors font-medium">
                    Registrarse
                </a>
            </div>

            <div id="navbar-auth" class="hidden sm:flex">
                <div class="flex items-center gap-6">
                
                <a href="<?php echo view_url('dashboard/saved_books'); ?>" class="flex items-center gap-2 text-gray-300 hover:text-cyan-400 transition-colors text-sm font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h6a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V5z"></path>
                    </svg>
                    <span class="sm:hidden">Guardados</span>
                    <span class="hidden sm:inline">Libros Guardados</span>
                </a>

                    <a href="<?php echo view_url('dashboard/reserved_books'); ?>" class="flex items-center gap-2 text-gray-300 hover:text-green-400 transition-colors text-sm font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="sm:hidden">Reservas</span>
                    <span class="hidden sm:inline">Libros Reservados</span>
                </a>

                    <div class="h-6 w-px bg-slate-600"></div>

                <details class="relative group">
                    <summary class="list-none cursor-pointer flex items-center gap-2 text-gray-200 hover:text-white transition-colors font-medium text-sm">
                        <div class="w-7 h-7 bg-slate-700 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-cyan-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span id="navbar-username">Usuario</span>
                        <svg class="w-4 h-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08Z" clip-rule="evenodd" />
                        </svg>
                    </summary>
                    
                    <div class="absolute right-0 mt-3 w-48 rounded-xl border border-slate-700 bg-slate-800 shadow-2xl overflow-hidden z-50">
                        <a href="<?php echo view_url('dashboard/profile'); ?>" class="block px-4 py-3 text-sm text-gray-200 hover:bg-slate-700 hover:text-white transition-colors">
                            Editar perfil
                        </a>
                        <button type="button" onclick="handleLogout()" class="w-full text-left px-4 py-3 text-sm text-red-400 hover:bg-slate-700 hover:text-red-300 transition-colors border-t border-slate-700">
                            Cerrar sesión
                        </button>
                    </div>
                </details>

                </div>
            </div>
        
    </div>
</nav>