<?php
declare(strict_types=1);

use App\Controllers\SettingsController;
use App\Support\Container;
use App\Support\Logger;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../public/bootstrap.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    /** @var Container $container */

    switch ($uri) {
        case '/index.php':
            if ($method === 'POST') {
                $controller = $container->get(SettingsController::class);
                $controller->showForm();
            }
            break;

        case '/app-settings/update':
            if ($method === 'POST') {
                $controller = $container->get(SettingsController::class);
                $controller->update();
            }
            break;

        case '/test':
            $b24service = $container->get(\App\Services\B24Service::class);
            $b24service->scopeList();
            break;
    }
} catch (Throwable $e) {
    echo $e->getMessage();
}
