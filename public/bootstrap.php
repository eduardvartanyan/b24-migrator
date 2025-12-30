<?php
declare(strict_types=1);

use App\Repositories\ClientRepository;
use App\Support\Container;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$container = new Container();

$container->set(ClientRepository::class,   fn() => new ClientRepository());
