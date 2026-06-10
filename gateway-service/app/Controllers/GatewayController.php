<?php

namespace App\Controllers;

use App\Services\SearchAggregator;
use App\Services\RequestForwarder;

class GatewayController
{
	private RequestForwarder $forwarder;

	public function __construct(RequestForwarder $forwarder)
	{
		$this->forwarder = $forwarder;
	}

	/**
	 * Maneja la petición hacia un microservicio destino.
	 * @param string $service
	 * @param string $endpoint
	 */
	public function handleRequest(string $service, string $endpoint): void
	{
		$services = require __DIR__ . '/../Config/services.php';

		if (!isset($services[$service])) {
			http_response_code(404);
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode(['error' => 'Service not found']);
			return;
		}

		$base = rtrim($services[$service], '/');
		$targetUrl = $base . '/' . ltrim($endpoint, '/');
		$queryString = (string) ($_SERVER['QUERY_STRING'] ?? '');
		if ($queryString !== '') {
			$targetUrl .= (strpos($targetUrl, '?') === false ? '?' : '&') . $queryString;
		}

		$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
		$allHeaders = function_exists('getallheaders') ? getallheaders() : [];
		$body = file_get_contents('php://input');

		// Convert headers associative to simple array of strings
		$hdrs = [];
		if (is_array($allHeaders)) {
			foreach ($allHeaders as $k => $v) {
				if (is_int($k)) {
					$hdrs[] = $v;
				} else {
					$hdrs[$k] = $v;
				}
			}
		}

		$resp = $this->forwarder->forward($method, $targetUrl, $hdrs, $body);

		http_response_code((int)$resp['status']);

		// Retransmit selected response headers from destination service to client
		$hopByHop = [
			'Connection', 'Keep-Alive', 'Proxy-Authenticate', 'Proxy-Authorization', 'TE', 'Trailers', 'Transfer-Encoding', 'Upgrade', 'Content-Length'
		];
		if (!empty($resp['headers']) && is_array($resp['headers'])) {
			foreach ($resp['headers'] as $name => $values) {
				if (in_array($name, $hopByHop, true)) continue;
				foreach ($values as $val) {
					// Forward header preserving duplicates (e.g., multiple Set-Cookie)
					header($name . ': ' . $val, false);
				}
			}
		}

		// Passthrough content (assume already JSON or appropriate)
		echo $resp['body'];
	}

	public function search(): void
	{
		$query = trim($_GET['q'] ?? '');
		$page = max(1, (int) ($_GET['page'] ?? 1));

		if ($query === '') {
			http_response_code(400);
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode(['error' => 'Missing q parameter'], JSON_UNESCAPED_UNICODE);
			return;
		}

		// New filters accepted from the frontend
		$origen = $_GET['origen'] ?? [];
		if (!is_array($origen)) {
			$origen = $origen === '' ? [] : [$origen];
		}

		$criterio = trim($_GET['criterio'] ?? '');
		$disponibilidad = trim($_GET['disponibilidad'] ?? '');

		$temas = $_GET['temas'] ?? [];
		if (!is_array($temas)) {
			$temas = $temas === '' ? [] : [$temas];
		}

		$filters = [
			'origen' => $origen,
			'criterio' => $criterio,
			'disponibilidad' => $disponibilidad,
			'temas' => $temas,
		];

		$results = SearchAggregator::searchAll($query, $page, $filters);

		http_response_code(200);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($results, JSON_UNESCAPED_UNICODE);
	}

	public function resourceDetails(): void
	{
		$id = trim($_GET['id'] ?? ($_GET['q'] ?? ''));
		$titulo = trim($_GET['titulo'] ?? '');
		$origen = trim($_GET['origen'] ?? '');

		if ($id === '' || $titulo === '' || $origen === '') {
			http_response_code(400);
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode(['error' => 'Missing id, titulo or origen parameter'], JSON_UNESCAPED_UNICODE);
			return;
		}

		$baseUrl = rtrim((string) (getenv('APP_URL') ?: 'http://localhost/nexuslib'), '/');

		switch ($origen) {
			case 'Alpha Cloud':
				$targetUrl = $baseUrl . '/alpha-service/public/index.php/details?id=' . urlencode($id) . '&titulo=' . urlencode($titulo);
				break;
			case 'e-Libro':
				$targetUrl = $baseUrl . '/elibro-service/public/index.php/details?id=' . urlencode($id) . '&titulo=' . urlencode($titulo);
				break;
			case 'Inventario UPT':
				$targetUrl = $baseUrl . '/inventory-service/public/index.php/details?id=' . urlencode($id);
				break;
			default:
				http_response_code(400);
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode(['error' => 'Origen no soportado'], JSON_UNESCAPED_UNICODE);
				return;
		}

		try {
			$resp = $this->forwarder->forward('GET', $targetUrl, [], null);
			$status = (int) ($resp['status'] ?? 0);
			$body = (string) ($resp['body'] ?? '');

			if ($status < 200 || $status >= 300) {
				http_response_code(500);
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode([
					'error' => 'Destination service failed',
					'target_url' => $targetUrl,
					'status_code' => $status,
					'response_body' => $body
				], JSON_UNESCAPED_UNICODE);
				return;
			}

			http_response_code($status);
			header('Content-Type: application/json; charset=utf-8');
			header('Access-Control-Allow-Origin: *');
			echo $body;
		} catch (\Throwable $e) {
			http_response_code(500);
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode([
				'error' => 'Gateway Exception',
				'message' => $e->getMessage(),
				'target_url' => $targetUrl ?? 'unknown'
			], JSON_UNESCAPED_UNICODE);
		}
	}
}

