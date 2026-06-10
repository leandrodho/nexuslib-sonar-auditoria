<main class="min-h-screen pt-24 pb-12 px-6">
    <div class="max-w-6xl mx-auto">
        
        <!-- Search Bar -->
        <div class="mb-10 mt-6">
            <div class="relative w-full shadow-2xl">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input 
                    type="text" 
                    placeholder="Buscar libros, autores o materias..." 
                    value="Programación en Python"
                    class="w-full pl-12 pr-20 md:pr-32 py-4 text-lg bg-slate-800 border border-slate-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500 focus:ring-opacity-50 transition-all shadow-inner"
                    onkeypress="if(event.key === 'Enter') search(this.value)"
                >
                <button onclick="search(document.querySelector('input').value)" class="absolute right-2 top-2 bottom-2 bg-cyan-500 hover:bg-cyan-600 text-white font-medium px-8 rounded-lg transition-colors flex items-center cursor-pointer">
                    Buscar
                </button>
            </div>
        </div>

        <div class="mb-4 sm:hidden flex justify-start px-2">
            <button id="filters-toggle" class="sm:hidden inline-flex items-center px-3 py-2 bg-slate-800 text-white rounded-md" aria-expanded="false" aria-controls="search-filters" aria-label="Mostrar filtros">
                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 6h18"></path>
                    <path d="M6 12h12"></path>
                    <path d="M10 18h4"></path>
                </svg>
                <span class="sr-only">Filtros</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Column: Filters (25%) -->
            <aside id="search-filters" class="hidden md:block md:col-span-1">
                <?php include __DIR__ . '/filters.php'; ?>
            </aside>

            <!-- Column: Results (75%) -->
            <section class="col-span-1 md:col-span-3">
                <div id="loading-spinner" class="hidden mb-6 flex flex-col items-center justify-center gap-3 text-gray-300">
                    <div class="h-10 w-10 animate-spin rounded-full border-4 border-slate-600 border-t-cyan-400"></div>
                    <p class="text-sm md:text-base">Buscando en múltiples bibliotecas...</p>
                </div>

                <p id="results-text" class="text-gray-400 mb-6">Se encontraron <span id="search-count" class="text-cyan-400 font-bold">0</span> resultados</p>

                <div id="results-container" class="space-y-6">
                    <!-- Data Mock: Libro Digital -->
                    <div class="bg-slate-800 bg-opacity-50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden hover:border-cyan-500 transition-all hover:shadow-lg hover:shadow-cyan-500/20 group">
                        <div class="flex flex-col md:flex-row gap-6 p-6">
                            <div class="w-full md:w-40 flex-shrink-0">
                                <div class="w-full h-56 bg-slate-700 rounded-lg overflow-hidden relative">
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    </div>
                                    <div class="absolute top-2 right-2 bg-teal-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">Alpha Cloud</div>
                                </div>
                            </div>

                            <div class="flex-1 flex flex-col justify-center">
                                <h3 class="text-2xl font-bold text-white mb-2">Ingeniería de Software Moderna</h3>
                                <p class="text-white font-semibold mb-6"><span class="text-cyan-400">Autor:</span> Sommerville, Ian</p>
                                <div>
                                    <button class="bg-cyan-500 hover:bg-cyan-600 text-white px-6 py-2 rounded transition-colors font-medium">Ver detalles</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Mock: Libro Físico -->
                    <div class="bg-slate-800 bg-opacity-50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden hover:border-cyan-500 transition-all hover:shadow-lg hover:shadow-cyan-500/20 group">
                        <div class="flex flex-col md:flex-row gap-6 p-6">
                            <div class="w-full md:w-40 flex-shrink-0">
                                <div class="w-full h-56 bg-slate-700 rounded-lg overflow-hidden relative">
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    </div>
                                    <div class="absolute top-2 right-2 bg-blue-800 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">Inventario UPT</div>
                                </div>
                            </div>

                            <div class="flex-1 flex flex-col justify-center">
                                <h3 class="text-2xl font-bold text-white mb-2">Sistemas Operativos Modernos</h3>
                                <p class="text-white font-semibold mb-6"><span class="text-cyan-400">Autor:</span> Tanenbaum, Andrew</p>
                                <div>
                                    <button class="bg-cyan-500 hover:bg-cyan-600 text-white px-6 py-2 rounded transition-colors font-medium">Ver detalles</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 text-center">
                    <button id="btn-load-more" onclick="loadMoreResults()" class="hidden bg-slate-700 hover:bg-slate-600 text-white font-medium px-6 py-3 rounded-lg transition-colors">
                        Cargar más resultados
                    </button>
                </div>
            </section>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('filters-toggle');
    const filters = document.getElementById('search-filters');
    if (toggle && filters) {
        toggle.addEventListener('click', function () {
            const isHidden = filters.classList.toggle('hidden');
            // aria-expanded true when visible
            toggle.setAttribute('aria-expanded', String(!isHidden));
        });
    }
});
</script>