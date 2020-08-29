<?php

namespace App\Service\Paypal;

use PayPal\Api\Payment;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalHttp\HttpResponse;
use Exception;

/**
 * Class PaymentService
 * @package App\Service\Paypal
 */
class PaymentService extends AbstractPaypalService
{
    /**
     * @return PayPalHttpClient
     */
    public function getHttpClient(): PayPalHttpClient
    {
        $sandboxEnvironment = new SandboxEnvironment($this->clientId, $this->clientSecret);
        return new PayPalHttpClient($sandboxEnvironment);
    }

    /**
     * @param string $orderId
     * @return Payment|null
     */
    public function capturePayment(string $orderId): ?HttpResponse
    {
        try {
            $request = new OrdersCaptureRequest($orderId);
            $request->headers["prefer"] = "return=representation";
            $client = $this->getHttpClient();
            $response = $client->execute($request);
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::capturePayment = ' . $e->getMessage());
            return null;
        }

        return $response;
    }
}
