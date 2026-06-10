<?php

require_once __DIR__ . '/../app/Config/Database.php';
require_once __DIR__ . '/../app/Models/Resource.php';
require_once __DIR__ . '/../app/Repositories/InventoryRepositoryInterface.php';
require_once __DIR__ . '/../app/Repositories/InventoryRepository.php';
require_once __DIR__ . '/../app/Services/InventoryService.php';
require_once __DIR__ . '/../app/Controllers/InventoryController.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
	http_response_code(204);
	exit;
}

try {
	$pdo = Database::getConnection();
	$repository = new InventoryRepository($pdo);
	$service = new InventoryService($repository);
	$controller = new InventoryController($service);
} catch (Throwable $e) {
	http_response_code(500);
	echo json_encode(['error' => 'Internal server error'], JSON_UNESCAPED_UNICODE);
	exit;
}

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

try {
	// Delegate internal routes to app/Routes/api.php for clearer separation
	if (strpos($uri, '/internal') !== false) {
		require __DIR__ . '/../app/Routes/api.php';
		exit;
	}
	if (strpos($uri, '/search') !== false) {
		$controller->search();
		exit;
	}

	if (strpos($uri, '/details') !== false) {
		$controller->details();
		exit;
	}
} catch (Throwable $e) {
	http_response_code(500);
	echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
	exit;
}

// Delegar al enrutador de administración si la ruta contiene /admin
if (strpos($uri, '/admin') !== false) {
    require __DIR__ . '/../app/Routes/api.php';
    exit;
}

// Endpoint not found fallback
http_response_code(404);
echo json_encode(['error' => 'Endpoint not found'], JSON_UNESCAPED_UNICODE);
exit;
