<?php
declare(strict_types=1);

namespace App\Services;

use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Exceptions\TransportException;
use Bitrix24\SDK\Services\ServiceBuilder;

readonly class B24Service
{
    public function __construct(private ServiceBuilder $b24) { }

    /**
     * @throws TransportException
     * @throws BaseException
     */
    public function scopeList(): void
    {
        $scope = $this->b24->core->call('scope', ['full' => false])->getResponseData()->getResult();
        echo '<pre>'; var_dump($scope);
    }

    public function checkScopeList(): bool
    {
        $scope = $this->b24->core->call('scope', ['full' => false])->getResponseData()->getResult();

        // Todo: Реализовать метод

        return true;
    }
}