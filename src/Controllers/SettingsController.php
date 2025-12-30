<?php
declare(strict_types=1);

namespace App\Controllers;

class SettingsController
{
    public function __construct() { }

    public function showForm(): void
    {
        http_response_code(200);
        require __DIR__ . '/../../views/settings.php';
    }
}