<?php

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Models/Resource.php';
require_once __DIR__ . '/InventoryRepositoryInterface.php';

class InventoryRepository implements InventoryRepositoryInterface
{
	private PDO $pdo;

	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	public function searchResources(string $keyword, int $limit = 16, int $offset = 0, string $criterio = '', string $disponibilidad = '', array $temas = []): array
	{
		$keyword = trim($keyword);
		$where = [];
		$params = [];

		// Criterio: titulo, autor, o ambos. Use parameter names unique per position
		if ($criterio === 'titulo') {
			$where[] = 'titulo LIKE :keyword_titulo';
			$params[':keyword_titulo'] = '%' . $keyword . '%';
		} elseif ($criterio === 'autor') {
			$where[] = 'autor LIKE :keyword_autor';
			$params[':keyword_autor'] = '%' . $keyword . '%';
		} else {
			$where[] = '(titulo LIKE :keyword_titulo OR autor LIKE :keyword_autor)';
			$params[':keyword_titulo'] = '%' . $keyword . '%';
			$params[':keyword_autor'] = '%' . $keyword . '%';
		}

		// Disponibilidad
		if ($disponibilidad === 'disponibles' || $disponibilidad === 'available') {
			$where[] = 'estado = :estado';
			$params[':estado'] = 'Disponible';
		}

		// Temas are handled by frontend (injected into q). No DB-level tema filtering required here.

		$whereSql = '';
		if (!empty($where)) {
			$whereSql = 'WHERE ' . implode(' AND ', $where);
		}

		$sql = "SELECT
				registro,
				codigo,
				titulo,
				autor,
				biblioteca,
				tipo,
				procedencia,
				fecha,
				estado
			FROM inventory
			{$whereSql}
			ORDER BY titulo ASC, registro ASC
			LIMIT :limit OFFSET :offset";

		$stmt = $this->pdo->prepare($sql);

		// Bind dynamic params
		foreach ($params as $pname => $pvalue) {
			$stmt->bindValue($pname, $pvalue, PDO::PARAM_STR);
		}

		$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
		$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$resources = [];

		foreach ($rows as $row) {
			$resources[] = $this->hydrateResource($row);
		}

		return $resources;
	}

	public function countSearchResources(string $keyword, string $criterio = '', string $disponibilidad = '', array $temas = []): int
	{
		$keyword = trim($keyword);
		$where = [];
		$params = [];

		if ($criterio === 'titulo') {
			$where[] = 'titulo LIKE :keyword_titulo';
			$params[':keyword_titulo'] = '%' . $keyword . '%';
		} elseif ($criterio === 'autor') {
			$where[] = 'autor LIKE :keyword_autor';
			$params[':keyword_autor'] = '%' . $keyword . '%';
		} else {
			$where[] = '(titulo LIKE :keyword_titulo OR autor LIKE :keyword_autor)';
			$params[':keyword_titulo'] = '%' . $keyword . '%';
			$params[':keyword_autor'] = '%' . $keyword . '%';
		}

		if ($disponibilidad === 'disponibles' || $disponibilidad === 'available') {
			$where[] = 'estado = :estado';
			$params[':estado'] = 'Disponible';
		}

		// Temas are handled by frontend; nothing to add here.

		$whereSql = '';
		if (!empty($where)) {
			$whereSql = 'WHERE ' . implode(' AND ', $where);
		}

		$sql = "SELECT COUNT(*) FROM inventory {$whereSql}";
		$stmt = $this->pdo->prepare($sql);

		foreach ($params as $pname => $pvalue) {
			$stmt->bindValue($pname, $pvalue, PDO::PARAM_STR);
		}

		$stmt->execute();
		$count = $stmt->fetchColumn();

		return $count === false ? 0 : (int) $count;
	}

	public function findByRegistro(int $registro): ?array
	{
		$sql = 'SELECT * FROM inventory WHERE registro = ?';
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([$registro]);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($row === false || $row === null) {
			return null;
		}

		return $row;
	}

