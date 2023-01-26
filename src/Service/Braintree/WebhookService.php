<?php

namespace App\Service\Braintree;

use Braintree\Exception\InvalidSignature;
use Braintree\WebhookNotification;

/**
 * Class WebhookService
 * @package App\Service\Braintree
 */
class WebhookService extends AbstractBraintreeService
{
    /**
     * @param string $btSignature
     * @param string $btPayload
     * @return bool
     * @throws InvalidSignature
     */
    public function processWebhook(string $btSignature, string $btPayload): bool
    {
        $webhookNotification = $this->gateway->webhookNotification()->parse($btSignature, $btPayload);
        return match ($webhookNotification->kind) {
            WebhookNotification::LOCAL_PAYMENT_COMPLETED =>
            $this->processLocalPaymentMethodWebhook($webhookNotification),
            WebhookNotification::PAYMENT_METHOD_REVOKED_BY_CUSTOMER =>
            $this->processPaymentMethodRevokedByCustomer($webhookNotification),default =>
            $this->processOthers($webhookNotification)
        };
    }

    /**
     * @param string $type
     * @return array
     */
    public function generateTestNotification(string $type): array
    {
        return $this->gateway->webhookTesting()->sampleNotification(
            constant(WebhookNotification::class .'::'. $type),
            sha1(time())
        );
    }

    /**
     * @param WebhookNotification $webhookNotification
     * @return bool
     */
    protected function processLocalPaymentMethodWebhook(WebhookNotification $webhookNotification): bool
    {
        //match the original amount and cart thanks to the $webhookNotification->paymentId;
        $this->gateway->transaction()->sale([
            'amount' => $this->settingsService->getSetting('settings-item-price'),
            'options' => [
                'submitForSettlement' => true,
            ],
            'paymentMethodNonce' => $webhookNotification->subject['localPayment']['paymentMethodNonce']
        ]);

        return true;
    }

    /**
     * @param WebhookNotification $webhookNotification
     * @return bool
     */
    protected function processPaymentMethodRevokedByCustomer(WebhookNotification $webhookNotification): bool
    {
        error_log(print_r($webhookNotification));
        return true;
    }

    /**
     * @param WebhookNotification $webhookNotification
     * @return bool
     */
    protected function processOthers(WebhookNotification $webhookNotification): bool
    {
        error_log(print_r($webhookNotification));
        return true;
    }
}
