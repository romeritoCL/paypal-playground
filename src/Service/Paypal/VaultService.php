<?php

namespace App\Service\Paypal;

use Exception;
use PayPal\Api\OpenIdUserinfo;

/**
 * Class VaultService.php
 * @package App\Service\Paypal
 */
class VaultService extends IdentityService
{
    /**
     * @param string $clientId
     * @return string | null
     */
    public function getDataClientToken(string $clientId = 'default_customer')
    {
        try {
            $accessToken = $this->getAccessToken();
            $response = $this->paypalApiCall(
                $accessToken,
                json_encode(["customer_id" => $clientId]),
                "https://api-m.sandbox.paypal.com/v1/identity/generate-token",
                [],
                'POST'
            );
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::getDataClientToken = ' . $e->getMessage());
            return null;
        }
        $result = json_decode($response['result'], true);

        if (array_key_exists('client_token', $result)) {
            return $result['client_token'];
        }
        return null;
    }
}
