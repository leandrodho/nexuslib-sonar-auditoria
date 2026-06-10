<?php

use App\Controllers\ElibroController;

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

if ($method === 'GET' && $path === '/search') {
	$controller = new ElibroController();
	$controller->search();
	exit;
}

if ($method === 'GET' && $path === '/details') {
	$controller = new ElibroController();
	$controller->details();
	exit;
}

http_response_code(404);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['error' => 'Endpoint not found'], JSON_UNESCAPED_UNICODE);
