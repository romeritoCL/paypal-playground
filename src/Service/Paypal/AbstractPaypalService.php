<?php

namespace App\Service\Paypal;

use App\Service\SessionService;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractPaypalService
 * @package App\Service\Paypal
 */
abstract class AbstractPaypalService
{
    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var ApiContext
     */
    protected $apiContext;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * PaypalService constructor.
     * @param string $clientId
     * @param string $clientSecret
     * @param LoggerInterface $logger
     * @param SessionService $sessionService
     */
    public function __construct(
        string $clientId,
        string $clientSecret,
        LoggerInterface $logger,
        SessionService $sessionService
    ) {
        $sessionClientId = $sessionService->session->get('PAYPAL_SDK_CLIENT_ID');
        $sessionClientSecret = $sessionService->session->get('PAYPAL_SDK_CLIENT_SECRET');
        $this->clientId = $sessionClientId ?? $clientId;
        $this->clientSecret = $sessionClientSecret ?? $clientSecret;
        $this->logger = $logger;
        $apiContext = new ApiContext(new OAuthTokenCredential($this->clientId, $this->clientSecret));
        $apiContext->setConfig(['mode' => 'sandbox']);
        $this->apiContext = $apiContext;
    }
}
