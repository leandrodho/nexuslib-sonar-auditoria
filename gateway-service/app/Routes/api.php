<?php

use App\Controllers\GatewayController;
use App\Services\RequestForwarder;

$forwarder = new RequestForwarder();
$controller = new GatewayController($forwarder);

// Obtenemos una ruta relativa al script para soportar subcarpetas en XAMPP
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

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' && $path === '/search') {
	$controller->search();
	return;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' && $path === '/api/details') {
	$controller->resourceDetails();
	return;
}

// Admin users endpoints: forward to auth-service (supports optional id in path)
if (preg_match('#^/api/admin/users(?:/([0-9]+))?$#', $path, $m)) {
	$id = $m[1] ?? null;
	$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
	$services = require __DIR__ . '/../Config/services.php';
	if (!isset($services['auth'])) {
		http_response_code(500);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(['error' => 'auth service not configured'], JSON_UNESCAPED_UNICODE);
		return;
	}

	$base = rtrim($services['auth'], '/');
	$targetUrl = $base . '/admin/users';
	// Append id as query param if present
	if ($id) {
		$targetUrl .= '?id=' . urlencode($id);
	} else {
		// preserve existing query string
		$qs = (string) ($_SERVER['QUERY_STRING'] ?? '');
		if ($qs !== '') $targetUrl .= (strpos($targetUrl, '?') === false ? '?' : '&') . $qs;
	}

	$allHeaders = function_exists('getallheaders') ? getallheaders() : [];
	$hdrs = [];
	if (is_array($allHeaders)) {
		foreach ($allHeaders as $k => $v) {
			if (is_int($k)) $hdrs[] = $v; else $hdrs[$k] = $v;
		}
	}

	$body = file_get_contents('php://input');
	$resp = $forwarder->forward($method, $targetUrl, $hdrs, $body);

	http_response_code((int)$resp['status']);
	// retransmit headers (skip hop-by-hop)
	$hopByHop = [
		'Connection', 'Keep-Alive', 'Proxy-Authenticate', 'Proxy-Authorization', 'TE', 'Trailers', 'Transfer-Encoding', 'Upgrade', 'Content-Length'
	];
	if (!empty($resp['headers']) && is_array($resp['headers'])) {
		foreach ($resp['headers'] as $name => $values) {
			if (in_array($name, $hopByHop, true)) continue;
			foreach ($values as $val) {
				header($name . ': ' . $val, false);
			}
		}
	}

	echo $resp['body'];
	return;
}

// Admin inventory endpoints: grouped, records, state
if (preg_match('#^/api/admin/inventory(?:/(grouped|records|state))?$#', $path, $m)) {
	$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
	$services = require __DIR__ . '/../Config/services.php';
	if (!isset($services['inventory'])) {
		http_response_code(500);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(['error' => 'inventory service not configured'], JSON_UNESCAPED_UNICODE);
		return;
	}

	$base = rtrim($services['inventory'], '/');
	$targetUrl = $base . '/admin/inventory';

	// append specific endpoint
	$endpoint = $m[1] ?? '';
	if ($endpoint !== '') {
		$targetUrl .= '/' . $endpoint;
	}

	// preserve query string for 'records' endpoint or other params
	$qs = (string) ($_SERVER['QUERY_STRING'] ?? '');
	if ($qs !== '') $targetUrl .= (strpos($targetUrl, '?') === false ? '?' : '&') . $qs;

	$allHeaders = function_exists('getallheaders') ? getallheaders() : [];
	$hdrs = [];
	if (is_array($allHeaders)) {
		foreach ($allHeaders as $k => $v) {
			if (is_int($k)) $hdrs[] = $v; else $hdrs[$k] = $v;
		}
	}

	$body = file_get_contents('php://input');
	$resp = $forwarder->forward($method, $targetUrl, $hdrs, $body);

	http_response_code((int)$resp['status']);
	// retransmit headers (skip hop-by-hop)
	$hopByHop = [
		'Connection', 'Keep-Alive', 'Proxy-Authenticate', 'Proxy-Authorization', 'TE', 'Trailers', 'Transfer-Encoding', 'Upgrade', 'Content-Length'
	];
	if (!empty($resp['headers']) && is_array($resp['headers'])) {
		foreach ($resp['headers'] as $name => $values) {
			if (in_array($name, $hopByHop, true)) continue;
			foreach ($values as $val) {
				header($name . ': ' . $val, false);
			}
		}
	}

	echo $resp['body'];
	return;
}

// Admin user-library endpoints: saved and reserved
if (preg_match('#^/api/admin/(saved|reserved)$#', $path, $m)) {
	$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
	$services = require __DIR__ . '/../Config/services.php';
	if (!isset($services['user-library'])) {
		http_response_code(500);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(['error' => 'user-library service not configured'], JSON_UNESCAPED_UNICODE);
		return;
	}

	$base = rtrim($services['user-library'], '/');
	$endpoint = $m[1];
	$targetUrl = $base . '/admin/' . $endpoint;

	// preserve query string
	$qs = (string) ($_SERVER['QUERY_STRING'] ?? '');
	if ($qs !== '') $targetUrl .= (strpos($targetUrl, '?') === false ? '?' : '&') . $qs;

	$allHeaders = function_exists('getallheaders') ? getallheaders() : [];
	$hdrs = [];
	if (is_array($allHeaders)) {
		foreach ($allHeaders as $k => $v) {
			if (is_int($k)) $hdrs[] = $v; else $hdrs[$k] = $v;
		}
	}

	$body = file_get_contents('php://input');
	$resp = $forwarder->forward($method, $targetUrl, $hdrs, $body);

	http_response_code((int)$resp['status']);
	$hopByHop = [
		'Connection', 'Keep-Alive', 'Proxy-Authenticate', 'Proxy-Authorization', 'TE', 'Trailers', 'Transfer-Encoding', 'Upgrade', 'Content-Length'
	];
	if (!empty($resp['headers']) && is_array($resp['headers'])) {
		foreach ($resp['headers'] as $name => $values) {
			if (in_array($name, $hopByHop, true)) continue;
			foreach ($values as $val) {
				header($name . ': ' . $val, false);
			}
		}
	}

	echo $resp['body'];
	return;
}

// Buscamos la palabra 'api/' seguida de dos bloques de texto (servicio y endpoint)
if (preg_match('/\/api\/([a-zA-Z0-9_-]+)\/([a-zA-Z0-9_-]+)/', $path, $matches)) {
	$service = $matches[1];
	$endpoint = $matches[2];

	$controller->handleRequest($service, $endpoint);
	return;
}

http_response_code(404);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(["error" => "Gateway endpoint not found"]);
