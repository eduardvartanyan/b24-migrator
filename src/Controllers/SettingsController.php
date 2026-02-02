<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\SettingsService;
use App\Support\Logger;
use Bitrix24\SDK\Core\Exceptions\InvalidArgumentException;

readonly class SettingsController
{
    public function __construct(private SettingsService $settingsService) { }

    public function showForm(): void
    {
        http_response_code(200);
        require __DIR__ . '/../../views/settings.php';
    }

    /**
     * @throws InvalidArgumentException
     */
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

        $result = $this->settingsService->updateWebhook($domain, $webhook);

        if (!$result) {
            http_response_code(400);
            echo json_encode(['error' => 'Не верно указан веб-хук']);
            return;
        }

        http_response_code(200);
        echo json_encode(['status' => 'OK']);
    }
}