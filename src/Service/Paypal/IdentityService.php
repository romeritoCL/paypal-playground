<?php

namespace App\Service\Paypal;

use Exception;
use PayPal\Api\OpenIdTokeninfo;
use PayPal\Api\OpenIdUserinfo;

/**
 * Class IdentityService
 * @package App\Service\Paypal
 */
class IdentityService extends AbstractPaypalService
{
    /**
     * getAccessTokenFromAuthToken
     *
     * @param $authToken
     * @return OpenIdTokeninfo|null
     */
    public function getAccessTokenFromAuthToken($authToken) : ?OpenIdTokeninfo
    {
        try {
            $accessToken = OpenIdTokeninfo::createFromAuthorizationCode(
                ['code' => $authToken],
                $this->clientId,
                $this->clientSecret,
                $this->apiContext
            );
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::getAccessTokenFromAuthToken = ' . $e->getMessage());
            return null;
        }
        return $accessToken;
    }

    /**
     * refreshToken
     *
     * @param string $refreshToken
     * @return OpenIdTokeninfo|null
     */
    public function refreshToken(string $refreshToken) : ?OpenIdTokeninfo
    {
        try {
            $tokenInfo = new OpenIdTokeninfo();
            $tokenInfo = $tokenInfo->createFromRefreshToken(['refresh_token' => $refreshToken], $this->apiContext);
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::refreshToken = ' . $e->getMessage());
            return null;
        }
        return $tokenInfo;
    }

    /**
     * @param OpenIdTokeninfo $tokenInfo
     * @return OpenIdUserinfo|null
     */
    public function getUserInfo(OpenIdTokeninfo $tokenInfo) : ?OpenIdUserinfo
    {
        try {
            $params = ['access_token' => $tokenInfo->getAccessToken()];
            $userInfo = OpenIdUserinfo::getUserinfo($params, $this->apiContext);
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::getUserInfo = ' . $e->getMessage());
            return null;
        }
        return $userInfo;
    }

    /**
     * @param string $refreshToken
     * @return OpenIdUserinfo|null
     */
    public function getUserInfoFromRefreshToken(string $refreshToken) : ?OpenIdUserinfo
    {
        try {
            $tokenInfo = $this->refreshToken($refreshToken);
            if ($tokenInfo) {
                $userInfo = $this->getUserInfo($tokenInfo);
            }
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::getUserInfoFromRefreshToken = ' . $e->getMessage());
            return null;
        }
        return $userInfo ?? null;
    }
}
