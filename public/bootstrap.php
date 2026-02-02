<?php
declare(strict_types=1);

use App\Controllers\SettingsController;
use App\Repositories\ClientRepository;
use App\Services\B24Service;
use App\Services\SettingsService;
use App\Support\Container;
use App\Support\System;
use Bitrix24\SDK\Services\ServiceBuilder;
use Bitrix24\SDK\Services\ServiceBuilderFactory;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$container = new Container();

$webhook = System::getWebhook();

if (empty($webhook)) {
    http_response_code(500);
}

$container->set(ServiceBuilder::class,     fn() => ServiceBuilderFactory::createServiceBuilderFromWebhook($webhook));
$container->set(B24Service::class,         fn() => new B24Service($container->get(ServiceBuilder::class)));
$container->set(ClientRepository::class,   fn() => new ClientRepository());
$container->set(SettingsController::class, fn() => new SettingsController($container->get(SettingsService::class)));
$container->set(SettingsService::class,    fn() => new SettingsService($container->get(ClientRepository::class)));