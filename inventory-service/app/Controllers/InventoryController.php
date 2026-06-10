<?php

require_once __DIR__ . '/../Services/InventoryService.php';

class InventoryController
{
	private InventoryService $service;

	public function __construct(InventoryService $service)
	{
		$this->service = $service;
	}

	public function search(): void
	{
		$keyword = trim($_GET['q'] ?? '');
		$limit = max(1, (int) ($_GET['limit'] ?? 16));
		$page = max(1, (int) ($_GET['page'] ?? 1));
		$offset = ($page - 1) * $limit;

		if ($keyword === '') {
			$this->jsonResponse(['error' => 'Missing q parameter'], 400);
			return;
		}

		// New filters
		$criterio = trim($_GET['criterio'] ?? '');
		$disponibilidad = trim($_GET['disponibilidad'] ?? '');

		$temas = $_GET['temas'] ?? [];
		if (!is_array($temas)) {
			$temas = $temas === '' ? [] : [$temas];
		}

		$results = $this->service->searchCatalog($keyword, $limit, $offset, $criterio, $disponibilidad, $temas);
		$payload = array_map([$this, 'resourceToArray'], $results);

		$this->jsonResponse(['data' => $payload], 200);
	}

	public function details(): void
	{
		$id = trim($_GET['id'] ?? '');

		if ($id === '') {
			$this->jsonResponse(['error' => 'Missing id parameter'], 400);
			return;
		}

		$libro = $this->service->findByRegistro((int) $id);

		if ($libro === null) {
			$this->jsonResponse(['error' => 'Resource not found'], 404);
			return;
		}

		$codigo = (string) ($libro['codigo'] ?? '');
		$copiasDisponibles = $codigo !== '' ? $this->service->countAvailableByCodigo($codigo) : 0;

		$this->jsonResponse([
			'id_recurso' => (int) $libro['registro'],
			'titulo' => $libro['titulo'],
			'autor' => $libro['autor'],
			'origen' => 'Inventario UPT',
			'portada_url' => null,
			'url_acceso' => null,
			'detalles_extra' => [
				'codigo' => $libro['codigo'],
				'biblioteca' => $libro['biblioteca'],
				'tipo' => $libro['tipo'],
				'procedencia' => $libro['procedencia'],
				'fecha' => $libro['fecha'],
				'estado' => $libro['estado'],
				'copias_disponibles' => $copiasDisponibles,
			],
		], 200);
	}

	public function grouped(): void
	{
		// Admin only
		if (session_status() !== PHP_SESSION_ACTIVE) session_start();
		if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
			$this->jsonResponse(['error' => 'Forbidden'], 403);
			return;
		}

		$items = $this->service->getGroupedInventory();
		$this->jsonResponse(['success' => true, 'data' => $items], 200);
	}

	public function records(): void
	{
		// Admin only
		if (session_status() !== PHP_SESSION_ACTIVE) session_start();
		if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
			$this->jsonResponse(['error' => 'Forbidden'], 403);
			return;
		}

		$codigo = trim((string) ($_GET['codigo'] ?? ''));
		if ($codigo === '') {
			$this->jsonResponse(['error' => 'Missing codigo parameter'], 400);
			return;
		}

		$rows = $this->service->getRecordsByCodigo($codigo);
		$this->jsonResponse(['success' => true, 'data' => $rows], 200);
	}

	public function updateState(): void
	{
		// Admin only
		if (session_status() !== PHP_SESSION_ACTIVE) session_start();
		if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
			$this->jsonResponse(['error' => 'Forbidden'], 403);
			return;
		}

		$raw = file_get_contents('php://input');
		$data = json_decode($raw, true) ?: [];
		$registro = isset($data['registro']) ? (int) $data['registro'] : 0;
		$estado = isset($data['estado']) ? trim((string) $data['estado']) : '';

		if ($registro <= 0 || $estado === '') {
			$this->jsonResponse(['error' => 'Missing registro or estado'], 400);
			return;
		}

		// Only allow specific states from admin UI
		if (!in_array($estado, ['Disponible', 'Prestado'], true)) {
			$this->jsonResponse(['error' => 'Invalid estado'], 400);
			return;
		}

		$ok = $this->service->updateRecordState($registro, $estado);
		if ($ok) {
			// Sincronizar el cambio de estado con el user-library-service (APP_URL-aware)
			$baseUrl = rtrim((string) (getenv('APP_URL') ?: 'http://localhost/nexuslib'), '/');
			$syncUrl = $baseUrl . '/user-library-service/public/index.php/internal/sync-state';
			$syncPayload = json_encode([
				'registro' => $registro,
				'estado' => $estado
			]);

			$ch = curl_init($syncUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $syncPayload);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json',
				'Content-Length: ' . strlen($syncPayload)
			]);
			curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Timeout corto para no bloquear la UI del admin
			curl_exec($ch);
			curl_close($ch);

			$this->jsonResponse(['success' => true], 200);
			return;
		}

		$this->jsonResponse(['error' => 'Registro not found or not updated'], 400);
	}

	private function jsonResponse(array $data, int $statusCode = 200): void
	{
		http_response_code($statusCode);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}

	private function resourceToArray(Resource $resource): array
	{
		return [
			'registro' => $resource->getRegistro(),
			'codigo' => $resource->getCodigo(),
			'titulo' => $resource->getTitulo(),
			'autor' => $resource->getAutor(),
			'biblioteca' => $resource->getBiblioteca(),
			'tipo' => $resource->getTipo(),
			'procedencia' => $resource->getProcedencia(),
			'fecha' => $resource->getFecha(),
			'estado' => $resource->getEstado(),
		];
	}
}
