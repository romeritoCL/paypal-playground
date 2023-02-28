<?php

namespace App\Service\Braintree;

use Braintree\Result\Error;
use Braintree\Result\Successful;
use Braintree\Transaction;
use Braintree\Exception\NotFound;
use Braintree\TransactionLineItem;
use Exception;

/**
 * Class PaymentService
 * @package App\Service\Braintree
 */
class PaymentService extends AbstractBraintreeService
{
    /**
     * @param string|null $customerId
     * @param string|null $merchantAccountId
     * @return string|null
     */
    public function getClientToken(string $customerId = null, string $merchantAccountId = null): ?string
    {
        try {
            if ($customerId !== null) {
                return $this->gateway->clientToken()->generate(
                    [
                        'customerId' => $customerId,
                        'merchantAccountId' => $merchantAccountId ??
                            $this->settingsService->getSetting('settings-merchant-maid'),
                    ]
                );
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
     * @param $serverOptions
     * @return Error|Successful
     */
    public function createSale($amount, $paymentNonce, $deviceDataFromTheClient, $serverOptions)
    {
        $lineItem = [
            'kind' => TransactionLineItem::DEBIT,
            'name' => $this->settingsService->getSetting('settings-item-name'),
            'description' => $this->settingsService->getSetting('settings-item-description'),
            'productCode' => $this->settingsService->getSetting('settings-item-sku'),
            'totalAmount' => $this->settingsService->getSetting('settings-item-price'),
            'unitAmount' => $this->settingsService->getSetting('settings-item-price'),
            'quantity' => 1
        ];

        $defaultOptions = [
            'merchantAccountId' => $merchantAccountId ??
                $this->settingsService->getSetting('settings-merchant-maid'),
            'lineItems' => [$lineItem],
            'amount' => $amount,
            'paymentMethodNonce' => $paymentNonce,
            'deviceData' => $deviceDataFromTheClient,
            'options' => [
                'submitForSettlement' => false,
            ]
        ];
        $serverOptions = json_decode($serverOptions, true);
        return $this->gateway->transaction()->sale(array_replace_recursive($defaultOptions, $serverOptions));
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
     * @return Transaction
     * @throws NotFound
     */
    public function getTransaction($transactionId)
    {
        return $this->gateway->transaction()->find($transactionId);
    }
}