	public function getExemplaresByCodigo(string $codigo): array
	{
		$sql = "
			SELECT
				registro,
				codigo,
				titulo,
				autor,
				biblioteca,
				tipo,
				procedencia,
				fecha,
				estado
			FROM inventory
			WHERE codigo = :codigo
			ORDER BY registro ASC
		";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':codigo', $codigo, PDO::PARAM_STR);
		$stmt->execute();

		$rows = $stmt->fetchAll();
		$resources = [];

		foreach ($rows as $row) {
			$resources[] = $this->hydrateResource($row);
		}

		return $resources;
	}

	public function getGroupedInventory(): array
	{
		$sql = "
			SELECT codigo, titulo, autor, COUNT(*) AS total
			FROM inventory
			GROUP BY codigo, titulo, autor
			ORDER BY titulo ASC
		";

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows === false ? [] : $rows;
	}

	public function getRecordsByCodigo(string $codigo): array
	{
		$sql = "
			SELECT registro, codigo, titulo, autor, biblioteca, tipo, procedencia, fecha, estado
			FROM inventory
			WHERE codigo = :codigo
			ORDER BY registro ASC
		";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':codigo', $codigo, PDO::PARAM_STR);
		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows === false ? [] : $rows;
	}

	public function countAvailableByCodigo(string $codigo): int
	{
		$sql = "
			SELECT COUNT(*)
			FROM inventory
			WHERE codigo = :codigo AND estado = 'Disponible'
		";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':codigo', $codigo, PDO::PARAM_STR);
		$stmt->execute();

		$count = $stmt->fetchColumn();

		return $count === false ? 0 : (int) $count;
	}

	public function findFirstAvailableByCodigo(string $codigo): ?int
	{
		$sql = "
			SELECT registro
			FROM inventory
			WHERE codigo = :codigo AND estado = 'Disponible'
			ORDER BY registro ASC
			LIMIT 1
		";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':codigo', $codigo, PDO::PARAM_STR);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($row === false || $row === null) {
			return null;
		}

		return isset($row['registro']) ? (int) $row['registro'] : null;
	}

	public function findFirstReservedByCodigo(string $codigo): ?int
	{
		$sql = "
			SELECT registro
			FROM inventory
			WHERE codigo = :codigo AND estado = 'Reservado'
			ORDER BY registro ASC
			LIMIT 1
		";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':codigo', $codigo, PDO::PARAM_STR);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($row === false || $row === null) {
			return null;
		}

		return isset($row['registro']) ? (int) $row['registro'] : null;
	}

	public function getEstadoByRegistro(int $registro): ?string
	{
		$sql = 'SELECT estado FROM inventory WHERE registro = :registro LIMIT 1';
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':registro', $registro, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($row === false || $row === null) {
			return null;
		}

		return $row['estado'] ?? null;
	}

	public function updateEstadoByRegistro(int $registro, string $estado): bool
	{
		$sql = 'UPDATE inventory SET estado = :estado WHERE registro = :registro';
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
		$stmt->bindValue(':registro', $registro, PDO::PARAM_INT);
		$stmt->execute();

		return ($stmt->rowCount() > 0);
	}

	public function updateRecordState(int $registro, string $estado): bool
	{
		// Reuse the existing updateEstadoByRegistro implementation
		return $this->updateEstadoByRegistro($registro, $estado);
	}

	private function hydrateResource(array $row): Resource
	{
		$resource = new Resource();

		$resource->setRegistro(isset($row['registro']) ? (int) $row['registro'] : null);
		$resource->setCodigo($row['codigo'] ?? null);
		$resource->setTitulo($row['titulo'] ?? '');
		$resource->setAutor($row['autor'] ?? null);
		$resource->setBiblioteca($row['biblioteca'] ?? null);
		$resource->setTipo($row['tipo'] ?? null);
		$resource->setProcedencia($row['procedencia'] ?? null);
		$resource->setFecha($row['fecha'] ?? null);
		$resource->setEstado($row['estado'] ?? 'Disponible');

		return $resource;
	}
}
