<?php

namespace App\Service\Braintree;

use Braintree\Gateway;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractBraintreeService
 * @package App\Service\Braintree
 */
abstract class AbstractBraintreeService
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Gateway
     */
    protected $gateway;

    /**
     * AbstractBraintreeService constructor.
     * @param string $environment
     * @param string $merchantId
     * @param string $publicKey
     * @param string $privateKey
     * @param LoggerInterface $logger
     */
    public function __construct(
        string $environment,
        string $merchantId,
        string $publicKey,
        string $privateKey,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->gateway = new Gateway([
            'environment' => $environment,
            'merchantId' => $merchantId,
            'publicKey' => $publicKey,
            'privateKey' => $privateKey
        ]);
    }
}
