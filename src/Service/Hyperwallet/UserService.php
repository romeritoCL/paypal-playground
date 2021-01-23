<?php

namespace App\Service\Hyperwallet;

use Hyperwallet\Exception\HyperwalletApiException;
use Hyperwallet\Response\ListResponse;

/**
 * Class CustomerService
 * @package App\Service\Hyperwallet
 */
class CustomerService extends AbstractHyperwalletService
{
    /**
     * @return ListResponse
     *
     * @throws HyperwalletApiException
     */
    public function listUsers(): ListResponse
    {
        return $this->client->listUsers([]);
    }
}
