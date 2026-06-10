<?php

// Retorna el mapa de rutas -> método del controlador
return [
	'POST /api/login'    => 'login',
	'POST /api/register' => 'register',
	'POST /api/logout'   => 'logout',
	'GET /api/verify'    => 'verify',
	'GET /api/profile'   => 'getProfile',
	'GET /api/admin/users' => 'getAllUsers',
	'DELETE /api/admin/users' => 'deleteUser',
	'GET /api/is-admin'  => 'isAdmin',
	'PUT /api/profile'   => 'updateProfile',
	'PUT /api/change-password' => 'changePassword',
];

