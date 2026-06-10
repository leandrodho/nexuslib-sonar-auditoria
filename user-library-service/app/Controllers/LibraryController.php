<?php

require_once __DIR__ . '/../Models/SavedBook.php';
require_once __DIR__ . '/../Models/ReservedBook.php';
require_once __DIR__ . '/../Services/SavedBooksService.php';
require_once __DIR__ . '/../Services/ReservationService.php';

class LibraryController
{
	private SavedBooksService $savedBooksService;
	private ReservationService $reservationService;

	public function __construct(SavedBooksService $savedBooksService, ReservationService $reservationService)
	{
		$this->savedBooksService = $savedBooksService;
		$this->reservationService = $reservationService;
	}

	public function save(): void
	{
		$data = $this->getInputData();
		$userUuid = trim((string) ($data['user_uuid'] ?? ($_POST['user_uuid'] ?? ($_GET['user_uuid'] ?? ''))));
		$codigo = trim((string) ($data['codigo'] ?? ($_POST['codigo'] ?? ($_GET['codigo'] ?? ''))));
		$origen = trim((string) ($data['origen'] ?? ($_POST['origen'] ?? ($_GET['origen'] ?? ''))));
		$titulo = trim((string) ($data['titulo'] ?? ($_POST['titulo'] ?? ($_GET['titulo'] ?? ''))));
		$portadaUrl = $data['portada_url'] ?? ($_POST['portada_url'] ?? ($_GET['portada_url'] ?? null));
		$portadaUrl = is_string($portadaUrl) ? trim($portadaUrl) : null;
		$portadaUrl = ($portadaUrl === '') ? null : $portadaUrl;

		if ($userUuid === '' || $codigo === '' || $origen === '' || $titulo === '') {
			$this->jsonResponse(['error' => 'Faltan datos requeridos'], 400);
			return;
		}

		$book = new SavedBook([
			'user_uuid' => $userUuid,
			'codigo' => $codigo,
			'origen' => $origen,
			'titulo' => $titulo,
		]);

		$result = $this->savedBooksService->saveBook($book, $portadaUrl);

		if (empty($result['success'])) {
			$this->jsonResponse(['error' => 'No se pudo guardar el libro'], 400);
			return;
		}

		$this->jsonResponse($result, 200);
	}

	public function getSaved(): void
	{
		$userUuid = trim($_GET['user_uuid'] ?? '');

		if ($userUuid === '') {
			$this->jsonResponse(['error' => 'Falta user_uuid'], 400);
			return;
		}

		$books = $this->savedBooksService->getSavedBooks($userUuid);
		$payload = array_map([$this, 'savedBookToArray'], $books);

		$this->jsonResponse(['data' => $payload], 200);
	}

	public function checkStatus(): void
	{
		$data = $this->getInputData();
		$userUuid = trim((string) ($_GET['user_uuid'] ?? ($data['user_uuid'] ?? '')));
		$codigo = trim((string) ($_GET['codigo'] ?? ($data['codigo'] ?? '')));
		$origen = trim((string) ($_GET['origen'] ?? ($data['origen'] ?? '')));

		if ($userUuid === '' || $codigo === '' || $origen === '') {
			$this->jsonResponse(['error' => 'Faltan datos requeridos'], 400);
			return;
		}

		$isSaved = $this->savedBooksService->isBookSaved($userUuid, $codigo, $origen);
		$this->jsonResponse(['is_saved' => $isSaved], 200);
	}

	public function reserve(): void
	{
		$data = $this->getInputData();
		$userUuid = trim((string) ($data['user_uuid'] ?? ($_GET['user_uuid'] ?? '')));
		$codigo = trim((string) ($data['codigo'] ?? ($_GET['codigo'] ?? '')));
		$estado = trim((string) ($data['estado'] ?? ($_GET['estado'] ?? 'Pendiente')));

		if ($userUuid === '' || $codigo === '') {
			$this->jsonResponse(['error' => 'Faltan datos requeridos'], 400);
			return;
		}

		$book = new ReservedBook([
			'user_uuid' => $userUuid,
			'codigo' => $codigo,
			'estado' => $estado === '' ? 'Pendiente' : $estado,
		]);

		try {
			$registro = $this->reservationService->reserveBook($book);
		} catch (\Exception $e) {
			$this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
			return;
		}

		$this->jsonResponse(['success' => true, 'registro' => $registro], 201);
	}

	public function cancel(): void
	{
		$data = $this->getInputData();
		$userUuid = trim((string) ($data['user_uuid'] ?? ($_GET['user_uuid'] ?? '')));
		$registro = isset($data['registro']) ? (int) $data['registro'] : (isset($_GET['registro']) ? (int) $_GET['registro'] : 0);

		if ($userUuid === '' || $registro <= 0) {
			$this->jsonResponse(['error' => 'Faltan datos requeridos'], 400);
			return;
		}

		$result = $this->reservationService->cancelReservation($userUuid, $registro);
		$statusCode = !empty($result['success']) ? 200 : 400;
		$this->jsonResponse($result, $statusCode);
	}

