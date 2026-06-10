<script>document.getElementById('page-title').innerText = 'Libros Guardados por Usuarios';</script>

<div class="bg-slate-800 rounded-xl border border-slate-700 shadow-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-900/50 text-cyan-400 text-sm uppercase tracking-wider">
                    <th class="p-4 font-semibold">ID</th>
                    <th class="p-4 font-semibold">UUID Usuario</th>
                    <th class="p-4 font-semibold">Email Usuario</th>
                    <th class="p-4 font-semibold">Código Libro</th>
                    <th class="p-4 font-semibold">Origen</th>
                    <th class="p-4 font-semibold">Título</th>
                    <th class="p-4 font-semibold">Fecha</th>
                </tr>
            </thead>
            <tbody id="saved-tbody" class="text-slate-300 text-sm"></tbody>
        </table>
    </div>
</div>