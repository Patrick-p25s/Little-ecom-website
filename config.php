<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'secure' => isset($_SERVER['HTTPS']),
        'samesite' => 'Lax',
    ]);
    session_start();
}

define('APP_NAME', 'Ecom Patrick');
define('DATA_DIR', __DIR__ . '/data');

date_default_timezone_set('Europe/Paris');

require_once __DIR__ . '/lib/JsonStorage.php';
require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/lib/auth.php';
