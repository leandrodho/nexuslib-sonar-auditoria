<script>document.getElementById('page-title').innerText = 'Inventario UPT (Agrupado)';</script>

<div class="bg-slate-800 rounded-xl border border-slate-700 shadow-xl overflow-hidden">
    <div class="p-5 border-b border-slate-700 bg-slate-800/80">
        <h3 class="font-bold text-white text-lg">Catálogo General de Libros</h3>
        <p class="text-xs text-slate-400 mt-1">Los ejemplares físicos están agrupados bajo un mismo código de obra.</p>
        <div class="mt-4">
            <input type="text" id="search-codigo" placeholder="🔍 Buscar por Código..." class="w-full md:w-1/3 bg-slate-900 border border-slate-600 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block p-2.5 placeholder-slate-400">
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-900/50 text-cyan-400 text-sm uppercase tracking-wider">
                    <th class="p-4 font-semibold">Código</th>
                    <th class="p-4 font-semibold">Título</th>
                    <th class="p-4 font-semibold">Autor</th>
                    <th class="p-4 font-semibold">Total Ejemplares</th>
                    <th class="p-4 font-semibold text-right">Acciones</th>
                </tr>
            </thead>
            <tbody id="inventory-tbody" class="text-slate-300 text-sm">
                </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('search-codigo')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#inventory-tbody tr');

    rows.forEach(row => {
        const codigoCell = row.querySelector('td:first-child');
        if (codigoCell) {
            const codigo = codigoCell.textContent.toLowerCase();
            row.style.display = codigo.includes(searchTerm) ? '' : 'none';
        }
    });
});
</script>