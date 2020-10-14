<?php

namespace App\Service\Hyperwallet;

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
     * AbstractHyperwalletService constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }
}
