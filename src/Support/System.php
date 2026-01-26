<?php

namespace App\Support;

use PDO;

final class System
{
    public static function getWebhook(): ?string
    {
        $identifier = self::resolveClientIdentifier();

        if (!$identifier) {
            return null;
        }

        $client = self::findClient($identifier);
        return $client['webhook'] ?? null;
    }

    public static function resolveClientIdentifier(): ?array
    {
        if (!empty($_REQUEST['DOMAIN'])) {
            return [
                'type' => 'domain',
                'value' => $_REQUEST['DOMAIN'],
            ];
        }

        return null;
    }

    public static function findClient(array $identifier): ?array
    {
        $pdo = Database::pdo();

        $stmt = $pdo->prepare('
            SELECT * 
            FROM clients 
            WHERE domain = :value 
            LIMIT 1
        ');

        $stmt->execute(['value' => $identifier['value']]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}