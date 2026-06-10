    <main class="pt-24 pb-12 px-6 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-cyan-400 to-blue-600 rounded-xl mb-4">
                <span class="text-white font-bold text-3xl">NL</span>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">NexusLib</h1>
            <p class="text-gray-400">Acceso a recursos académicos</p>
        </div>

        <!-- Login Card -->
        <div class="bg-slate-800 bg-opacity-50 backdrop-blur-sm border border-slate-700 rounded-2xl p-8 shadow-2xl">
            
            <h2 class="text-2xl font-bold text-white mb-6 text-center">Iniciar Sesión</h2>

            <div id="login-error" class="hidden mb-4 rounded-lg border border-red-400/60 bg-red-500/10 px-4 py-3 text-sm text-red-200 break-words whitespace-normal"></div>

            <form onsubmit="handleLogin(event)">
                <!-- Email Field -->
                <div class="mb-6">
                    <label class="block text-gray-300 font-semibold mb-2">Correo Electrónico</label>
                    <input 
                        type="email" 
                        id="email"
                        placeholder="tu@correo.com"
                        required
                        class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500 focus:ring-opacity-50 transition-all"
                    >
                </div>

                <!-- Password Field -->
                <div class="mb-8">
                    <label class="block text-gray-300 font-semibold mb-2">Contraseña</label>
                    <input 
                        type="password" 
                        id="password"
                        placeholder="••••••••"
                        required
                        class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500 focus:ring-opacity-50 transition-all"
                    >
                </div>

                <!-- Login Button -->
                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-600 hover:to-cyan-700 text-white font-bold py-3 px-4 rounded-lg transition-all transform hover:scale-105 shadow-lg mb-4"
                >
                    Entrar
                </button>
            </form>

            <!-- Divider -->
            <div class="relative mb-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-600"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-slate-800 text-gray-400">O</span>
                </div>
            </div>

            <!-- Create Account Link -->
            <p class="text-center text-gray-300">
                ¿No tienes cuenta? 
                <a href="<?php echo view_url('auth/register'); ?>" class="text-cyan-400 hover:text-cyan-300 font-semibold transition-colors">
                    Crear cuenta nueva
                </a>
            </p>

        </div>

        <!-- Back Home Link -->
        <div class="text-center mt-6">
            <a href="<?php echo view_url('home/index'); ?>" class="text-gray-400 hover:text-gray-300 text-sm transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Volver al inicio
            </a>
        </div>
    </div>
    </main>
