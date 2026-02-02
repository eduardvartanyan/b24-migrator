<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\ClientRepository;
use Bitrix24\SDK\Core\Exceptions\InvalidArgumentException;
use Bitrix24\SDK\Services\ServiceBuilder;
use Bitrix24\SDK\Services\ServiceBuilderFactory;

readonly class SettingsService
{
    public function __construct(private ClientRepository $clientRepository) { }

    /**
     * @throws InvalidArgumentException
     */
    public function updateWebhook(string $domain, string $webhook): bool
    {
        $serviceBuilder = ServiceBuilderFactory::createServiceBuilderFromWebhook($webhook);
        $newB24Service = new B24Service($serviceBuilder);

        if ($newB24Service->checkWebhook()) {
            $this->clientRepository->updateByDomain($domain, ['webhook' => $webhook]);
            return true;
        }

        return false;
    }
}