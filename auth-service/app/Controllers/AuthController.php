<?php

require_once __DIR__ . '/../Services/AuthService.php';

class AuthController
{
	private AuthService $auth;

	public function __construct(AuthService $auth)
	{
		$this->auth = $auth;
	}

	/** Respuesta JSON uniforme */
	private function jsonResponse($data, int $statusCode = 200)
	{
		http_response_code($statusCode);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
		return null;
	}

	public function register()
	{
		$body = json_decode(file_get_contents('php://input'), true) ?: [];
		$name = $body['name'] ?? null;
		$email = $body['email'] ?? null;
		$password = $body['password'] ?? null;

		if (!$name || !$email || !$password) {
			return $this->jsonResponse(['error' => 'Faltan datos requeridos'], 400);
		}

		$ok = $this->auth->register($name, $email, $password);
		if ($ok) {
			return $this->jsonResponse(['success' => true], 200);
		}

		return $this->jsonResponse(['error' => 'No se pudo registrar el usuario'], 400);
	}

	public function login()
	{
		$body = json_decode(file_get_contents('php://input'), true) ?: [];
		$email = $body['email'] ?? null;
		$password = $body['password'] ?? null;

		if (!$email || !$password) {
			return $this->jsonResponse(['error' => 'Faltan credenciales'], 400);
		}

		try {
			$user = $this->auth->login($email, $password);
		} catch (\RuntimeException $e) {
			if ($e->getMessage() === 'email_not_verified') {
				return $this->jsonResponse(['error' => 'email_not_verified'], 403);
			}
			return $this->jsonResponse(['error' => 'Login error'], 500);
		}

		if ($user !== null) {
			// Ensure session contains role (AuthService normally sets session, but double-check here)
			if (session_status() === PHP_SESSION_NONE) session_start();
			$_SESSION['id_user'] = $user->id_user;
			$_SESSION['uuid'] = $user->uuid;
			$_SESSION['name'] = $user->name;
			$_SESSION['email'] = $user->email;
			$_SESSION['role'] = $user->role ?? 'user';

			return $this->jsonResponse([
				'status' => 'success',
				'success' => true,
				'user' => [
					'name' => $user->name,
					'email' => $user->email,
					'uuid' => $user->uuid,
					'role' => $user->role ?? 'user',
				],
			], 200);
		}

		return $this->jsonResponse(['error' => 'Credenciales inválidas'], 401);
	}

	public function verify()
	{
		$token = null;
		// Prefer GET param, fallback to JSON body
		if (!empty($_GET['token'])) {
			$token = trim($_GET['token']);
		} else {
			$body = json_decode(file_get_contents('php://input'), true) ?: [];
			$token = $body['token'] ?? null;
		}

		if (!$token) {
			return $this->jsonResponse(['error' => 'Missing token'], 400);
		}

		$ok = $this->auth->verifyEmail($token);
		if ($ok) {
			return $this->jsonResponse(['success' => true], 200);
		}

		return $this->jsonResponse(['error' => 'Invalid or expired token'], 400);
	}

	public function logout()
	{
		$this->auth->logout();
		return $this->jsonResponse(['success' => true], 200);
	}

	/**
	 * Devuelve el perfil del usuario autenticado.
	 */
	public function getProfile()
	{
		if (session_status() === PHP_SESSION_NONE) session_start();
		$userId = $_SESSION['id_user'] ?? null;
		if (!$userId) {
			return $this->jsonResponse(['error' => 'unauthorized'], 401);
		}

		$user = $this->auth->getProfile((int)$userId);
		if ($user === null) {
			return $this->jsonResponse(['error' => 'user_not_found'], 404);
		}

		return $this->jsonResponse(['success' => true, 'user' => [
			'name' => $user->name,
			'email' => $user->email,
			'uuid' => $user->uuid,
			'id' => $user->id_user,
			'role' => $user->role ?? 'user'
		]], 200);
	}

	/**
	 * Comprueba si el usuario autenticado es admin.
	 */
	public function isAdmin()
	{
		if (session_status() === PHP_SESSION_NONE) session_start();
		$role = $_SESSION['role'] ?? null;
		if ($role === 'admin') {
			return $this->jsonResponse(['is_admin' => true], 200);
		}
		return $this->jsonResponse(['error' => 'forbidden'], 403);
	}

