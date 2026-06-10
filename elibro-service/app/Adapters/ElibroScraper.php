<?php

namespace App\Adapters;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ElibroScraper
{
	public function search(string $query, int $page = 1, int $limit = 17): array
	{
		try {
			$client = new Client();
			$url = 'https://elibro.net/api/collection_titles_search/?page_size=' . $limit . '&page=' . $page;

			$response = $client->post($url, [
				'json' => [
					'type' => 'quicksearch',
					'q' => $query,
					'lang' => 'sp',
					'channel' => 'bf4daf71-e3d8-487a-bfb4-fee6d9a22d0a',
				],
			]);
			$data = json_decode((string) $response->getBody(), true);
			$items = [];

			foreach (($data['results'] ?? []) as $item) {
				$items[] = [
					'id' => $item['id'] ?? null,
					'titulo' => $item['title_name'] ?? 'Desconocido',
					'autor' => implode(', ', $item['contributors'] ?? ['Desconocido']),
					'portada_url' => $item['cover'] ?? null,
					'enlace_elibro' => 'https://elibro.net/es/lc/bibliotecaupt/titulos/' . ($item['id'] ?? ''),
				];
			}

			return $items;

		} catch (GuzzleException $e) {
			return [];
		} catch (\Throwable $e) {
			return [[
				'id' => 'ERROR',
				'titulo' => 'Error en Scraper eLibro',
				'autor' => $e->getMessage(),
				'portada_url' => null,
				'enlace_elibro' => null,
			]];
		}
	}

	public function getDetails(string $id, string $titulo): ?array
	{
		try {
			$client = new Client();
			$url = 'https://elibro.net/api/collection_titles_search/?page_size=17&page=1';

			$response = $client->post($url, [
				'json' => [
					'type' => 'quicksearch',
					'q' => $titulo,
					'lang' => 'sp',
					'channel' => 'bf4daf71-e3d8-487a-bfb4-fee6d9a22d0a',
				],
			]);

			$data = json_decode((string) $response->getBody(), true);
			foreach (($data['results'] ?? []) as $libro) {
				if (!is_array($libro) || (($libro['id'] ?? null) != $id)) {
					continue;
				}

				$autores = !empty($libro['contributors']) ? implode(', ', $libro['contributors']) : 'Autor desconocido';
				$isbns = !empty($libro['productidentifier_set']) ? implode(' / ', $libro['productidentifier_set']) : '';

				return [
					'id_recurso' => $libro['id'] ?? '',
					'titulo' => htmlspecialchars_decode($libro['title_name'] ?? 'Sin título'),
					'autor' => htmlspecialchars_decode($autores),
					'origen' => 'e-Libro',
					'portada_url' => $libro['cover'] ?? '',
					'url_acceso' => 'https://elibro.net/es/lc/bibliotecaupt/titulos/' . ($libro['id'] ?? ''),
					'detalles_extra' => [
						'isbn' => $isbns,
						'ano_publicacion' => $libro['edition_year'] ?? '',
						'editorial' => $libro['publisher'] ?? '',
						'sinopsis' => 'Para leer la sinopsis y el contenido completo, haz clic en "Leer Libro".'
					]
				];
			}

			return null;
		} catch (GuzzleException $e) {
			return null;
		} catch (\Throwable $e) {
			return null;
		}
	}
}
