<?php

namespace App\Service\Paypal;

use Exception;
use PayPal\Api\OpenIdTokeninfo;
use PayPal\Api\OpenIdUserinfo;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Core\PayPalConfigManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class IdentityService
 * @package App\Service\Paypal
 */
class IdentityService extends AbstractPaypalService
{
    /**
     * @return string|null
     */
    public function getAccessToken(): ?string
    {
        $credential = new OAuthTokenCredential($this->clientId, $this->clientSecret);
        $config = PayPalConfigManager::getInstance()->getConfigHashmap();
        return $credential->getAccessToken($config);
    }

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

    /**
     *
     * Get Client Token
     *
     * @return string|null
     * @throws Exception
     *
     */
    public function getClientToken(): ?string
    {
        $accessToken = $this->getAccessToken();
        $response = $this->paypalApiCall($accessToken,
            [],
            'https://api.sandbox.paypal.com/v1/identity/generate-token'
        );
        if (array_key_exists('statusCode',$response) && $response['statusCode'] == Response::HTTP_OK) {
            $result = (object) json_decode($response['result'],true);
            return  $result->client_token;
        } else {
            $this->logger->error('Error on PayPal::getClientToken');
            throw new \Exception('Error on PayPal::getClientToken');
        }
    }
}
