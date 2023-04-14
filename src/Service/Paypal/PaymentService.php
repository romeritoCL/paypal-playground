<?php

namespace App\Service\Paypal;

use PayPal\Api\Payment;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
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

    /**
     * @param string $body
     * @param array $headers
     * @return array|string
     */
    public function createOrder(string $body, array $headers = []): object|null
    {
        try {
            $request = new OrdersCreateRequest();
            $request->body = $body;
            $request->headers = array_merge($request->headers, $headers);
            $client = $this->getHttpClient();
            $response = $client->execute($request);
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::createOrder = ' . $e->getMessage());
            return null;
        }

        return $response->result;
    }
}
