
<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/UserRepositoryInterface.php';

/**
 * Implementación del repositorio de usuarios usando PDO.
 */
class UserRepository implements UserRepositoryInterface
{
	private \PDO $pdo;

	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	public function findByEmail(string $email): ?User
	{
		$sql = 'SELECT id_user, uuid, name, email, password, role, status, verification_token, created_at, updated_at FROM accounts WHERE email = :email LIMIT 1';
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([':email' => $email]);
		$row = $stmt->fetch(\PDO::FETCH_ASSOC);

		if (!$row) {
			return null;
		}

		return new User($row);
	}

	public function findById(int $id_user): ?User
	{
		$sql = 'SELECT id_user, uuid, name, email, password, role, status, verification_token, created_at, updated_at FROM accounts WHERE id_user = :id_user LIMIT 1';
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([':id_user' => $id_user]);
		$row = $stmt->fetch(\PDO::FETCH_ASSOC);

		if (!$row) {
			return null;
		}

		return new User($row);
	}

	public function save(User $user): bool
	{
		try {
			$sql = 'INSERT INTO accounts (uuid, name, email, password, verification_token) VALUES (UUID(), :name, :email, :password, :verification_token)';
			$stmt = $this->pdo->prepare($sql);

			$ok = $stmt->execute([
				':name'               => $user->name,
				':email'              => $user->email,
				':password'           => $user->password,
				':verification_token' => $user->verification_token,
			]);

			if ($ok) {
				$lastId = (int)$this->pdo->lastInsertId();
				if ($lastId > 0) {
					$user->id_user = $lastId;
					// Recuperar uuid y timestamps generados por la BD
					$refresh = $this->findById($lastId);
					if ($refresh !== null) {
						$user->uuid = $refresh->uuid;
						$user->created_at = $refresh->created_at;
						$user->updated_at = $refresh->updated_at;
					}
				}
				return true;
			}

			return false;
		} catch (\PDOException $e) {
			return false;
		}
	}

	/**
	 * Buscar un usuario por su token de verificación
	 */
	public function findByVerificationToken(string $token): ?User
	{
		$sql = 'SELECT id_user, uuid, name, email, password, role, status, verification_token, created_at, updated_at FROM accounts WHERE verification_token = :token LIMIT 1';
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([':token' => $token]);
		$row = $stmt->fetch(\PDO::FETCH_ASSOC);

		if (!$row) return null;
		return new User($row);
	}

	/**
	 * Actualiza campos modificables del usuario (status, verification_token)
	 */
	public function update(User $user): bool
	{
		try {
			$sql = 'UPDATE accounts SET status = :status, verification_token = :verification_token, updated_at = CURRENT_TIMESTAMP WHERE id_user = :id_user';
			$stmt = $this->pdo->prepare($sql);
			return (bool) $stmt->execute([
				':status' => $user->status,
				':verification_token' => $user->verification_token,
				':id_user' => $user->id_user,
			]);
		} catch (\PDOException $e) {
			return false;
		}
	}

	/**
	 * Actualiza campos de perfil (name, email) y devuelve el usuario actualizado.
	 * Si no hay campos válidos en $fields, devuelve el usuario actual.
	 */
	public function updateProfile(int $id_user, array $fields): ?User
	{
		try {
			$allowed = ['name', 'email'];
			$sets = [];
			$params = [':id_user' => $id_user];

			foreach ($allowed as $col) {
				if (array_key_exists($col, $fields)) {
					$sets[] = "$col = :$col";
					$params[":$col"] = $fields[$col];
				}
			}

			if (empty($sets)) {
				return $this->findById($id_user);
			}

			$sql = 'UPDATE accounts SET ' . implode(', ', $sets) . ', updated_at = CURRENT_TIMESTAMP WHERE id_user = :id_user';
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute($params);

			return $this->findById($id_user);
		} catch (\PDOException $e) {
			return null;
		}
	}

	/**
	 * Actualiza el hash de la contraseña del usuario.
	 */
	public function updatePassword(int $id_user, string $passwordHash): bool
	{
		try {
			$sql = 'UPDATE accounts SET password = :password, updated_at = CURRENT_TIMESTAMP WHERE id_user = :id_user';
			$stmt = $this->pdo->prepare($sql);
			return (bool) $stmt->execute([
				':password' => $passwordHash,
				':id_user' => $id_user,
			]);
		} catch (\PDOException $e) {
			return false;
		}
	}

	/**
	 * Retorna todos los usuarios en forma de array asociativo.
	 */
	public function getAllUsers(): array
	{
		try {
			$sql = 'SELECT id_user, uuid, name, email, role, status, created_at FROM accounts ORDER BY id_user DESC';
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute();
			$rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			return $rows ?: [];
		} catch (\PDOException $e) {
			return [];
		}
	}

	/**
	 * Borra físicamente un usuario por su id_user.
	 */
	public function deleteUser(int $id_user): bool
	{
		try {
			$sql = 'DELETE FROM accounts WHERE id_user = :id_user';
			$stmt = $this->pdo->prepare($sql);
			return (bool) $stmt->execute([':id_user' => $id_user]);
		} catch (\PDOException $e) {
			return false;
		}
	}
}

