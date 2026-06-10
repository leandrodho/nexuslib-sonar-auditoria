<?php

require_once __DIR__ . '/../app/Config/Database.php';
require_once __DIR__ . '/../app/Models/SavedBook.php';
require_once __DIR__ . '/../app/Models/ReservedBook.php';
require_once __DIR__ . '/../app/Repositories/LibraryRepositoryInterface.php';
require_once __DIR__ . '/../app/Repositories/LibraryRepository.php';
require_once __DIR__ . '/../app/Services/SavedBooksService.php';
require_once __DIR__ . '/../app/Services/ReservationService.php';
require_once __DIR__ . '/../app/Controllers/LibraryController.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
	http_response_code(204);
	exit;
}

try {
	$pdo = Database::getConnection();
	$repository = new LibraryRepository($pdo);
	$savedBooksService = new SavedBooksService($repository);
	$reservationService = new ReservationService($repository);
	$controller = new LibraryController($savedBooksService, $reservationService);

	$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

	if (strpos($uri, '/admin/') === false && preg_match('/\/(save|saved|check-status|reserve|reserved|cancel)$/', $uri, $matches)) {
		switch ($matches[1]) {
			case 'save':
				$controller->save();
				exit;

			case 'saved':
				$controller->getSaved();
				exit;

			case 'check-status':
				$controller->checkStatus();
				exit;

			case 'reserve':
				$controller->reserve();
				exit;

			case 'reserved':
				$controller->getReserved();
				exit;

			case 'cancel':
				$controller->cancel();
				exit;
		}
	}

	// Delegar al enrutador si la ruta contiene /admin/ o /internal/
	if (strpos($uri, '/admin/') !== false || strpos($uri, '/internal/') !== false) {
		require __DIR__ . '/../app/Routes/api.php';
		exit;
	}

	// Respuesta por defecto si no coincide nada
	http_response_code(404);
	echo json_encode(['error' => 'Endpoint not found'], JSON_UNESCAPED_UNICODE);
	exit;
} catch (\Throwable $e) {
	http_response_code(500);
	echo json_encode(['error' => 'Error interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
	exit;
}
