<?php

namespace App\Services;

use App\Adapters\ElibroScraper;

class ElibroSearchService
{
	private ElibroScraper $scraper;

	public function __construct()
	{
		$this->scraper = new ElibroScraper();
	}

	public function searchBooks(string $query, int $page = 1, int $limit = 17): array
	{
		return $this->scraper->search($query, $page, $limit);
	}

	public function getDetails(string $id, string $titulo): ?array
	{
		return $this->scraper->getDetails($id, $titulo);
	}
}