	/**
	 * Actualiza el perfil del usuario autenticado.
	 */
	public function updateProfile()
	{
		if (session_status() === PHP_SESSION_NONE) session_start();
		$userId = $_SESSION['id_user'] ?? null;
		if (!$userId) {
			return $this->jsonResponse(['error' => 'unauthorized'], 401);
		}

		$body = json_decode(file_get_contents('php://input'), true) ?: [];
		$name = $body['name'] ?? null;
		$email = $body['email'] ?? null;

		if ($name === null || $email === null) {
			return $this->jsonResponse(['error' => 'validation_error', 'fields' => ['name' => 'required', 'email' => 'required']], 400);
		}

		// Basic email validation
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return $this->jsonResponse(['error' => 'validation_error', 'fields' => ['email' => 'invalid']], 400);
		}

		try {
			$res = $this->auth->updateProfile((int)$userId, $name, $email);
			return $this->jsonResponse(['success' => true, 'changed' => $res['changed'], 'user' => [
				'name' => $res['user']->name,
				'email' => $res['user']->email,
				'id' => $res['user']->id_user,
				'uuid' => $res['user']->uuid
			]], 200);
		} catch (\RuntimeException $e) {
			if ($e->getMessage() === 'email_in_use') {
				return $this->jsonResponse(['error' => 'email_in_use'], 409);
			}
			return $this->jsonResponse(['error' => 'update_failed'], 500);
		}
	}

	/**
	 * Cambia la contraseña del usuario autenticado.
	 */
	public function changePassword()
	{
		if (session_status() === PHP_SESSION_NONE) session_start();
		$userId = $_SESSION['id_user'] ?? null;
		if (!$userId) {
			return $this->jsonResponse(['error' => 'unauthorized'], 401);
		}

		$body = json_decode(file_get_contents('php://input'), true) ?: [];
		$current = $body['current_password'] ?? null;
		$new = $body['new_password'] ?? null;
		$confirm = $body['confirm_password'] ?? null;

		if (!$current || !$new || !$confirm) {
			return $this->jsonResponse(['error' => 'validation_error', 'fields' => ['current_password' => 'required', 'new_password' => 'required', 'confirm_password' => 'required']], 400);
		}

		if ($new !== $confirm) {
			return $this->jsonResponse(['error' => 'validation_error', 'fields' => ['confirm_password' => 'mismatch']], 400);
		}

		try {
			$this->auth->changePassword((int)$userId, $current, $new);
			return $this->jsonResponse(['success' => true], 200);
		} catch (\RuntimeException $e) {
			$msg = $e->getMessage();
			if ($msg === 'current_password_incorrect') {
				return $this->jsonResponse(['error' => 'current_password_incorrect'], 403);
			}
			if ($msg === 'password_too_weak') {
				return $this->jsonResponse(['error' => 'password_too_weak'], 400);
			}
			return $this->jsonResponse(['error' => 'update_failed'], 500);
		}
	}

	/**
	 * Listar todos los usuarios (solo admin).
	 */
	public function getAllUsers()
	{
		if (session_status() === PHP_SESSION_NONE) session_start();
		$role = $_SESSION['role'] ?? null;
		if ($role !== 'admin') {
			return $this->jsonResponse(['error' => 'forbidden'], 403);
		}

		$rows = $this->auth->getAllUsers();
		return $this->jsonResponse(['success' => true, 'data' => $rows], 200);
	}

	/**
	 * Elimina un usuario (solo admin). Recibe id vía query param `id`.
	 */
	public function deleteUser()
	{
		if (session_status() === PHP_SESSION_NONE) session_start();
		$role = $_SESSION['role'] ?? null;
		if ($role !== 'admin') {
			return $this->jsonResponse(['error' => 'forbidden'], 403);
		}

		// Allow id via path query or JSON body
		$id = null;
		if (isset($_GET['id'])) {
			$id = (int) $_GET['id'];
		} else {
			$body = json_decode(file_get_contents('php://input'), true) ?: [];
			$id = isset($body['id']) ? (int)$body['id'] : null;
		}

		if (!$id) {
			return $this->jsonResponse(['error' => 'missing_id'], 400);
		}

		$ok = $this->auth->deleteUser($id);
		if ($ok) {
			return $this->jsonResponse(['success' => true], 200);
		}

		return $this->jsonResponse(['error' => 'delete_failed'], 500);
	}
}

