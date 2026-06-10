<?php

$allowedViews = [
	'home/index',
	'search/results',
	'details/details_digital',
	'details/details_physical',
	'auth/login',
	'auth/register',
	'auth/email_verification',
	'saved_books',
	'reserved_books',
	'dashboard/profile',
];

$pageTitles = [
	'home/index' => 'NexusLib - Inicio',
	'search/results' => 'Resultados de búsqueda - NexusLib',
	'details/details_digital' => 'Detalles del Recurso Digital - NexusLib',
	'details/details_physical' => 'Detalles del Libro Físico - NexusLib',
	'auth/login' => 'Iniciar Sesión - NexusLib',
	'auth/register' => 'Registrarse - NexusLib',
	'auth/email_verification' => 'Verificación de Correo - NexusLib',
	'saved_books' => 'Mis Libros Guardados - NexusLib',
	'reserved_books' => 'Mis Libros Reservados - NexusLib',
	'dashboard/profile' => 'Mi Perfil - NexusLib',
];

// Map of view names to frontend paths used by client-side routing/rewrite
$viewRoutes = [
	'home/index' => view_url('home/index'),
	'search/results' => view_url('search/results'),
	'details/details_digital' => view_url('details/details_digital'),
	'details/details_physical' => view_url('details/details_physical'),
	'auth/login' => view_url('auth/login'),
	'auth/register' => view_url('auth/register'),
	'auth/email_verification' => view_url('auth/email_verification'),
	'saved_books' => view_url('saved_books'),
	'reserved_books' => view_url('reserved_books'),
	'dashboard/profile' => view_url('dashboard/profile'),
	// Additional useful mappings
	'dashboard/saved_books' => view_url('saved_books'),
	'dashboard/reserved_books' => view_url('reserved_books'),
	'admin/index' => view_url('admin/index'),
	'admin/users' => view_url('admin/users'),
	'admin/inventory' => view_url('admin/inventory'),
	'admin/inventory_records' => view_url('admin/inventory_records'),
];

function view_url(string $view, array $query = []): string
{
	$basePath = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
	$query = array_merge(['view' => $view], $query);

	return $basePath . '?' . http_build_query($query);
}

// Routing: choose layout based on view (admin uses admin_layout)
$view = $_GET['view'] ?? 'home/index';
$currentView = $view; // keep compatibility with existing JS that expects currentView
$contentView = __DIR__ . "/../Views/{$view}.php";

// If the view starts with "admin/", use the admin layout
if (strpos($view, 'admin/') === 0) {
	require __DIR__ . '/../Views/layouts/admin_layout.php';
} else {
	// For all other views use the normal header/footer layout
	require __DIR__ . '/../Views/layouts/header.php';
	require __DIR__ . '/../Views/layouts/navbar.php'; // <-- Esta es la línea que faltaba
	if (file_exists($contentView)) {
		require $contentView;
	}
	require __DIR__ . '/../Views/layouts/footer.php';
}

