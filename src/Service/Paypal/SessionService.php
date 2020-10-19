<?php

namespace App\Service\Paypal;

use PayPal\Api\OpenIdUserinfo;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class SessionService
 * @package App\Service\PayPal
 */
class SessionService
{
    /**
     * @var Session
     */
    public $session;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SessionService constructor.
     * @param SessionInterface $session
     * @param LoggerInterface $logger
     */
    public function __construct(SessionInterface  $session, LoggerInterface $logger)
    {
        $this->session = $session;
        $this->logger = $logger;
    }

    /**
     * LogOut from Connect with PayPal
     */
    public function logout(): void
    {
        $this->session->remove('paypal');
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        if (is_array($this->session->get('paypal'))) {
            return true;
        }
        return false;
    }

    /**
     * @param OpenIdUserinfo $userinfo
     * @param string $refreshToken
     */
    public function login(OpenIdUserinfo $userinfo, string $refreshToken): void
    {
        $paypalUser = [
            'email' => $userinfo->getEmail(),
            'name' => $userinfo->getName(),
            'userId' => $userinfo->getUserId(),
            'address_street' => $userinfo->getAddress()->getStreetAddress(),
            'address_locality' => $userinfo->getAddress()->getLocality(),
            'address_region' => $userinfo->getAddress()->getRegion(),
            'address_post_code' => $userinfo->getAddress()->getPostalCode(),
            'address_country' => $userinfo->getAddress()->getCountry(),
            'refresh-token' => $refreshToken,
        ];

        $this->session->set('paypal', $paypalUser);
    }

    /**
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        return $this->session->get('paypal')['refresh-token'];
    }

    /**
     * @param string $clientId
     * @param string $clientSecret
     */
    public function updateCredentials(string $clientId, string $clientSecret): void
    {
        $this->session->set('PAYPAL_SDK_CLIENT_ID', $clientId);
        $this->session->set('PAYPAL_SDK_CLIENT_SECRET', $clientSecret);
    }
}
