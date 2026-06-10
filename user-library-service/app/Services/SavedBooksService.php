<?php

require_once __DIR__ . '/../Repositories/LibraryRepositoryInterface.php';

class SavedBooksService
{
	private LibraryRepositoryInterface $repository;

	public function __construct(LibraryRepositoryInterface $repository)
	{
		$this->repository = $repository;
	}

	public function saveBook(SavedBook $book, ?string $portadaUrl = null): array
	{
		$isSaved = $this->repository->isBookSaved($book->getUserUuid(), $book->getCodigo(), $book->getOrigen());

		if ($isSaved) {
			$removed = $this->repository->removeSavedBook($book->getUserUuid(), $book->getCodigo(), $book->getOrigen());
			if (!$removed) {
				return ['success' => false];
			}

			return ['success' => true, 'action' => 'removed'];
		}

		$added = $this->repository->saveBook($book, $portadaUrl);
		if (!$added) {
			return ['success' => false];
		}

		return ['success' => true, 'action' => 'added'];
	}

	public function getSavedBooks(string $uuid): array
	{
		return $this->repository->getSavedBooksByUser($uuid);
	}

	public function getAllSavedBooks(): array
	{
		if (method_exists($this->repository, 'getAllSavedBooks')) {
			return $this->repository->getAllSavedBooks();
		}

		return [];
	}

	public function isBookSaved(string $userUuid, string $codigo, string $origen): bool
	{
		return $this->repository->isBookSaved($userUuid, $codigo, $origen);
	}
}
