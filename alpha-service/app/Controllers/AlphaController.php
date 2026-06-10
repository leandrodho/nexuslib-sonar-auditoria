<?php

namespace App\Controllers;

use App\Services\AlphaSearchService;

class AlphaController
{
	private AlphaSearchService $searchService;

	public function __construct()
	{
		$this->searchService = new AlphaSearchService();
	}

	public function search(): void
	{
		$query = trim($_GET['q'] ?? '');
		$page = max(1, (int) ($_GET['page'] ?? 1));
		$limit = max(1, (int) ($_GET['limit'] ?? 17));

		if ($query === '') {
			$this->jsonResponse(['error' => 'Missing q parameter'], 400);
			return;
		}

		$books = $this->searchService->searchBooks($query, $page, $limit);

		// Post-filter by criterio if provided (titulo or autor)
		$criterio = trim($_GET['criterio'] ?? '');
		if ($criterio === 'titulo') {
			$books = array_values(array_filter($books, function ($item) use ($query) {
				return stripos((string) ($item['titulo'] ?? ''), $query) !== false;
			}));
		} elseif ($criterio === 'autor') {
			$books = array_values(array_filter($books, function ($item) use ($query) {
				return stripos((string) ($item['autor'] ?? ''), $query) !== false;
			}));
		}
		$this->jsonResponse(['data' => $books], 200);
	}

	public function details(): void
	{
		$id = trim($_GET['id'] ?? '');
		$titulo = trim($_GET['titulo'] ?? '');

		if ($id === '' || $titulo === '') {
			$this->jsonResponse(['error' => 'Missing id or titulo parameter'], 400);
			return;
		}

		$details = $this->searchService->getDetails($id, $titulo);

		if ($details === null) {
			$this->jsonResponse(['error' => 'Resource not found'], 404);
			return;
		}

		$this->jsonResponse($details, 200);
	}

	private function jsonResponse(array $data, int $statusCode = 200): void
	{
		http_response_code($statusCode);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}
}
