<?php

namespace App\Service\Hyperwallet;

use Hyperwallet\Hyperwallet;
use Hyperwallet\Exception\HyperwalletArgumentException;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractHyperwalletService
 * @package App\Service\Hyperwallet
 */
abstract class AbstractHyperwalletService
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Hyperwallet
     */
    protected $client;

    /**
     * AbstractHyperwalletService constructor.
     * @param string $user
     * @param string $password
     * @param string $token
     * @param string $url
     * @param LoggerInterface $logger
     *
     * @throws HyperwalletArgumentException
     */
    public function __construct(
        string $user,
        string $password,
        string $token,
        string $url,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->client = new Hyperwallet(
            $user,
            $password,
            $token,
            $url
        );
    }
}
