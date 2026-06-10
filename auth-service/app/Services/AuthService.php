<?php

require_once __DIR__ . '/../Repositories/UserRepositoryInterface.php';
require_once __DIR__ . '/SessionService.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/MailService.php';

/**
 * Servicio de autenticación que utiliza un UserRepository y SessionService.
 */
class AuthService
{
	private UserRepositoryInterface $repo;
	private SessionService $session;
	private MailService $mailService;

	public function __construct(UserRepositoryInterface $repo, SessionService $session)
	{
		$this->repo = $repo;
		$this->session = $session;
		$this->mailService = new MailService();
	}

	/**
	 * Registra un nuevo usuario.
	 * @param string $name
	 * @param string $email
	 * @param string $password
	 * @return bool
	 */
	public function register(string $name, string $email, string $password): bool
	{
		$hashed = password_hash($password, PASSWORD_BCRYPT);

		$user = new User([
			'name' => $name,
			'email' => $email,
			'password' => $hashed,
		]);

		// Generar token de verificación seguro y asignarlo al usuario
		try {
			$token = bin2hex(random_bytes(32));
		} catch (\Throwable $e) {
			// Fallback: usar openssl si random_bytes no está disponible
			$token = bin2hex(openssl_random_pseudo_bytes(32));
		}

		$user->verification_token = $token;

		$ok = $this->repo->save($user);
		if ($ok) {
			// Enviar correo de verificación usando MailService (PHPMailer)
			try {
				$this->mailService->sendVerificationEmail($user, $token);
			} catch (\Throwable $_) {
				// no-op: no queremos romper el registro si el envío falla
			}

			return $ok;
		}

		return $ok;
	}

	/**
	 * Intenta autenticar al usuario y guarda datos en sesión si tiene éxito.
	 * @param string $email
	 * @param string $password
	 * @return User|null
	 */
	public function login(string $email, string $password): ?User
	{
		$user = $this->repo->findByEmail($email);
		if ($user === null) {
			return null;
		}

		if (!password_verify($password, $user->password)) {
			return null;
		}

		// Bloquear login si la cuenta está inactiva (no verificada)
		if (isset($user->status) && $user->status === 'inactive') {
			throw new \RuntimeException('email_not_verified');
		}

		$this->session->start();
		$this->session->set('id_user', $user->id_user);
		$this->session->set('uuid', $user->uuid);
		$this->session->set('name', $user->name);
		$this->session->set('email', $user->email);
		$this->session->set('role', $user->role);

		return $user;
	}

	/**
	 * Cierra la sesión del usuario.
	 */
	public function logout(): void
	{
		$this->session->destroy();
	}

	/**
	 * Verifica el email a partir de un token.
	 * @param string $token
	 * @return bool
	 */
	public function verifyEmail(string $token): bool
	{
		$user = $this->repo->findByVerificationToken($token);
		if ($user === null) {
			return false;
		}

		$user->status = 'active';
		$user->verification_token = null;

		return $this->repo->update($user);
	}

	/**
	 * Obtiene el perfil del usuario por id.
	 * @param int $userId
	 * @return User|null
	 */
	public function getProfile(int $userId): ?User
	{
		return $this->repo->findById($userId);
	}

	/**
	 * Actualiza el perfil del usuario (name, email). Retorna array con 'changed' boolean y 'user'.
	 * @param int $userId
	 * @param string $name
	 * @param string $email
	 * @return array
	 * @throws \RuntimeException on conflict or validation
	 */
	public function updateProfile(int $userId, string $name, string $email): array
	{
		$user = $this->repo->findById($userId);
		if ($user === null) {
			throw new \RuntimeException('user_not_found');
		}

		$trimName = trim($name);
		$trimEmail = trim($email);

		$changed = false;
		$fields = [];

		if ($trimName !== $user->name) {
			$fields['name'] = $trimName;
			$changed = true;
		}
		if ($trimEmail !== $user->email) {
			// verificar unicidad del email
			$existing = $this->repo->findByEmail($trimEmail);
			if ($existing !== null && $existing->id_user !== $userId) {
				throw new \RuntimeException('email_in_use');
			}
			$fields['email'] = $trimEmail;
			$changed = true;
		}

		if (!$changed) {
			return ['changed' => false, 'user' => $user];
		}

		$updated = $this->repo->updateProfile($userId, $fields);
		if ($updated === null) {
			throw new \RuntimeException('update_failed');
		}

		return ['changed' => true, 'user' => $updated];
	}

	/**
	 * Cambia la contraseña del usuario verificando la contraseña actual.
	 * @param int $userId
	 * @param string $currentPlain
	 * @param string $newPlain
	 * @return void
	 * @throws \RuntimeException
	 */
	public function changePassword(int $userId, string $currentPlain, string $newPlain): void
	{
		$user = $this->repo->findById($userId);
		if ($user === null) {
			throw new \RuntimeException('user_not_found');
		}

		if (!password_verify($currentPlain, $user->password)) {
			throw new \RuntimeException('current_password_incorrect');
		}

		$newHash = password_hash($newPlain, PASSWORD_BCRYPT);
		$ok = $this->repo->updatePassword($userId, $newHash);
		if (!$ok) {
			throw new \RuntimeException('update_failed');
		}
	}

	/**
	 * Devuelve todos los usuarios (para administración).
	 * @return array
	 */
	public function getAllUsers(): array
	{
		return $this->repo->getAllUsers();
	}

	/**
	 * Elimina un usuario por id (hard delete).
	 * @param int $userId
	 * @return bool
	 */
	public function deleteUser(int $userId): bool
	{
		return $this->repo->deleteUser($userId);
	}
}

