<?php

interface InventoryRepositoryInterface
{
	public function searchResources(string $keyword, int $limit = 16, int $offset = 0, string $criterio = '', string $disponibilidad = '', array $temas = []): array;

	public function countSearchResources(string $keyword, string $criterio = '', string $disponibilidad = '', array $temas = []): int;

	public function getExemplaresByCodigo(string $codigo): array;

	// Devuelve un listado agrupado por codigo con total de ejemplares: codigo, titulo, autor, total
	public function getGroupedInventory(): array;

	// Devuelve todos los ejemplares físicos para un código específico
	public function getRecordsByCodigo(string $codigo): array;

	public function countAvailableByCodigo(string $codigo): int;

	public function findFirstAvailableByCodigo(string $codigo): ?int;

	public function findFirstReservedByCodigo(string $codigo): ?int;

	// Devuelve el estado actual del ejemplar identificado por registro, o null si no existe
	public function getEstadoByRegistro(int $registro): ?string;

	// Actualiza el estado del ejemplar identificado por registro. Devuelve true si se actualizó al menos una fila.
	public function updateEstadoByRegistro(int $registro, string $estado): bool;

	// Actualiza el estado de un registro (registro = id físico)
	public function updateRecordState(int $registro, string $estado): bool;
}
