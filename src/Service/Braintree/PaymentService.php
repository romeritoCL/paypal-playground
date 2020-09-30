<?php

namespace App\Service\Braintree;

use Braintree\Result\Error;
use Braintree\Result\Successful;
use Exception;

/**
 * Class PaymentService
 * @package App\Service\Braintree
 */
class PaymentService extends AbstractBraintreeService
{
    /**
     * @param string|null $customerId
     * @return string|null
     */
    public function getClientToken(string $customerId = null): ?string
    {
        try {
            if ($customerId) {
                return $this->gateway->clientToken()->generate(['customerId' => $customerId]);
            }
            return $this->gateway->clientToken()->generate();
        } catch (Exception $exception) {
            $this->logger->error(
                'Error on ' . __CLASS__ . '->' . __FUNCTION__ . ': ' . $exception->getMessage()
            );
        }
        return null;
    }

    /**
     * @param $amount
     * @param $paymentNonce
     * @param $deviceDataFromTheClient
     * @return Error|Successful
     */
    public function createSale($amount, $paymentNonce, $deviceDataFromTheClient)
    {
        return $this->gateway->transaction()->sale([
            'amount' => $amount,
            'paymentMethodNonce' => $paymentNonce,
            'deviceData' => $deviceDataFromTheClient,
            'options' => [
                'submitForSettlement' => false
            ]
        ]);
    }

    /**
     * @param $transactionId
     * @param $amount
     * @return Error|Successful
     */
    public function captureSale($transactionId, $amount)
    {
        return $this->gateway->transaction()->submitForSettlement($transactionId, $amount);
    }

    /**
     * @param $transactionId
     * @return \Braintree\Transaction
     * @throws \Braintree\Exception\NotFound
     */
    public function getTransaction($transactionId)
    {
        return $this->gateway->transaction()->find($transactionId);
    }
}
