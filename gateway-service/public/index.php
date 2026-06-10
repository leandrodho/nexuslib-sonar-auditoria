<?php

require_once __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json; charset=utf-8');
// Dynamic CORS origin when credentials are allowed
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
	// In production validate $origin against an allowlist
	header('Access-Control-Allow-Origin: ' . $origin);
} else {
	header('Access-Control-Allow-Origin: *');
}
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
// Allow credentials (cookies) to be forwarded when origin is explicit
header('Access-Control-Allow-Credentials: true');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
	http_response_code(204);
	exit;
}

require_once __DIR__ . '/../app/Routes/api.php';