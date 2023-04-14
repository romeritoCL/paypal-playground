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
    public function getDataUserIdToken(string $clientId)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api-m.sandbox.paypal.com/v1/oauth2/token');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                "grant_type=client_credentials&response_type=id_token&target_customer_id=" . $clientId
            );
            curl_setopt($ch, CURLOPT_USERPWD, $this->clientId . ':' . $this->clientSecret);
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::getDataUserIdToken = ' . $e->getMessage());
            return null;
        }
        $result = json_decode($result, true);

        if (array_key_exists('id_token', $result)) {
            return $result['id_token'];
        }
        return null;
    }
}
