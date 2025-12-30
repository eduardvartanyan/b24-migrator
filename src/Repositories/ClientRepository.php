<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Support\Database;
use App\Support\Logger;
use PDO;
use PDOException;
use RuntimeException;

class ClientRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    /**
     * @return int — id добавленного клиента
     */
    public function create(array $values): int
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO clients (domain) 
                VALUES (:domain);
            ");
            $stmt->execute([
                ':domain'  => $values['domain'],
            ]);

            return (int) $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new RuntimeException(
                '[ClientRepository->create] Error inserting into clients -> ' . $e->getMessage()
            );
        }
    }

    public function getByDomain(string $domain): ?array
    {
        if ($domain === '') return null;

        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM clients 
                WHERE domain = :domain;
            ");
            $stmt->execute([
                ':domain' => $domain,
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) return null;

            return $result;
        } catch (PDOException $e) {
            throw new RuntimeException(
                '[ClientRepository->getByDomain] Error selecting from clients -> ' . $e->getMessage()
            );
        }
    }

    /**
     * @param string $domain
     * @param array{
     *      webhook: string,
     *  } $values
     * @return bool
     */
    public function updateByDomain(string $domain, array $values): bool
    {
        if ($domain === '') return false;

        $stmt = $this->pdo->prepare("
            SELECT *
            FROM clients
            WHERE domain = :domain
            LIMIT 1;
        ");

        if ($stmt->execute([':domain' => $domain]) === false) return false;

        if ($client = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $stmt = $this->pdo->prepare("
                UPDATE clients
                SET webhook = :webhook
                WHERE domain = :domain;
            ");
            $stmt->execute([
                ':webhook' => $values['webhook'] ?? $client['webhook'],
                ':domain'  => $domain,
            ]);

            return true;
        }

        return false;
    }
}
