<script>document.getElementById('page-title').innerText = 'Gestión de Ejemplares Físicos';</script>

<div class="mb-4">
    <a href="?view=admin/inventory" class="inline-flex items-center text-cyan-400 hover:text-cyan-300 transition text-sm font-medium">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Volver al Catálogo
    </a>
</div>

<div class="bg-slate-800 rounded-xl border border-slate-700 shadow-xl overflow-hidden">
    <div class="p-5 border-b border-slate-700 bg-slate-800/80 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-white text-lg" id="records-title">Ejemplares del Código: Cargando...</h3>
            <p class="text-xs text-slate-400 mt-1">Modifica el estado de disponibilidad del inventario físico.</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-900/50 text-cyan-400 text-sm uppercase tracking-wider">
                    <th class="p-4 font-semibold">Registro (ID Físico)</th>
                    <th class="p-4 font-semibold">Biblioteca</th>
                    <th class="p-4 font-semibold">Estado Actual</th>
                    <th class="p-4 font-semibold text-right w-64">Cambiar Estado</th>
                </tr>
            </thead>
            <tbody id="records-tbody" class="text-slate-300 text-sm">
                </tbody>
        </table>
    </div>
</div>