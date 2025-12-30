<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../public/bootstrap.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

echo $method . ': ' . $uri;

try {
    switch ($uri) {
        case '/index.php':
            if ($method === 'POST') {
                echo 'Migrator Hello!';
            }
            break;
    }
} catch (Throwable $e) {
    echo $e->getMessage();
}