	public function getReserved(): void
	{
		$userUuid = trim($_GET['user_uuid'] ?? '');

		if ($userUuid === '') {
			$this->jsonResponse(['error' => 'Falta user_uuid'], 400);
			return;
		}

		$books = $this->reservationService->getReservedBooks($userUuid);
		$payload = [];

		foreach ($books as $book) {
			$item = $this->reservedBookToArray($book);
			$registro = $book->getRegistro();
			$item['registro'] = $registro;
			$item['titulo'] = 'Desconocido';
			$item['autor'] = 'Desconocido';
			$item['biblioteca'] = 'Desconocido';

			if (is_int($registro) && $registro > 0) {
				// Try to fetch details from inventory-service
				try {
					$baseUrl = rtrim((string) (getenv('APP_URL') ?: 'http://localhost/nexuslib'), '/');
					$detailUrl = $baseUrl . '/inventory-service/public/index.php/details?id=' . urlencode((string)$registro);
					$resp = @file_get_contents($detailUrl);
					if ($resp !== false) {
						$data = json_decode($resp, true);
						if (is_array($data)) {
							if (!empty($data['titulo'])) {
								$item['titulo'] = $data['titulo'];
							}
							if (!empty($data['autor'])) {
								$item['autor'] = $data['autor'];
							}
							$extra = $data['detalles_extra'] ?? [];
							if (is_array($extra) && !empty($extra['biblioteca'])) {
								$item['biblioteca'] = $extra['biblioteca'];
							}
						}
					}
				} catch (\Throwable $_) {
					// ignore and leave fallbacks
				}
			}

			$payload[] = $item;
		}

		$this->jsonResponse(['data' => $payload], 200);
	}

	public function getAllSavedBooks(): void
	{
		// Admin only
		if (session_status() !== PHP_SESSION_ACTIVE) session_start();
		if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
			$this->jsonResponse(['error' => 'Forbidden'], 403);
			return;
		}

		$rows = $this->savedBooksService->getAllSavedBooks();
		$this->jsonResponse(['success' => true, 'data' => $rows], 200);
	}

	public function getAllReservedBooks(): void
	{
		// Admin only
		if (session_status() !== PHP_SESSION_ACTIVE) session_start();
		if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
			$this->jsonResponse(['error' => 'Forbidden'], 403);
			return;
		}

		$rows = $this->reservationService->getAllReservedBooks();
		$this->jsonResponse(['success' => true, 'data' => $rows], 200);
	}

	public function syncInventoryState(): void
	{
		$data = $this->getInputData();
		$registro = isset($data['registro']) ? (int) $data['registro'] : 0;
		$estado = isset($data['estado']) ? trim((string) $data['estado']) : '';

		if ($registro <= 0 || $estado === '') {
			$this->jsonResponse(['error' => 'Faltan registro o estado'], 400);
			return;
		}

		require_once __DIR__ . '/../Config/Database.php';
		require_once __DIR__ . '/../Repositories/LibraryRepository.php';

		$pdo = Database::getConnection();
		$repo = new LibraryRepository($pdo);

		try {
			if ($estado === 'Prestado') {
				$ok = $repo->updateReservationState($registro, 'Prestado');
				$this->jsonResponse(['success' => true, 'updated' => (bool)$ok], 200);
				return;
			}

			if ($estado === 'Disponible') {
				$ok = $repo->deleteReservationByRegistry($registro);
				$this->jsonResponse(['success' => true, 'deleted' => (bool)$ok], 200);
				return;
			}

			$this->jsonResponse(['error' => 'Estado no soportado'], 400);
			return;
		} catch (\Throwable $e) {
			$this->jsonResponse(['error' => 'Internal error: ' . $e->getMessage()], 500);
			return;
		}
	}

	private function getInputData(): array
	{
		$raw = file_get_contents('php://input');
		if ($raw === false || trim($raw) === '') {
			return [];
		}

		$decoded = json_decode($raw, true);
		return is_array($decoded) ? $decoded : [];
	}

	private function jsonResponse(array $data, int $statusCode = 200): void
	{
		http_response_code($statusCode);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}

	private function savedBookToArray(SavedBook $book): array
	{
		return [
			'id' => $book->getId(),
			'user_uuid' => $book->getUserUuid(),
			'codigo' => $book->getCodigo(),
			'origen' => $book->getOrigen(),
			'titulo' => $book->getTitulo(),
			'portada_url' => $book->getPortadaUrl(),
			'fecha_guardado' => $book->getFechaGuardado(),
		];
	}

	private function reservedBookToArray(ReservedBook $book): array
	{
		return [
			'id' => $book->getId(),
			'user_uuid' => $book->getUserUuid(),
			'codigo' => $book->getCodigo(),
			'estado' => $book->getEstado(),
			'fecha_reserva' => $book->getFechaReserva(),
		];
	}
}
