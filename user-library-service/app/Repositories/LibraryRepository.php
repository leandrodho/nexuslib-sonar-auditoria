<?php

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Models/SavedBook.php';
require_once __DIR__ . '/../Models/ReservedBook.php';
require_once __DIR__ . '/LibraryRepositoryInterface.php';

class LibraryRepository implements LibraryRepositoryInterface
{
	private PDO $pdo;

	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	public function saveBook(SavedBook $book, ?string $portadaUrl = null): bool
	{
		try {
			$sql = 'INSERT INTO saved_books (user_uuid, codigo, origen, titulo, portada_url) VALUES (:user_uuid, :codigo, :origen, :titulo, :portada_url)';
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue(':user_uuid', $book->getUserUuid(), PDO::PARAM_STR);
			$stmt->bindValue(':codigo', $book->getCodigo(), PDO::PARAM_STR);
			$stmt->bindValue(':origen', $book->getOrigen(), PDO::PARAM_STR);
			$stmt->bindValue(':titulo', $book->getTitulo(), PDO::PARAM_STR);
			$stmt->bindValue(':portada_url', $portadaUrl, $portadaUrl === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
			$ok = $stmt->execute();

			if ($ok) {
				$book->setId((int) $this->pdo->lastInsertId());
			}

			return $ok;
		} catch (PDOException $e) {
			if ($e->getCode() === '23000') {
				return false;
			}

			return false;
		}
	}

	public function isBookSaved(string $userUuid, string $codigo, string $origen): bool
	{
		$sql = 'SELECT COUNT(*) FROM saved_books WHERE user_uuid = :user_uuid AND codigo = :codigo AND origen = :origen';
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':user_uuid', $userUuid, PDO::PARAM_STR);
		$stmt->bindValue(':codigo', $codigo, PDO::PARAM_STR);
		$stmt->bindValue(':origen', $origen, PDO::PARAM_STR);
		$stmt->execute();

		return ((int) $stmt->fetchColumn()) > 0;
	}

	public function removeSavedBook(string $userUuid, string $codigo, string $origen): bool
	{
		try {
			$sql = 'DELETE FROM saved_books WHERE user_uuid = :user_uuid AND codigo = :codigo AND origen = :origen';
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue(':user_uuid', $userUuid, PDO::PARAM_STR);
			$stmt->bindValue(':codigo', $codigo, PDO::PARAM_STR);
			$stmt->bindValue(':origen', $origen, PDO::PARAM_STR);
			$stmt->execute();

			return ($stmt->rowCount() > 0);
		} catch (PDOException $e) {
			return false;
		}
	}

	public function getSavedBooksByUser(string $uuid): array
	{
		$sql = 'SELECT id, user_uuid, codigo, origen, titulo, portada_url, fecha_guardado FROM saved_books WHERE user_uuid = :user_uuid ORDER BY fecha_guardado DESC, id DESC';
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':user_uuid', $uuid, PDO::PARAM_STR);
		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$books = [];

		foreach ($rows as $row) {
			$books[] = new SavedBook($row);
		}

		return $books;
	}

	public function getAllSavedBooks(): array
	{
		$sql = 'SELECT sb.id, sb.user_uuid, sb.codigo, sb.origen, sb.titulo, sb.portada_url, sb.fecha_guardado, a.email as user_email FROM saved_books sb LEFT JOIN accounts a ON sb.user_uuid COLLATE utf8mb4_unicode_ci = a.uuid COLLATE utf8mb4_unicode_ci ORDER BY sb.fecha_guardado DESC, sb.id DESC';
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows === false ? [] : $rows;
	}

	public function reserveBook(ReservedBook $book): bool
	{
		try {
			$sql = 'INSERT INTO reserved_books (user_uuid, codigo, registro, estado) VALUES (:user_uuid, :codigo, :registro, :estado)';
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue(':user_uuid', $book->getUserUuid(), PDO::PARAM_STR);
			$stmt->bindValue(':codigo', $book->getCodigo(), PDO::PARAM_STR);
			if ($book->getRegistro() === null) {
				$stmt->bindValue(':registro', null, PDO::PARAM_NULL);
			} else {
				$stmt->bindValue(':registro', $book->getRegistro(), PDO::PARAM_INT);
			}
			$stmt->bindValue(':estado', $book->getEstado(), PDO::PARAM_STR);
			$ok = $stmt->execute();

			if ($ok) {
				$book->setId((int) $this->pdo->lastInsertId());
			}

			return $ok;
		} catch (PDOException $e) {
			if ($e->getCode() === '23000') {
				return false;
			}

			return false;
		}
	}

	public function getReservedBooksByUser(string $uuid): array
	{
		$sql = 'SELECT id, user_uuid, codigo, registro, estado, fecha_reserva FROM reserved_books WHERE user_uuid = :user_uuid ORDER BY fecha_reserva DESC, id DESC';
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':user_uuid', $uuid, PDO::PARAM_STR);
		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$books = [];

		foreach ($rows as $row) {
			$books[] = new ReservedBook($row);
		}

		return $books;
	}

	public function getAllReservedBooks(): array
	{
		$sql = 'SELECT rb.id, rb.user_uuid, rb.codigo, rb.registro, rb.estado, rb.fecha_reserva, a.email as user_email FROM reserved_books rb LEFT JOIN accounts a ON rb.user_uuid COLLATE utf8mb4_unicode_ci = a.uuid COLLATE utf8mb4_unicode_ci ORDER BY rb.fecha_reserva DESC, rb.id DESC';
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows === false ? [] : $rows;
	}

	public function deleteReservation(string $userUuid, int $registro): bool
	{
		try {
			$sql = 'DELETE FROM reserved_books WHERE user_uuid = :user_uuid AND registro = :registro';
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue(':user_uuid', $userUuid, PDO::PARAM_STR);
			$stmt->bindValue(':registro', $registro, PDO::PARAM_INT);
			$stmt->execute();

			return ($stmt->rowCount() > 0);
		} catch (PDOException $e) {
			return false;
		}
	}

	public function updateReservationState(int $registro, string $estado): bool
	{
		try {
			$sql = 'UPDATE reserved_books SET estado = :estado WHERE registro = :registro';
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
			$stmt->bindValue(':registro', $registro, PDO::PARAM_INT);
			$stmt->execute();

			return ($stmt->rowCount() > 0);
		} catch (PDOException $e) {
			return false;
		}
	}

	public function deleteReservationByRegistry(int $registro): bool
	{
		try {
			$sql = 'DELETE FROM reserved_books WHERE registro = :registro';
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue(':registro', $registro, PDO::PARAM_INT);
			$stmt->execute();

			return ($stmt->rowCount() > 0);
		} catch (PDOException $e) {
			return false;
		}
	}
}
