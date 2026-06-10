<?php

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$path = '';
if (!empty($_SERVER['PATH_INFO'])) {
	$path = $_SERVER['PATH_INFO'];
} else {
	$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
	$script = $_SERVER['SCRIPT_NAME'] ?? '';

	if ($script !== '' && strpos($requestPath, $script) === 0) {
		$path = substr($requestPath, strlen($script));
	} else {
		$scriptDir = rtrim(dirname($script), '/\\');
		if ($scriptDir !== '' && strpos($requestPath, $scriptDir) === 0) {
			$path = substr($requestPath, strlen($scriptDir));
		} else {
			$path = $requestPath;
		}
	}
}

$path = ($path === '' || $path === false) ? '/' : '/' . ltrim($path, '/');

// Admin: list all saved books
if ($method === 'GET' && $path === '/admin/saved') {
	require_once __DIR__ . '/../Config/Database.php';
	require_once __DIR__ . '/../Repositories/LibraryRepositoryInterface.php';
	require_once __DIR__ . '/../Repositories/LibraryRepository.php';
	require_once __DIR__ . '/../Services/SavedBooksService.php';
	require_once __DIR__ . '/../Controllers/LibraryController.php';

	$pdo = Database::getConnection();
	$repo = new LibraryRepository($pdo);
	$savedService = new SavedBooksService($repo);
	$reservationService = new ReservationService($repo);
	$controller = new LibraryController($savedService, $reservationService);
	$controller->getAllSavedBooks();
	return;
}

// Admin: list all reserved books
if ($method === 'GET' && $path === '/admin/reserved') {
	require_once __DIR__ . '/../Config/Database.php';
	require_once __DIR__ . '/../Repositories/LibraryRepositoryInterface.php';
	require_once __DIR__ . '/../Repositories/LibraryRepository.php';
	require_once __DIR__ . '/../Services/SavedBooksService.php';
	require_once __DIR__ . '/../Services/ReservationService.php';
	require_once __DIR__ . '/../Controllers/LibraryController.php';

	$pdo = Database::getConnection();
	$repo = new LibraryRepository($pdo);
	$savedService = new SavedBooksService($repo);
	$reservationService = new ReservationService($repo);
	$controller = new LibraryController($savedService, $reservationService);
	$controller->getAllReservedBooks();
	return;
}

// Internal: sync inventory state (called by inventory-service)
if ($method === 'POST' && $path === '/internal/sync-state') {
	require_once __DIR__ . '/../Config/Database.php';
	require_once __DIR__ . '/../Repositories/LibraryRepositoryInterface.php';
	require_once __DIR__ . '/../Repositories/LibraryRepository.php';
	require_once __DIR__ . '/../Services/SavedBooksService.php';
	require_once __DIR__ . '/../Services/ReservationService.php';
	require_once __DIR__ . '/../Controllers/LibraryController.php';

	$pdo = Database::getConnection();
	$repo = new LibraryRepository($pdo);
	$savedService = new SavedBooksService($repo);
	$reservationService = new ReservationService($repo);
	$controller = new LibraryController($savedService, $reservationService);
	$controller->syncInventoryState();
	return;
}

http_response_code(404);
echo json_encode(['error' => 'Internal route not found']);