?>
<script>
(function () {
	const currentView = <?php echo json_encode($currentView, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
	const viewRoutes = <?php echo json_encode($viewRoutes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
	const pageTitles = <?php echo json_encode($pageTitles, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;

	const routeMap = {
		'/home/index.html': 'home/index',
		'/search/results.html': 'search/results',
		'/details/details_digital.html': 'details/details_digital',
		'/details/details_physical.html': 'details/details_physical',
		'/auth/login.html': 'auth/login',
		'/auth/register.html': 'auth/register',
		'/dashboard/profile.html': 'dashboard/profile',
	};

	function viewUrl(view, params = {}) {
		const baseUrl = new URL(viewRoutes[view] || viewRoutes['home/index'], window.location.origin);
		Object.entries(params).forEach(([key, value]) => {
			if (value !== undefined && value !== null && value !== '') {
				baseUrl.searchParams.set(key, value);
			}
		});
		return baseUrl.pathname + baseUrl.search + baseUrl.hash;
	}

	function ensureNavbarState() {
		const guest = document.getElementById('navbar-guest');
		const auth = document.getElementById('navbar-auth');
		const user = getUserData();
		if (!guest || !auth) return;

		if (user) {
			// enforce display via inline style to avoid Tailwind responsive conflicts
			guest.style.display = 'none';
			auth.style.display = 'flex';

			// set username if available
			try {
				const navbarUsername = document.getElementById('navbar-username');
				if (navbarUsername) {
					navbarUsername.textContent = user.name || 'Usuario';
				}
			} catch (e) { /* ignore DOM errors */ }
		} else {
			guest.style.display = 'flex';
			auth.style.display = 'none';
		}
	}

	// Keep navbar state in sync across tabs/windows
	window.addEventListener('storage', function (e) {
		if (e.key === 'nexuslib_user' || e.key === 'user_uuid') {
			try { ensureNavbarState(); } catch (err) { /* ignore */ }
		}
	});

	function getUserData() {
		const rawUser = localStorage.getItem('nexuslib_user');
		if (!rawUser) {
			return null;
		}

		try {
			return JSON.parse(rawUser);
		} catch (error) {
			return null;
		}
	}

	function rewriteLinks() {
		document.querySelectorAll('a[href]').forEach((anchor) => {
			const originalHref = anchor.getAttribute('href');
			if (!originalHref) {
				return;
			}

			const cleanedHref = originalHref.split('#')[0];
			const hash = originalHref.includes('#') ? '#' + originalHref.split('#').slice(1).join('#') : '';
			const queryIndex = cleanedHref.indexOf('?');
			const path = queryIndex >= 0 ? cleanedHref.slice(0, queryIndex) : cleanedHref;
			const queryString = queryIndex >= 0 ? cleanedHref.slice(queryIndex + 1) : '';
			const view = routeMap[path];

			if (!view) {
				return;
			}

			const params = new URLSearchParams(queryString);
			anchor.setAttribute('href', viewUrl(view, Object.fromEntries(params.entries())) + hash);
		});
	}

	function setAuthBodyClasses() {
		if (currentView === 'auth/login') {
			document.body.classList.add('flex', 'items-center', 'justify-center', 'min-h-screen', 'px-4');
		}

		if (currentView === 'auth/register') {
			document.body.classList.add('flex', 'items-center', 'justify-center', 'min-h-screen', 'px-4', 'py-8');
		}
	}

	function goToSearch(query) {
		const origin = document.querySelector('input[name="origin"]:checked')?.value || '';
		const params = {};

		if (query) {
			params.q = query;
		}

		if (origin) {
			params.origin = origin;
		}

		window.location.href = viewUrl('search/results', params);
	}

	function goToDetails(type, bookId) {
		const targetView = type === 'physical' ? 'details/details_physical' : 'details/details_digital';
		window.location.href = viewUrl(targetView, { id: bookId });
	}

	function search(query) {
		const params = {};

		if (query) {
			params.q = query;
		}

		if (currentView === 'search/results') {
			// In-page search: update globals and fetch results without full reload
			currentQuery = query || '';
			currentPage = 1;
			renderedResultsCount = 0;
			const container = document.getElementById('results-container');
			if (container) container.innerHTML = '';
			fetchResults(false);
			return;
		}

		window.location.href = viewUrl('search/results', params);
	}

	let currentQuery = '';
	let currentPage = 1;
	let renderedResultsCount = 0;

	function escapeHtml(value) {
		return String(value ?? '')
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#39;');
	}

	function getOriginBadgeClass(origin) {
		if (origin === 'Alpha Cloud') {
			return 'bg-teal-500';
		}
		if (origin === 'e-Libro') {
			return 'bg-orange-500';
		}
		if (origin === 'Inventario UPT' || origin === 'Biblioteca UPT') {
			return 'bg-blue-800';
		}
		return 'bg-slate-600';
	}

	function renderCoverHtml(recurso) {
		const origen = escapeHtml(recurso?.origen || 'Desconocido');
		const badgeClass = getOriginBadgeClass(recurso?.origen || '');
		const portadaUrl = (recurso?.portada_url || '').trim();
		const titulo = escapeHtml(recurso?.titulo || 'Sin título');
		// If there's an explicit portada URL, use it
		if (portadaUrl !== '') {
			return `
				<div class="w-full h-56 bg-slate-700 rounded-lg overflow-hidden relative">
					<img src="${escapeHtml(portadaUrl)}" alt="Portada de ${titulo}" class="w-full h-full object-cover">
					<div class="absolute top-2 right-2 ${badgeClass} text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">
						${origen}
					</div>
				</div>
			`;
		}

		// No portada URL: if the origin is Inventario UPT, show local logo as fallback
		if ((recurso?.origen || '') === 'Inventario UPT') {
			return `
				<div class="w-full h-56 bg-slate-700 rounded-lg overflow-hidden relative">
					<img src="/nexuslib/frontend/public/images/logo-upt.png" alt="Portada de ${titulo}" class="w-full h-full object-cover">
					<div class="absolute top-2 right-2 ${badgeClass} text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">
						${origen}
					</div>
				</div>
			`;
		}

		// Default placeholder when no portada and not Inventario UPT
		return `
			<div class="w-full h-56 bg-slate-800 border-2 border-slate-700 rounded-lg flex flex-col items-center justify-center relative">
				<svg class="w-16 h-16 text-slate-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
				</svg>
				<span class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Sin Portada</span>
				<div class="absolute top-2 right-2 ${badgeClass} text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">
					${origen}
				</div>
			</div>
		`;
	}

	function buildResultCardHtml(recurso, savedSet = new Set()) {
		const id = escapeHtml(recurso?.id_recurso ?? '');
		const tituloRaw = String(recurso?.titulo || 'Sin título');
		const portadaRaw = String(recurso?.portada_url || '');
		const titulo = escapeHtml(tituloRaw);
		const autor = escapeHtml(recurso?.autor || 'Desconocido');
		const coverHtml = renderCoverHtml(recurso);
		const origen = String(recurso?.origen || '');
		const codigoRaw = String(recurso?.id_recurso ?? '');
		const favoriteKey = codigoRaw + '::' + origen;
		const isSaved = savedSet.has(codigoRaw) || savedSet.has(favoriteKey);
		const targetView = origen === 'Inventario UPT' ? 'details/details_physical' : 'details/details_digital';
		const idParam = encodeURIComponent(String(recurso?.id_recurso ?? ''));
		const tituloParam = encodeURIComponent(String(recurso?.titulo ?? ''));
		const origenParam = encodeURIComponent(origen);
		const favoriteButtonClass = isSaved
			? 'absolute top-4 right-4 z-20 inline-flex h-10 w-10 items-center justify-center rounded-full bg-yellow-500/15 text-yellow-400 transition-colors duration-200 hover:text-yellow-300 hover:bg-yellow-500/20'
			: 'absolute top-4 right-4 z-20 inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-900/70 text-gray-400 transition-colors duration-200 hover:text-cyan-400 hover:bg-slate-900/90';
		const favoriteIconClass = isSaved
			? 'favorite-icon h-5 w-5 fill-current text-yellow-400 stroke-yellow-400 transition-colors duration-200'
			: 'favorite-icon h-5 w-5 fill-none transition-colors duration-200';
		const favoritedAttr = isSaved ? 'true' : 'false';

		return `
			<div class="relative bg-slate-800 bg-opacity-50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden hover:border-cyan-500 transition-all hover:shadow-lg hover:shadow-cyan-500/20 group">
				<button type="button" data-favorited="${favoritedAttr}" onclick="toggleFavoriteBackend(this, event, '${id}', '${origen}', '${tituloRaw.replace(/'/g, "\\'")}', '${portadaRaw.replace(/'/g, "\\'")}')" class="${favoriteButtonClass}" aria-label="Guardar en favoritos">
					<svg class="${favoriteIconClass}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M6 3.75A2.25 2.25 0 0 0 3.75 6v13.5c0 1.236 1.337 1.999 2.4 1.37l5.85-3.462 5.85 3.462c1.063.629 2.4-.134 2.4-1.37V6A2.25 2.25 0 0 0 18 3.75H6Z"></path>
					</svg>
				</button>
				<div class="flex flex-col md:flex-row gap-6 p-6">
					<div class="w-full md:w-40 flex-shrink-0">
						${coverHtml}
					</div>

					<div class="flex-1 flex flex-col justify-center">
						<h3 class="text-2xl font-bold text-white mb-2 group-hover:text-cyan-400 transition-colors">
							${titulo}
						</h3>
						<p class="text-white font-semibold mb-6"><span class="text-cyan-400">Autor:</span> ${autor}</p>
						<div>
							<button onclick="window.location.href='index.php?view=${targetView}&id=${idParam}&titulo=${tituloParam}&origen=${origenParam}'" class="bg-cyan-500 hover:bg-cyan-600 text-white px-6 py-2 rounded transition-colors font-medium cursor-pointer">
								Ver detalles
							</button>
						</div>
					</div>
				</div>
			</div>
		`;
	}

	function displayResults(items, savedSet, append = false) {
		const container = document.getElementById('results-container');
		if (!container) {
			return;
		}

		const html = items.map((item) => buildResultCardHtml(item, savedSet)).join('');
		if (append) {
			container.insertAdjacentHTML('beforeend', html);
		} else {
			container.innerHTML = html;
		}
	}

	function showToast(message, type = 'error') {
		const toast = document.createElement('div');
		const baseClasses = [
			'fixed',
			'bottom-5',
			'right-5',
			'px-6',
			'py-3',
			'rounded-lg',
			'shadow-xl',
			'z-50',
			'text-white',
			'font-medium',
			'transition-all',
			'duration-300',
			'transform',
			'translate-y-0',
			'opacity-100',
		];

		toast.className = baseClasses.join(' ') + ' ' + (type === 'success' ? 'bg-emerald-500' : 'bg-red-500');
		toast.textContent = message;
		document.body.appendChild(toast);

		window.setTimeout(() => {
			toast.classList.add('opacity-0', 'translate-y-2');
			window.setTimeout(() => {
				toast.remove();
			}, 300);
		}, 3000);
	}

	async function toggleFavoriteBackend(button, event, codigo, origen, titulo, portadaUrl = null) {
		event.stopPropagation();
		if (!button) return;

		const userUuid = localStorage.getItem('user_uuid');
		if (!userUuid) {
			if (typeof window.showToast === 'function') window.showToast('Debes iniciar sesión para guardar libros', 'error');
			return;
		}

		try {
			const response = await fetch('/nexuslib/gateway-service/public/index.php/api/user-library/save', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({
					user_uuid: userUuid,
					codigo: codigo,
					origen: origen,
					titulo: titulo,
					portada_url: (typeof portadaUrl === 'string' && portadaUrl.trim() !== '') ? portadaUrl : null
				})
			});

			if (response.ok) {
				const icon = button.querySelector('.favorite-icon');
				const isActive = button.dataset.favorited === 'true';

				button.dataset.favorited = !isActive ? 'true' : 'false';
				button.classList.toggle('text-gray-400', isActive);
				button.classList.toggle('text-yellow-400', !isActive);
				button.classList.toggle('bg-slate-900/70', isActive);
				button.classList.toggle('bg-yellow-500/15', !isActive);
				button.classList.toggle('hover:text-cyan-400', isActive);
				button.classList.toggle('hover:text-yellow-300', !isActive);

				if (icon) {
					icon.classList.toggle('fill-none', isActive);
					icon.classList.toggle('fill-current', !isActive);
					icon.classList.toggle('stroke-current', isActive);
					icon.classList.toggle('stroke-yellow-400', !isActive);
					icon.classList.toggle('text-yellow-400', !isActive);
				}

				if (typeof window.showToast === 'function') {
					window.showToast(!isActive ? 'Libro guardado en favoritos' : 'Libro removido de favoritos', 'success');
				}
			}
		} catch (error) {
			console.error('Error al guardar:', error);
		}
	}

	function fetchResults(append = false) {
		const container = document.getElementById('results-container');
		const loadMoreButton = document.getElementById('btn-load-more');
		const countEl = document.getElementById('search-count');
		const loadingSpinner = document.getElementById('loading-spinner');
		const resultsText = document.getElementById('results-text');

		if (!container) {
			return;
		}

		if (!append) {
			renderedResultsCount = 0;
			if (resultsText) {
				resultsText.classList.add('hidden');
			}
			if (loadMoreButton) {
				loadMoreButton.classList.add('hidden');
			}
			if (loadingSpinner) {
				loadingSpinner.classList.remove('hidden');
			}
			container.innerHTML = '';
		} else if (loadMoreButton) {
			loadMoreButton.textContent = 'Cargando...';
			loadMoreButton.disabled = true;
		}

		if (currentQuery === '') {
			if (loadingSpinner) {
				loadingSpinner.classList.add('hidden');
			}
			if (resultsText) {
				resultsText.classList.remove('hidden');
			}
			if (countEl) {
				countEl.textContent = '0';
			}
			if (loadMoreButton) {
				loadMoreButton.textContent = 'Cargar más resultados';
				loadMoreButton.disabled = false;
				loadMoreButton.classList.add('hidden');
			}
			return;
		}

		// Collect filters from the DOM (if controls exist)
		const criterioParam = document.querySelector('input[name="criterio"]:checked')?.value || '';
		const origenArr = Array.from(document.querySelectorAll('.filter-origen:checked')).map(cb => cb.value);
		const disponibilidadParam = document.getElementById('filter-disponibilidad')?.checked ? 'disponibles' : '';
		const temaSeleccionado = document.querySelector('.filter-tema:checked')?.value || '';

		// If a theme is selected, use it as the effective search query (acts as shortcut)
		let effectiveQuery = currentQuery;
		if (temaSeleccionado) {
			effectiveQuery = temaSeleccionado;
			const searchInput = document.querySelector('main input[type="text"]');
			if (searchInput) searchInput.value = temaSeleccionado;
		}

		const params = new URLSearchParams();
		params.set('q', effectiveQuery);
		params.set('page', String(currentPage));
		if (criterioParam) params.set('criterio', criterioParam);
		if (disponibilidadParam) params.set('disponibilidad', disponibilidadParam);
		origenArr.forEach(o => params.append('origen[]', o));

		fetch(`/nexuslib/gateway-service/public/index.php/search?${params.toString()}`)
			.then((response) => response.json())
			.then(async (data) => {
				const items = Array.isArray(data?.data) ? data.data : [];
				const userUuid = (localStorage.getItem('user_uuid') || '').trim();
				const savedSet = new Set();

				if (userUuid !== '') {
					try {
						const savedResponse = await fetch(`/nexuslib/gateway-service/public/index.php/api/user-library/saved?user_uuid=${encodeURIComponent(userUuid)}`);
						if (savedResponse.ok) {
							const savedData = await savedResponse.json();
							const savedItems = Array.isArray(savedData?.data) ? savedData.data : [];
							savedItems.forEach((savedItem) => {
								const codigo = String(savedItem?.codigo ?? '').trim();
								const origen = String(savedItem?.origen ?? '').trim();
								if (codigo !== '') {
									savedSet.add(codigo);
									if (origen !== '') {
										savedSet.add(codigo + '::' + origen);
									}
								}
							});
						}
					} catch (_error) {
						// Fallback: render without favorite hydration when saved list fails.
					}
				}

				displayResults(items, savedSet, append);

				renderedResultsCount += items.length;
				if (loadingSpinner) {
					loadingSpinner.classList.add('hidden');
				}
				if (resultsText) {
					resultsText.classList.remove('hidden');
				}
				if (countEl) {
					countEl.textContent = String(renderedResultsCount);
				}

				if (loadMoreButton) {
					loadMoreButton.disabled = false;
					loadMoreButton.textContent = 'Cargar más resultados';
					if (data?.hay_mas_resultados) {
						loadMoreButton.classList.remove('hidden');
					} else {
						loadMoreButton.classList.add('hidden');
					}
				}

				if (!append && items.length === 0) {
					container.innerHTML = '<div class="rounded-lg border border-slate-700 bg-slate-800/60 p-6 text-center text-gray-300">No se encontraron resultados para esta búsqueda.</div>';
				}
			})
			.catch(() => {
				if (loadingSpinner) {
					loadingSpinner.classList.add('hidden');
				}
				if (resultsText) {
					resultsText.classList.remove('hidden');
				}
				if (!append) {
					container.innerHTML = '<div class="rounded-lg border border-red-400/60 bg-red-500/10 p-6 text-center text-red-200">No se pudieron cargar los resultados en este momento.</div>';
				}
				if (loadMoreButton) {
					loadMoreButton.disabled = false;
					loadMoreButton.textContent = 'Cargar más resultados';
					loadMoreButton.classList.add('hidden');
				}
			});
	}

	function loadMoreResults() {
		currentPage += 1;
		fetchResults(true);
	}

	function handleLogin(event) {
		event.preventDefault();
		const loginErrorDiv = document.getElementById('login-error');
		if (loginErrorDiv) {
			loginErrorDiv.classList.add('hidden');
		}

		const email = document.getElementById('email')?.value || '';
		const password = document.getElementById('password')?.value || '';

		if (!email || !password) {
			if (loginErrorDiv) {
				loginErrorDiv.textContent = 'Por favor completa todos los campos.';
				loginErrorDiv.classList.remove('hidden');
			}
			return;
		}

		fetch('/nexuslib/gateway-service/public/index.php/api/auth/login', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			credentials: 'include',
			body: JSON.stringify({
				email: email,
				password: password,
			})
		})
			.then((response) => response.json().then((data) => ({ status: response.status, data })))
			.then(({ status, data }) => {
				if (status === 403 && data?.error === 'email_not_verified') {
					if (loginErrorDiv) {
						loginErrorDiv.textContent = 'Verifica tu correo antes de iniciar sesión.';
						loginErrorDiv.classList.remove('hidden');
					}
					return;
				}

				if (data.success === true) {
					const userData = data.user || { email: email };
					localStorage.setItem('nexuslib_user', JSON.stringify(userData));
					if (userData.uuid) {
						localStorage.setItem('user_uuid', String(userData.uuid));
					}
					// Redirect admins to admin panel
					const role = (userData.role || '').toString().toLowerCase();
					if (role === 'admin') {
						window.location.href = 'index.php?view=admin/index';
					} else {
						window.location.href = 'index.php';
					}
					return;
				}

				if (loginErrorDiv) {
					loginErrorDiv.textContent = data.error || 'Credenciales inválidas';
					loginErrorDiv.classList.remove('hidden');
				}
			})
			.catch(() => {
				if (loginErrorDiv) {
					loginErrorDiv.textContent = 'No se pudo iniciar sesión. Intenta nuevamente.';
					loginErrorDiv.classList.remove('hidden');
				}
			});
	}

	function handleRegister(event) {
		event.preventDefault();
		const registerErrorDiv = document.getElementById('register-error');
		const registerSuccessDiv = document.getElementById('register-success');
		if (registerErrorDiv) {
			registerErrorDiv.classList.add('hidden');
		}
		if (registerSuccessDiv) {
			registerSuccessDiv.classList.add('hidden');
		}

		const name = document.getElementById('name')?.value || '';
		const email = document.getElementById('email')?.value || '';
		const password = document.getElementById('password')?.value || '';

		if (!name || !email || !password) {
			if (registerErrorDiv) {
				registerErrorDiv.textContent = 'Por favor completa todos los campos.';
				registerErrorDiv.classList.remove('hidden');
			}
			return;
		}

		fetch('/nexuslib/gateway-service/public/index.php/api/auth/register', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({
				name: name,
				email: email,
				password: password,
			})
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.success === true) {
					document.getElementById('register-error').classList.add('hidden');
					const successDiv = document.getElementById('register-success');
					// Mensaje de éxito instructivo y estilo verde
					successDiv.textContent = '¡Éxito! Revisa tu correo para verificar tu cuenta.';
					successDiv.classList.remove('hidden');
					successDiv.classList.add('bg-green-500/20', 'text-green-400', 'border', 'border-green-500/25', 'p-3', 'rounded');
					event.target.reset();
					// Mantener al usuario en la página tras registro exitoso (sin redirección automática)
					return;
				}

				if (registerErrorDiv) {
					registerErrorDiv.textContent = data.error || 'Error al registrar';
					registerErrorDiv.classList.remove('hidden');
				}
			})
			.catch(() => {
				if (registerErrorDiv) {
					registerErrorDiv.textContent = 'No se pudo completar el registro. Intenta nuevamente.';
					registerErrorDiv.classList.remove('hidden');
				}
			});
	}

	function reserveBook() {
		if (!localStorage.getItem('nexuslib_user')) {
			alert('Debes iniciar sesión para reservar libros.');
			window.location.href = viewUrl('auth/login');
			return;
		}

		alert('¡Libro reservado exitosamente! Puedes recogerlo en la Biblioteca Central en 48 horas.');
	}

	function checkAuth() {
		const user = getUserData();

		if (!user) {
			window.location.href = viewUrl('auth/login');
			return;
		}

		const userName = document.getElementById('user-name');
		const profileName = document.getElementById('profile-name');
		const profileEmail = document.getElementById('profile-email');

		if (userName) {
			userName.textContent = 'Hola, ' + user.name;
		}

		if (profileName) {
			profileName.textContent = user.name;
		}

		if (profileEmail) {
			profileEmail.textContent = user.email;
		}
	}

	function handleLogout() {
		localStorage.removeItem('nexuslib_user');
		localStorage.removeItem('user_uuid');
		ensureNavbarState();
		window.location.href = 'index.php';
	}

	function logout() {
		handleLogout();
	}

	function editProfile() {
		alert('Editar perfil - Feature próximamente disponible');
	}

	function removeSaved(element) {
		if (confirm('¿Deseas eliminar este libro de tus guardados?')) {
			element.closest('.bg-slate-700')?.remove();
			alert('Libro eliminado de favoritos.');
		}
	}

	function pickupBook() {
		alert('Recogida confirmada. Por favor, presenta tu ID en la biblioteca.');
	}

	// legacy cancelReservation function removed to avoid global name collision with view-level modal handler

	window.viewUrl = viewUrl;
	window.goToSearch = goToSearch;
	window.goToDetails = goToDetails;
	window.search = search;
	window.handleLogin = handleLogin;
	window.handleRegister = handleRegister;
	window.reserveBook = reserveBook;
	window.checkAuth = checkAuth;
	window.handleLogout = handleLogout;
	window.logout = logout;
	window.loadMoreResults = loadMoreResults;
	window.editProfile = editProfile;
	window.removeSaved = removeSaved;
	window.pickupBook = pickupBook;
	// legacy cancelReservation removed to avoid global name collision with view-level modal handler
	window.showToast = showToast;
	window.toggleFavoriteBackend = toggleFavoriteBackend;
	window.toggleNavbarState = ensureNavbarState;

	document.addEventListener('DOMContentLoaded', function () {
		rewriteLinks();
		setAuthBodyClasses();
		ensureNavbarState();

		if (currentView === 'dashboard/profile') {
			checkAuth();
		}

		if (currentView === 'search/results') {
			const q = new URLSearchParams(window.location.search).get('q');
			currentQuery = q || '';
			currentPage = 1;

			const searchInput = document.querySelector('main input[type="text"]');
			if (searchInput) {
				searchInput.value = currentQuery;

				// UX: cuando el usuario empieza a teclear manualmente, desmarcar cualquier Tema seleccionado
				searchInput.addEventListener('input', function () {
					const temas = document.querySelectorAll('.filter-tema');
					if (!temas) return;
					temas.forEach(cb => { try { cb.checked = false; } catch (e) { /* ignore */ } });
				});
			}

			fetchResults(false);
		}
	});

	if (pageTitles[currentView]) {
		document.title = pageTitles[currentView];
	}
})();
</script>
