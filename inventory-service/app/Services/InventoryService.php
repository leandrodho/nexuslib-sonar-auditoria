<?php

require_once __DIR__ . '/../Repositories/InventoryRepositoryInterface.php';

class InventoryService
{
	private InventoryRepositoryInterface $repository;

	public function __construct(InventoryRepositoryInterface $repository)
	{
		$this->repository = $repository;
	}

	public function searchCatalog(string $keyword, int $limit = 16, int $offset = 0, string $criterio = '', string $disponibilidad = '', array $temas = []): array
	{
		return $this->repository->searchResources($keyword, $limit, $offset, $criterio, $disponibilidad, $temas);
	}

	public function countSearchResults(string $keyword, string $criterio = '', string $disponibilidad = '', array $temas = []): int
	{
		if (method_exists($this->repository, 'countSearchResources')) {
			return $this->repository->countSearchResources($keyword, $criterio, $disponibilidad, $temas);
		}

		return 0;
	}

	public function getExemplares(string $codigo): array
	{
		return $this->repository->getExemplaresByCodigo($codigo);
	}

	public function getGroupedInventory(): array
	{
		if (method_exists($this->repository, 'getGroupedInventory')) {
			return $this->repository->getGroupedInventory();
		}

		return [];
	}

	public function getRecordsByCodigo(string $codigo): array
	{
		if (method_exists($this->repository, 'getRecordsByCodigo')) {
			return $this->repository->getRecordsByCodigo($codigo);
		}

		return [];
	}

	public function updateRecordState(int $registro, string $estado): bool
	{
		if (method_exists($this->repository, 'updateRecordState')) {
			return $this->repository->updateRecordState($registro, $estado);
		}

		return false;
	}

	public function countAvailableByCodigo(string $codigo): int
	{
		return $this->repository->countAvailableByCodigo($codigo);
	}

	public function findByRegistro(int $registro): ?array
	{
		return $this->repository->findByRegistro($registro);
	}
}
