
<?php

/**
 * Interface para el patrón Repositorio de usuarios.
 * Mantengo el namespace global para compatibilidad con el resto del proyecto.
 */
interface UserRepositoryInterface
{
	/**
	 * Busca un usuario por su email.
	 * @param string $email
	 * @return User|null
	 */
	public function findByEmail(string $email): ?User;

	/**
	 * Busca un usuario por su id_user (PK autoincrement).
	 * @param int $id_user
	 * @return User|null
	 */
	public function findById(int $id_user): ?User;

	/**
	 * Inserta un nuevo usuario en la base de datos.
	 * Debe usar la función UUID() en MySQL para el campo `uuid`.
	 * @param User $user
	 * @return bool True si se insertó correctamente.
	 */
	public function save(User $user): bool;

	/**
	 * Buscar por token de verificación
	 * @param string $token
	 * @return User|null
	 */
	public function findByVerificationToken(string $token): ?User;

	/**
	 * Actualiza campos de un usuario existente (status, verification_token, etc.)
	 * @param User $user
	 * @return bool
	 */
	public function update(User $user): bool;

	/**
	 * Retorna todos los usuarios (id_user, uuid, name, email, role, status)
	 * @return array
	 */
	public function getAllUsers(): array;

	/**
	 * Borra físicamente un usuario por id_user
	 * @param int $id_user
	 * @return bool
	 */
	public function deleteUser(int $id_user): bool;

	/**
	 * Actualiza campos de perfil modificables (name, email).
	 * @param int $id_user
	 * @param array $fields Associative array with keys 'name' and/or 'email'
	 * @return User|null Updated User object or null on failure
	 */
	public function updateProfile(int $id_user, array $fields): ?User;

	/**
	 * Actualiza la contraseña de un usuario (almacenada en hash).
	 * @param int $id_user
	 * @param string $passwordHash
	 * @return bool
	 */
	public function updatePassword(int $id_user, string $passwordHash): bool;
}

