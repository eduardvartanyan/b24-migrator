<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ClientRepository;

class SettingsController
{
    public function __construct(private readonly ClientRepository $clientRepository) { }

    public function showForm(): void
    {
        http_response_code(200);
        require __DIR__ . '/../../views/settings.php';
    }
}