<?php

namespace App\Service;

use PayPal\Api\OpenIdUserinfo;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class SessionService
 * @package App\Service
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
     * @param OpenIdUserinfo $userinfo
     * @param string $refreshToken
     */
    public function login(OpenIdUserinfo $userinfo, string $refreshToken): void
    {
        $this->session->start();
        $this->session->set('email', $userinfo->getEmail());
        $this->session->set('name', $userinfo->getName());
        $this->session->set('userId', $userinfo->getUserId());
        $this->session->set('address_street', $userinfo->getAddress()->getStreetAddress());
        $this->session->set('address_locality', $userinfo->getAddress()->getLocality());
        $this->session->set('address_region', $userinfo->getAddress()->getRegion());
        $this->session->set('address_post_code', $userinfo->getAddress()->getPostalCode());
        $this->session->set('address_country', $userinfo->getAddress()->getCountry());
        $this->session->set('refresh-token', $refreshToken);
    }

    /**
     * @param string $refreshToken
     */
    public function refresh(string $refreshToken): void
    {
        $this->session->set('refresh-token', $refreshToken);
    }

    /**
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        return $this->session->get('refresh-token');
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        if ($this->session->get('refresh-token') != null) {
            return true;
        }
        return false;
    }

    /**
     * @param string $clientId
     * @param string $clientSecret
     */
    public function updateCredentials(string $clientId, string $clientSecret): void
    {
        $this->session->clear();
        $this->session->start();
        $this->session->set('PAYPAL_SDK_CLIENT_ID', $clientId);
        $this->session->set('PAYPAL_SDK_CLIENT_SECRET', $clientSecret);
    }
}
