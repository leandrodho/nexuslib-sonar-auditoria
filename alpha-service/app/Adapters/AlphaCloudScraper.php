<?php

namespace App\Adapters;

use App\Config\ScraperConfig;
use App\Models\LibroAlpha;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

class AlphaCloudScraper
{
	public function search(string $query, int $page = 1, int $limit = 17): array
	{
		try {
			$client = new Client([
				'verify' => false,
				'headers' => [
					'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
				],
			]);
			$url = 'https://www.alphaeditorialcloud.com/library/search/' . urlencode($query);

			$response = $client->get($url);
			$html = (string) $response->getBody();
			$crawler = new Crawler($html);
			$items = [];

			$crawler->filter('article.Issue')->each(function (Crawler $node) use (&$items) {
				$id = null;
				$titulo = 'Desconocido';
				$autor = 'Desconocido';
				$portadaUrl = null;
				$enlaceAlpha = null;

				if ($node->count() > 0) {
					$id = trim((string) ($node->attr('data-id') ?? ''));
					$enlaceAlpha = trim((string) ($node->attr('data-url') ?? ''));
				}

				if ($node->filter('h2.Issue-title')->count() > 0) {
					$titulo = trim($node->filter('h2.Issue-title')->first()->text('Desconocido'));
				}

				if ($node->filter('.Issue-author p')->count() > 0) {
					$autor = trim($node->filter('.Issue-author p')->first()->text('Desconocido'));
				}

				if ($node->filter('.Issue-cover img')->count() > 0) {
					$coverNode = $node->filter('.Issue-cover img')->first();
					$portadaUrl = trim((string) ($coverNode->attr('src') ?? ''));
				}

				$items[] = [
					'id' => $id,
					'titulo' => $titulo,
					'autor' => $autor,
					'portada_url' => $portadaUrl,
					'enlace_alpha' => $enlaceAlpha,
				];
			});

			$offset = max(0, ($page - 1) * $limit);

			return array_slice($items, $offset, $limit);
		} catch (GuzzleException $e) {
			return [];
		} catch (\Throwable $e) {
			return [[
				'id' => 'ERROR',
				'titulo' => 'Error en el Scraper Alpha',
				'autor' => $e->getMessage(),
				'portada_url' => null,
				'enlace_alpha' => null
			]];
		}
	}

	public function getDetails(string $id, string $titulo): ?array
	{
		try {
			$html = $this->fetchHtml('https://www.alphaeditorialcloud.com/library/search/' . urlencode($titulo));
			if ($html === null || trim($html) === '') {
				return null;
			}

			libxml_use_internal_errors(true);
			$doc = new \DOMDocument();
			$loaded = @$doc->loadHTML($html);
			if (!$loaded) {
				libxml_clear_errors();
				return null;
			}

			$xpath = new \DOMXPath($doc);
			$articles = $xpath->query("//article[contains(concat(' ', normalize-space(@class), ' '), ' Issue ')]");
			if ($articles === false || $articles->length === 0) {
				libxml_clear_errors();
				return null;
			}

			foreach ($articles as $article) {
				if (!$article instanceof \DOMElement) {
					continue;
				}

				$idNodo = trim((string) $article->getAttribute('data-id'));
				if ($idNodo !== $id) {
					continue;
				}

				$idExtraido = trim((string) $article->getAttribute('data-id'));
				$tituloExtraido = $this->extractNodeText($xpath, $article, ".//*[contains(concat(' ', normalize-space(@class), ' '), ' Issue-title ')]", 'Desconocido');
				$autorExtraido = $this->extractNodeText($xpath, $article, ".//*[contains(concat(' ', normalize-space(@class), ' '), ' Issue-author ')]//p", 'Desconocido');
				$portadaExtraida = $this->extractNodeAttr($xpath, $article, ".//*[contains(concat(' ', normalize-space(@class), ' '), ' Issue-cover ')]//img", 'src');
				$urlExtraida = trim((string) $article->getAttribute('data-url'));

				$fechaExtraida = $this->extractNodeText($xpath, $article, ".//*[contains(concat(' ', normalize-space(@class), ' '), ' Issue-publicationDate ')]", '');
				$sinopsisExtraida = $this->extractNodeText($xpath, $article, ".//*[contains(concat(' ', normalize-space(@class), ' '), ' Issue-description-crop ')]", '');

				libxml_clear_errors();

				return [
					'id_recurso' => $idExtraido,
					'titulo' => $tituloExtraido,
					'autor' => $autorExtraido,
					'origen' => 'Alpha Cloud',
					'portada_url' => $portadaExtraida,
					'url_acceso' => $urlExtraida,
					'detalles_extra' => [
						'isbn' => $article->getAttribute('data-external-id') ?? '',
						'ano_publicacion' => $fechaExtraida ?? '',
						'editorial' => '',
						'sinopsis' => $sinopsisExtraida ?? '',
					],
				];
			}

			libxml_clear_errors();

			return null;
		} catch (GuzzleException $e) {
			return null;
		} catch (\Throwable $e) {
			return null;
		}
	}

	private function fetchHtml(string $url): ?string
	{
		try {
			$client = new Client([
				'verify' => false,
				'headers' => [
					'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
				],
			]);

			$response = $client->get($url);

			return (string) $response->getBody();
		} catch (\Throwable $e) {
			return null;
		}
	}

	private function extractNodeText(\DOMXPath $xpath, \DOMNode $context, string $query, string $default = ''): string
	{
		$nodes = $xpath->query($query, $context);
		if ($nodes === false || $nodes->length === 0) {
			return $default;
		}

		return trim((string) $nodes->item(0)->textContent);
	}

	private function extractNodeAttr(\DOMXPath $xpath, \DOMNode $context, string $query, string $attribute): string
	{
		$nodes = $xpath->query($query, $context);
		if ($nodes === false || $nodes->length === 0 || !$nodes->item(0) instanceof \DOMElement) {
			return '';
		}

		return trim((string) $nodes->item(0)->getAttribute($attribute));
	}
}
