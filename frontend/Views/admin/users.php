<script>document.getElementById('page-title').innerText = 'Gestión de Usuarios';</script>

<div class="bg-slate-800 rounded-xl border border-slate-700 shadow-xl overflow-hidden">
    <div class="p-5 border-b border-slate-700 flex justify-between items-center bg-slate-800/80">
        <h3 class="font-bold text-white text-lg">Lista de Cuentas Registradas</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-900/50 text-cyan-400 text-sm uppercase tracking-wider">
                    <th class="p-4 font-semibold">ID</th>
                    <th class="p-4 font-semibold">Nombre</th>
                    <th class="p-4 font-semibold">Email</th>
                    <th class="p-4 font-semibold">Rol</th>
                    <th class="p-4 font-semibold">Estado</th>
                    <th class="p-4 font-semibold text-right">Acciones</th>
                </tr>
            </thead>
            <tbody id="users-tbody" class="text-slate-300 text-sm">
                </tbody>
        </table>
    </div>
</div>