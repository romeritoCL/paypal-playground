<?php

namespace App\Service\Paypal;

use Exception;

/**
 * Class BillingAgreementService
 * @package App\Service\Paypal
 */
class BillingAgreementService extends IdentityService
{
    /**
     * @param $requestBody
     * @param string $url
     * @param string $verb
     * @return array|string|null
     */
    public function paypalApiCall($requestBody, string $url, string $verb = 'POST')
    {
        try {
            $accessToken = $this->getAccessToken();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);
            if ($requestBody !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken,
            ]);
            $result = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::'.$url.' = ' . $e->getMessage());
            return null;
        }
        return ([
            'result' => $result,
            'statusCode' => $statusCode
        ]);
    }

    /**
     * @param $requestBody
     * @return bool|string|null
     */
    public function createBillingAgreementToken($requestBody)
    {
        return $this->paypalApiCall(
            $requestBody,
            'https://api.sandbox.paypal.com/v1/billing-agreements/agreement-tokens'
        );
    }

    /**
     * @param $requestBody
     * @return bool|string|null
     */
    public function createBillingAgreement($requestBody)
    {
        return $this->paypalApiCall(
            $requestBody,
            'https://api.sandbox.paypal.com/v1/billing-agreements/agreements'
        );
    }

    /**
     * @param $requestBody
     * @return bool|string|null
     */
    public function createReferenceTransaction($requestBody)
    {
        return $this->paypalApiCall(
            $requestBody,
            'https://api.sandbox.paypal.com/v1/payments/payment'
        );
    }

    /**
     * @param string $billingAgreementId
     * @return bool|string|null
     */
    public function deleteBillingAgreement(string $billingAgreementId)
    {
        return $this->paypalApiCall(
            null,
            'https://api.sandbox.paypal.com/v1/billing-agreements/agreements/' . $billingAgreementId . '/cancel'
        );
    }
}
