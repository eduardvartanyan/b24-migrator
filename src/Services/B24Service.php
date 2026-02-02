<?php
declare(strict_types=1);

namespace App\Services;

use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Exceptions\TransportException;
use Bitrix24\SDK\Services\ServiceBuilder;
use Throwable;

class B24Service
{
    private array $scopeList = ['catalog', 'crm', 'user'];

    public function __construct(private readonly ServiceBuilder $b24) { }

    public function checkWebhook(): bool
    {
        try {
            $this->b24->core->call('scope', ['full' => false])->getResponseData()->getResult();
        } catch (Throwable $e) {
            return false;
        }

        return true;
    }

    /**
     * @throws TransportException
     * @throws BaseException
     */
    public function checkScopeList(): bool
    {
        $scope = $this->b24->core->call('scope', ['full' => false])->getResponseData()->getResult();

        if (empty(array_diff($this->scopeList, $scope))) {
            return true;
        }

        return false;
    }
}
