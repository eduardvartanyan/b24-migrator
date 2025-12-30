<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ClientRepository;
use App\Support\Logger;

readonly class SettingsController
{
    public function __construct(private ClientRepository $clientRepository) { }

    public function showForm(): void
    {
        http_response_code(200);
        require __DIR__ . '/../../views/settings.php';
    }

    public function update(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $domain  = $_REQUEST['domain'] ?? null;
        $webhook = trim($_REQUEST['webhook']) ?? '';

        if (!$domain) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing DOMAIN']);
            return;
        }

        if ($webhook === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Заполните ссылку на вебхук']);
            return;
        }

        $this->clientRepository->updateByDomain($domain, ['webhook' => $webhook,]);

        http_response_code(200);
        echo json_encode(['status' => 'OK']);
    }
}