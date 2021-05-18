<?php

namespace App\Service\Hyperwallet;

use Exception;
use Hyperwallet\Exception\HyperwalletApiException;
use Hyperwallet\Model\Payment;
use Hyperwallet\Response\ListResponse;

/**
 * Class PaymentService
 * @package App\Service\Hyperwallet
 */
class PaymentService extends AbstractHyperwalletService
{
    /**
     * @return ListResponse
     *
     * @throws HyperwalletApiException
     */
    public function list(): ListResponse
    {
        return $this->client->listPayments();
    }

    /**
     * @param array $paymentDetails
     * @return Payment | Exception
     */
    public function create(array $paymentDetails)
    {
        try {
            $payment = new Payment($paymentDetails);
            return $this->client->createPayment($payment);
        } catch (Exception $exception) {
            return $exception;
        }
    }

    /**
     * @param string $paymentToken
     * @return Exception|Payment
     */
    public function get(string $paymentToken)
    {
        try {
            return $this->client->getPayment($paymentToken);
        } catch (Exception $exception) {
            return $exception;
        }
    }
}
