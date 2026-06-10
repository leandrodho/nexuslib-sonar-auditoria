<?php

require_once __DIR__ . '/../Repositories/LibraryRepositoryInterface.php';

class ReservationService
{
	private LibraryRepositoryInterface $repository;
	private string $inventoryReserveUrl;
	private string $inventoryReleaseUrl;

	public function __construct(LibraryRepositoryInterface $repository)
	{
		$this->repository = $repository;
		$baseUrl = rtrim((string) (getenv('APP_URL') ?: 'http://localhost/nexuslib'), '/');
		$this->inventoryReserveUrl = $baseUrl . '/inventory-service/public/index.php/internal/reserve';
		$this->inventoryReleaseUrl = $baseUrl . '/inventory-service/public/index.php/internal/release';
	}

	public function reserveBook(ReservedBook $book): int
	{
		$codigo = trim($book->getCodigo());

		if ($codigo === '') {
			throw new \InvalidArgumentException('Missing codigo');
		}

		// First, request the inventory to reserve and obtain the specific registro
		$registro = $this->notifyInventoryReservation($codigo);

		if (!is_int($registro) || $registro <= 0) {
			throw new \RuntimeException('Invalid registro received from inventory');
		}

		// Attach registro to the reservation and persist
		$book->setRegistro($registro);
		$saved = $this->repository->reserveBook($book);

		if (!$saved) {
			// Attempt best-effort rollback in inventory
			try {
				$this->notifyInventoryRelease($registro);
			} catch (\Throwable $e) {
				error_log('ReservationService: failed to rollback inventory after DB error: ' . $e->getMessage());
			}

			throw new \RuntimeException('Failed to persist reservation in database');
		}

		return $registro;
	}

	public function getReservedBooks(string $uuid): array
	{
		return $this->repository->getReservedBooksByUser($uuid);
	}

	public function getAllReservedBooks(): array
	{
		if (method_exists($this->repository, 'getAllReservedBooks')) {
			return $this->repository->getAllReservedBooks();
		}

		return [];
	}

	public function cancelReservation(string $userUuid, int $registro): array
	{
		$deleted = $this->repository->deleteReservation($userUuid, $registro);

		if (!$deleted) {
			return [
				'success' => false,
				'error' => 'No se pudo eliminar la reserva',
			];
		}

		$this->notifyInventoryRelease($registro);

		return [
			'success' => true,
		];
	}

	private function notifyInventoryReservation(string $codigo): int
	{
		$codigo = trim($codigo);

		if ($codigo === '') {
			throw new \InvalidArgumentException('Invalid codigo for inventory reservation');
		}

		$payload = json_encode(['codigo' => $codigo]);
		if ($payload === false) {
			throw new \RuntimeException('Failed to encode payload for inventory reservation');
		}

		$options = [
			'http' => [
				'method' => 'POST',
				'header' => "Content-Type: application/json\r\n" .
					"Accept: application/json\r\n" .
					"Content-Length: " . strlen($payload) . "\r\n",
				'content' => $payload,
				'timeout' => 3,
				'ignore_errors' => true,
			],
		];

		$context = stream_context_create($options);
		$response = @file_get_contents($this->inventoryReserveUrl, false, $context);

		if ($response === false) {
			$error = error_get_last();
			throw new \RuntimeException('Inventory reservation request failed: ' . ($error['message'] ?? 'unknown'));
		}

		$data = json_decode($response, true);
		if (!is_array($data)) {
			throw new \RuntimeException('Invalid response from inventory service');
		}

		if (!empty($data['success']) && isset($data['registro']) && is_int($data['registro'])) {
			return $data['registro'];
		}

		// If inventory returned success with registro as string number, try to coerce
		if (!empty($data['success']) && isset($data['registro'])) {
			$registro = (int) $data['registro'];
			if ($registro > 0) {
				return $registro;
			}
		}

		// No stock or other error
		$msg = $data['error'] ?? 'No stock available';
		throw new \RuntimeException('Inventory reservation failed: ' . $msg);
	}

	private function notifyInventoryRelease(int $registro): void
	{
		if (!is_int($registro) || $registro <= 0) {
			error_log('ReservationService: invalid registro for inventory release sync');
			return;
		}

		$payload = json_encode(['registro' => $registro]);
		if ($payload === false) {
			error_log('ReservationService: failed to encode inventory release payload');
			return;
		}

		$options = [
			'http' => [
				'method' => 'POST',
				'header' => "Content-Type: application/json\r\n" .
					"Accept: application/json\r\n" .
					"Content-Length: " . strlen($payload) . "\r\n",
				'content' => $payload,
				'timeout' => 3,
				'ignore_errors' => true,
			],
		];

		$context = stream_context_create($options);
		$response = @file_get_contents($this->inventoryReleaseUrl, false, $context);

		if ($response === false) {
			$error = error_get_last();
			error_log('ReservationService: inventory release sync failed' . ($error['message'] ?? ''));
		}
	}
}
