<?php

namespace App\Service\Paypal;

use PayPal\Api\Payment;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalHttp\HttpException;
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
     * @return object|array
     */
    public function capturePayment(string $orderId):  object|array
    {
        try {
            $request = new OrdersCaptureRequest($orderId);
            $request->headers["prefer"] = "return=representation";
            $this->addNegativeTestingSetting($request);
            $client = $this->getHttpClient();
            $response = $client->execute($request);
        } catch (HttpException $e) {
            $this->logger->error('Error on PayPal::capturePayment = ' . $e->getMessage());
            return [
                'code' => $e->statusCode,
                'message' => json_decode($e->getMessage()),
                'headers' => $e->headers
            ];
        }

        return $response->result;
    }

    /**
     * @param string $body
     * @param array $headers
     * @return object|array
     */
    public function createOrder(string $body, array $headers = []): object|array
    {
        try {
            $request = new OrdersCreateRequest();
            $request->body = $body;
            $request->headers = array_merge($request->headers, $headers);
            $this->addNegativeTestingSetting($request);
            $client = $this->getHttpClient();
            $response = $client->execute($request);
        } catch (HttpException $e) {
            $this->logger->error('Error on PayPal::createOrder = ' . $e->getMessage());
            return [
                'code' => $e->statusCode,
                'message' => json_decode($e->getMessage()),
                'headers' => $e->headers
            ];
        }

        return $response->result;
    }
}
