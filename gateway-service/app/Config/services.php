<?php

namespace App\Config;

// Mapa de rutas base de microservicios (construido desde APP_URL o localhost por defecto)
$baseUrl = rtrim((string) (getenv('APP_URL') ?: 'http://localhost/nexuslib'), '/');

return [
	'auth' => $baseUrl . '/auth-service/public/index.php/api',
	'inventory' => $baseUrl . '/inventory-service/public/index.php',
	'alpha' => $baseUrl . '/alpha-service/public/index.php',
	'elibro' => $baseUrl . '/elibro-service/public/index.php',
	'user-library' => $baseUrl . '/user-library-service/public/index.php',
];

