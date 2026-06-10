<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;

class SearchAggregator
{
	private MatchService $matchService;
	public static function searchAll($query, $page = 1, $filters = []): array
	{
		try {
			$services = require __DIR__ . '/../Config/services.php';
			$targets = [
				'elibro' => ['url' => $services['elibro'] ?? null, 'limit' => 17],
				'alpha' => ['url' => $services['alpha'] ?? null, 'limit' => 17],
				'inventory' => ['url' => $services['inventory'] ?? null, 'limit' => 16],
			];

			// Determine which targets to call based on filters['origen'] if provided
			$selected = array_values(array_filter(array_map('trim', (array) ($filters['origen'] ?? []))));
			$originMap = [
				'Alpha Cloud' => 'alpha',
				'e-Libro' => 'elibro',
				'Inventario UPT' => 'inventory',
				'Biblioteca UPT' => 'inventory',
				'alpha' => 'alpha',
				'elibro' => 'elibro',
				'inventory' => 'inventory',
			];

			$selectedKeys = [];
			if (!empty($selected)) {
				foreach ($selected as $orig) {
					$key = $originMap[$orig] ?? null;
					if ($key !== null) {
						$selectedKeys[$key] = true;
					}
				}
			}

			// Build HTTP client and promises only for selected targets (or all if none selected)
			$client = new Client();
			$promises = [];

			foreach ($targets as $serviceName => $config) {
				if (!empty($selectedKeys) && !isset($selectedKeys[$serviceName])) {
					continue; // skip services not selected
				}

				$baseUrl = $config['url'];
				$limit = (int) $config['limit'];

				if (!is_string($baseUrl) || $baseUrl === '') {
					continue;
				}

				// Build query params with passthrough filters
				$params = [
					'q' => $query,
					'page' => $page,
					'limit' => $limit,
				];

				if (!empty($filters['criterio'])) {
					$params['criterio'] = $filters['criterio'];
				}
				if (!empty($filters['disponibilidad'])) {
					$params['disponibilidad'] = $filters['disponibilidad'];
				}
				if (!empty($filters['temas'])) {
					// allow temas to be an array
					$params['temas'] = (array) $filters['temas'];
				}

				$url = rtrim($baseUrl, '/') . '/search?' . http_build_query($params);
				$promises[$serviceName] = $client->getAsync($url);
			}

			$settled = Utils::settle($promises)->wait();
			$groupedResults = [
				'elibro' => [],
				'alpha' => [],
				'inventory' => [],
			];

			$matchService = new MatchService();

			foreach ($settled as $serviceName => $item) {
				if (($item['state'] ?? '') !== 'fulfilled') {
					continue;
				}

				$response = $item['value'];
				$decoded = json_decode((string) $response->getBody(), true);

				if (!is_array($decoded)) {
					continue;
				}

				$items = [];
				if (isset($decoded['data']) && is_array($decoded['data'])) {
					$items = $decoded['data'];
				} elseif (array_is_list($decoded)) {
					$items = $decoded;
				} else {
					$items = [$decoded];
				}

				$normalized = $matchService->normalize($items, (string) $serviceName);
				$groupedResults[(string) $serviceName] = $normalized;
			}

			$interleaved = [];
			$order = ['elibro', 'alpha', 'inventory'];

			while (!empty($groupedResults['elibro']) || !empty($groupedResults['alpha']) || !empty($groupedResults['inventory'])) {
				foreach ($order as $serviceName) {
					if (!empty($groupedResults[$serviceName])) {
						$interleaved[] = array_shift($groupedResults[$serviceName]);
					}
				}
			}

			$seen = [];
			$deduplicated = [];

			foreach ($interleaved as $item) {
				if (!is_array($item)) {
					continue;
				}

				$titulo = preg_replace('/\s+/u', '', mb_strtolower(trim((string) ($item['titulo'] ?? '')), 'UTF-8'));
				$autor = preg_replace('/\s+/u', '', mb_strtolower(trim((string) ($item['autor'] ?? '')), 'UTF-8'));
				$origen = preg_replace('/\s+/u', '', mb_strtolower(trim((string) ($item['origen'] ?? '')), 'UTF-8'));

				$signature = md5($titulo . '|' . $autor . '|' . $origen);

				if (isset($seen[$signature])) {
					continue;
				}

				$seen[$signature] = true;
				$deduplicated[] = $item;
			}

			return [
				'data' => $deduplicated,
				'pagina_actual' => $page,
				'hay_mas_resultados' => count($deduplicated) > 0,
			];
		} catch (\Throwable $e) {
			return [];
		}
	}
}
