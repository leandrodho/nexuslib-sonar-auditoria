<main class="gradient-bg min-h-screen pt-20 pb-12">
        <div class="max-w-4xl mx-auto px-6 flex flex-col items-center justify-center">
            
            <div class="text-center mb-12">
                <h1 class="text-5xl md:text-6xl font-bold mb-4 text-white">
                    Descubre recursos académicos
                </h1>
                <p class="text-xl text-gray-300 mb-8">
                    Acceso unificado a libros digitales, bases de datos y catálogos de bibliotecas
                </p>
            </div>

            <div class="w-full max-w-2xl mb-12">
                <div class="relative shadow-2xl rounded-xl">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        placeholder="Buscar libros, artículos, autores..." 
                        class="w-full pl-12 pr-20 md:pr-32 py-4 bg-slate-800 border border-slate-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500 focus:ring-opacity-50 text-lg shadow-inner transition-all"
                        onkeypress="if(event.key === 'Enter') goToSearch(this.value)"
                    >
                    <button onclick="goToSearch(document.querySelector('input').value)" class="absolute right-2 top-2 bottom-2 bg-cyan-500 hover:bg-cyan-600 text-white font-medium px-8 rounded-lg transition-colors flex items-center cursor-pointer">
                        Buscar
                    </button>
                </div>
            </div>

            <div class="w-full max-w-[280px] mx-auto">
                <div class="bg-slate-800 bg-opacity-50 backdrop-blur-sm border border-slate-700 rounded-xl p-6 w-full shadow-lg">
                    <h3 class="text-lg font-semibold text-white mb-6 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Fuentes de Información
                    </h3>
                    
                    <div class="flex flex-col items-start gap-4">
                        
                        <div class="flex items-center gap-4 px-5 py-3 bg-slate-700/30 border border-slate-600 rounded-lg hover:border-teal-500/50 transition-colors w-full">
                            <span class="w-3 h-3 rounded-full bg-teal-500 shadow-[0_0_8px_rgba(20,184,166,0.8)]"></span>
                            <span class="text-gray-200 font-medium">Alpha Cloud</span>
                        </div>

                        <div class="flex items-center gap-4 px-5 py-3 bg-slate-700/30 border border-slate-600 rounded-lg hover:border-orange-500/50 transition-colors w-full">
                            <span class="w-3 h-3 rounded-full bg-orange-500 shadow-[0_0_8px_rgba(249,115,22,0.8)]"></span>
                            <span class="text-gray-200 font-medium">e-Libro</span>
                        </div>

                        <div class="flex items-center gap-4 px-5 py-3 bg-slate-700/30 border border-slate-600 rounded-lg hover:border-blue-500/50 transition-colors w-full">
                            <span class="w-3 h-3 rounded-full bg-blue-800 shadow-[0_0_8px_rgba(30,64,175,0.8)]"></span>
                            <span class="text-gray-200 font-medium">Biblioteca UPT</span>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
        function goToSearch(query) {
            const params = new URLSearchParams();
            if (query) params.append('q', query);
            
            // Redirigimos al enrutador principal en index.php pasando la vista y los parámetros
            window.location.href = 'index.php?view=search/results' + (params.toString() ? '&' + params.toString() : '');
        }
    </script>