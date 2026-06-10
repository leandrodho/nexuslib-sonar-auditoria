<?php

namespace App\Services;

class MatchService
{
	public function normalize(array $items, string $source): array
	{
		$origin = match ($source) {
			'inventory' => 'Inventario UPT',
			'alpha' => 'Alpha Cloud',
			'elibro' => 'e-Libro',
			default => 'Desconocido',
		};

		$normalized = [];

		foreach ($items as $item) {
			if (!is_array($item)) {
				continue;
			}

			if ($source === 'inventory') {
				$normalized[] = [
					'id_recurso' => $item['registro'] ?? ($item['id_recurso'] ?? null),
					'titulo' => $item['titulo'] ?? 'Desconocido',
					'autor' => $item['autor'] ?? 'Desconocido',
					'origen' => $origin,
					'portada_url' => null,
					'url_acceso' => null,
				];
				continue;
			}

			$portadaUrl = $item['portada_url'] ?? $item['portadaUrl'] ?? null;
			$urlAcceso = $item['url_acceso'] ?? $item['enlace_alpha'] ?? $item['enlace_elibro'] ?? $item['enlace'] ?? null;

			$normalized[] = [
				'id_recurso' => $item['id_recurso'] ?? ($item['id'] ?? ($item['registro'] ?? null)),
				'titulo' => $item['titulo'] ?? 'Desconocido',
				'autor' => $item['autor'] ?? 'Desconocido',
				'origen' => $origin,
				'portada_url' => $portadaUrl,
				'url_acceso' => $urlAcceso,
			];
		}

		return $normalized;
	}
}
