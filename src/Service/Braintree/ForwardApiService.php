<?php

namespace App\Service\Braintree;

use Exception;

/**
 * Class ForwardApiService
 * @package App\Service\Braintree
 */
class ForwardApiService extends AbstractBraintreeService
{
    /**
     * @var string
     */
    public string $DIRECT_TOKEN_ENDPOINT = "https://forwarding.sandbox.braintreegateway.com/tsp";

    /**
     * @param string $paymentNonce
     * @param string $deviceData
     * @return array|null
     */
    public function directTokenization(string $paymentNonce, string $deviceData): ?array
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->DIRECT_TOKEN_ENDPOINT);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
            curl_setopt(
                $ch,
                CURLOPT_USERPWD,
                $this->gateway->config->getPublicKey() . ':' . $this->gateway->config->getPrivateKey()
            );
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'merchant_id' => $this->gateway->config->getMerchantId(),
                'payment_method_nonce' => $paymentNonce,
                'device_data' => $deviceData,
            ], true));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        } catch (Exception $e) {
            $this->logger->error('Error on ForwardAPI::'.$this->DIRECT_TOKEN_ENDPOINT.' = ' . $e->getMessage());
            return null;
        }
        return ([
            'result' => (object) json_decode($result),
            'statusCode' => $statusCode
        ]);
    }
}
