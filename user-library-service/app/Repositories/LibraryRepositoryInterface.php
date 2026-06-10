<?php

interface LibraryRepositoryInterface
{
	public function saveBook(SavedBook $book, ?string $portadaUrl = null): bool;

	public function isBookSaved(string $userUuid, string $codigo, string $origen): bool;

	public function removeSavedBook(string $userUuid, string $codigo, string $origen): bool;

	public function getSavedBooksByUser(string $uuid): array;

	// Admin: devuelve todos los saved_books (admin use)
	public function getAllSavedBooks(): array;

	public function reserveBook(ReservedBook $book): bool;

	public function getReservedBooksByUser(string $uuid): array;

	// Admin: devuelve todas las reserved_books con posible email del usuario (LEFT JOIN accounts)
	public function getAllReservedBooks(): array;

	public function deleteReservation(string $userUuid, int $registro): bool;
}
