<div class="bg-slate-800 bg-opacity-50 border border-slate-700 rounded-xl p-5 shadow-lg flex flex-col gap-2 w-full">
    <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
        </svg>
        Filtros
    </h2>

    <details class="group border-b border-slate-700 pb-4" open>
        <summary class="flex justify-between items-center font-medium cursor-pointer list-none text-white hover:text-cyan-400 transition-colors">
            <span>Criterio de Búsqueda</span>
            <span class="transition group-open:rotate-180">
                <svg fill="none" height="20" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="20"><path d="M6 9l6 6 6-6"></path></svg>
            </span>
        </summary>
        <div class="text-gray-300 mt-4 flex flex-col gap-3 text-sm">
            <label class="flex items-center gap-3 cursor-pointer hover:text-white transition-colors">
                <input type="radio" name="criterio" value="cualquiera" class="w-4 h-4 accent-cyan-500 cursor-pointer" checked>
                <span>Cualquier campo</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer hover:text-white transition-colors">
                <input type="radio" name="criterio" value="titulo" class="w-4 h-4 accent-cyan-500 cursor-pointer">
                <span>Solo por Título</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer hover:text-white transition-colors">
                <input type="radio" name="criterio" value="autor" class="w-4 h-4 accent-cyan-500 cursor-pointer">
                <span>Solo por Autor</span>
            </label>
        </div>
    </details>

    <details class="group border-b border-slate-700 py-4" open>
        <summary class="flex justify-between items-center font-medium cursor-pointer list-none text-white hover:text-cyan-400 transition-colors">
            <span>Origen</span>
            <span class="transition group-open:rotate-180">
                <svg fill="none" height="20" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="20"><path d="M6 9l6 6 6-6"></path></svg>
            </span>
        </summary>
        <div class="text-gray-300 mt-4 flex flex-col gap-3 text-sm">
            <label class="flex items-center gap-3 cursor-pointer hover:text-white transition-colors">
                <input type="checkbox" value="Alpha Cloud" class="filter-origen w-4 h-4 accent-cyan-500 cursor-pointer rounded">
                <span>Alpha Cloud</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer hover:text-white transition-colors">
                <input type="checkbox" value="e-Libro" class="filter-origen w-4 h-4 accent-cyan-500 cursor-pointer rounded">
                <span>e-Libro</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer hover:text-white transition-colors">
                <input type="checkbox" value="Inventario UPT" class="filter-origen w-4 h-4 accent-cyan-500 cursor-pointer rounded">
                <span>Inventario UPT</span>
            </label>
        </div>
    </details>

    <details class="group border-b border-slate-700 py-4" open>
        <summary class="flex justify-between items-center font-medium cursor-pointer list-none text-white hover:text-cyan-400 transition-colors">
            <span>Disponibilidad</span>
            <span class="transition group-open:rotate-180">
                <svg fill="none" height="20" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="20"><path d="M6 9l6 6 6-6"></path></svg>
            </span>
        </summary>
        <div class="text-gray-300 mt-4 flex flex-col gap-3 text-sm">
            <label id="label-disponibilidad" class="flex items-center gap-3 cursor-pointer hover:text-white transition-colors">
                <input type="checkbox" id="filter-disponibilidad" value="disponibles" class="w-4 h-4 accent-cyan-500 cursor-pointer rounded">
                <span>Solo ejemplares disponibles</span>
            </label>
        </div>
    </details>

    <details class="group pt-4" open>
        <summary class="flex justify-between items-center font-medium cursor-pointer list-none text-white hover:text-cyan-400 transition-colors">
            <span>Temas</span>
            <span class="transition group-open:rotate-180">
                <svg fill="none" height="20" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="20"><path d="M6 9l6 6 6-6"></path></svg>
            </span>
        </summary>
        <div class="text-gray-300 mt-4 flex flex-col gap-3 text-sm">
            <label class="flex items-center gap-3 cursor-pointer hover:text-white transition-colors">
                <input type="checkbox" value="Desarrollo de Software" class="filter-tema w-4 h-4 accent-cyan-500 cursor-pointer rounded">
                <span>Desarrollo de Software</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer hover:text-white transition-colors">
                <input type="checkbox" value="Inteligencia Artificial" class="filter-tema w-4 h-4 accent-cyan-500 cursor-pointer rounded">
                <span>Inteligencia Artificial</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer hover:text-white transition-colors">
                <input type="checkbox" value="Cloud Computing" class="filter-tema w-4 h-4 accent-cyan-500 cursor-pointer rounded">
                <span>Cloud Computing</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer hover:text-white transition-colors">
                <input type="checkbox" value="Bases de Datos" class="filter-tema w-4 h-4 accent-cyan-500 cursor-pointer rounded">
                <span>Bases de Datos</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer hover:text-white transition-colors">
                <input type="checkbox" value="Redes y Ciberseguridad" class="filter-tema w-4 h-4 accent-cyan-500 cursor-pointer rounded">
                <span>Redes y Ciberseguridad</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer hover:text-white transition-colors">
                <input type="checkbox" value="Sistemas Operativos" class="filter-tema w-4 h-4 accent-cyan-500 cursor-pointer rounded">
                <span>Sistemas Operativos</span>
            </label>
        </div>
    </details>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const origenes = document.querySelectorAll('.filter-origen');
    const temas = document.querySelectorAll('.filter-tema');
    const dispCheckbox = document.getElementById('filter-disponibilidad');
    const dispLabel = document.getElementById('label-disponibilidad');

    // Lógica para que Origen actúe como selección única (pero desmarcable)
    origenes.forEach(cb => {
        cb.addEventListener('change', function() {
            if (this.checked) {
                origenes.forEach(other => {
                    if (other !== this) other.checked = false;
                });
            }
            actualizarFiltroDisponibilidad();
        });
    });

    // Lógica para que Temas actúe como selección única
    temas.forEach(cb => {
        cb.addEventListener('change', function() {
            if (this.checked) {
                temas.forEach(other => {
                    if (other !== this) other.checked = false;
                });
            }
        });
    });

    // Lógica Inteligente para la Disponibilidad
    function actualizarFiltroDisponibilidad() {
        let deshabilitar = false;
        
        origenes.forEach(cb => {
            if (cb.checked && (cb.value === 'Alpha Cloud' || cb.value === 'e-Libro')) {
                deshabilitar = true;
            }
        });

        if (deshabilitar) {
            dispCheckbox.checked = false;
            dispCheckbox.disabled = true;
            dispCheckbox.classList.add('opacity-40', 'cursor-not-allowed');
            dispLabel.classList.remove('hover:text-white', 'cursor-pointer');
            dispLabel.classList.add('text-slate-600', 'cursor-not-allowed');
        } else {
            dispCheckbox.disabled = false;
            dispCheckbox.classList.remove('opacity-40', 'cursor-not-allowed');
            dispLabel.classList.add('hover:text-white', 'cursor-pointer');
            dispLabel.classList.remove('text-slate-600', 'cursor-not-allowed');
        }
    }
});
</script>