<?php

namespace App\Service\Paypal;

/**
 * Class BillingAgreementService
 * @package App\Service\Paypal
 */
class BillingAgreementService extends IdentityService
{
    /**
     * @param $requestBody
     * @param string $url
     * @param array $inputHeaders
     * @param string $verb
     * @return array|string|null
     */
    public function doPaypalApiCall($requestBody, string $url, array $inputHeaders = [], string $verb = 'POST')
    {
        $accessToken = $this->getAccessToken();
        return $this->paypalApiCall($accessToken, $requestBody, $url, $inputHeaders, $verb);
    }

    /**
     * @param $requestBody
     * @return bool|string|null
     */
    public function createBillingAgreementToken($requestBody)
    {
        return $this->doPaypalApiCall(
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
        return $this->doPaypalApiCall(
            $requestBody,
            'https://api.sandbox.paypal.com/v1/billing-agreements/agreements'
        );
    }

    /**
     * @param $requestBody
     * @param string $fraudnetSession
     * @return bool|string|null
     */
    public function createReferenceTransaction($requestBody, string $fraudnetSession)
    {
        return $this->doPaypalApiCall(
            $requestBody,
            'https://api.sandbox.paypal.com/v1/payments/payment',
            ['PAYPAL-CLIENT-METADATA-ID: '.$fraudnetSession]
        );
    }

    /**
     * @param string $billingAgreementId
     * @return bool|string|null
     */
    public function deleteBillingAgreement(string $billingAgreementId)
    {
        return $this->doPaypalApiCall(
            null,
            'https://api.sandbox.paypal.com/v1/billing-agreements/agreements/' . $billingAgreementId . '/cancel'
        );
    }
}
