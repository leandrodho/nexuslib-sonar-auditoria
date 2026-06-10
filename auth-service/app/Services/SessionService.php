<?php

/**
 * Servicio simple para manejar sesiones nativas de PHP de forma orientada a objetos.
 */
class SessionService
{
	/** Inicia la sesión si aún no está iniciada */
	public function start(): void
	{
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
	}

	/** Guarda un valor en la sesión */
	public function set(string $key, $value): void
	{
		$this->start();
		$_SESSION[$key] = $value;
	}

	/** Obtiene un valor de la sesión, o devuelve $default si no existe */
	public function get(string $key, $default = null)
	{
		$this->start();
		return $_SESSION[$key] ?? $default;
	}

	/** Destruye la sesión de forma segura */
	public function destroy(): void
	{
		if (session_status() === PHP_SESSION_NONE) {
			return;
		}

		// Limpiar variables de sesión
		$_SESSION = [];

		// Borrar cookie de sesión si aplica
		if (ini_get('session.use_cookies')) {
			$params = session_get_cookie_params();
			setcookie(
				session_name(),
				'',
				time() - 42000,
				$params['path'],
				$params['domain'],
				$params['secure'],
				$params['httponly']
			);
		}

		// Finalmente destruir la sesión
		session_destroy();
	}
}

