<?php

namespace App\Service;

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
     * @param string $email
     * @param string $refreshToken
     */
    public function login(string $email, string $refreshToken): void
    {
        $this->session->start();
        $this->session->set('email', $email);
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
        $this->session->set('client-id', $clientId);
        $this->session->set('client-secret', $clientSecret);
    }
}
