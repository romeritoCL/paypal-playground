<?php

namespace App\Service;

use App\Service\Braintree\CustomerService;
use App\Service\Braintree\ForwardApiService;
use App\Service\Braintree\PaymentService;
use App\Service\Braintree\WebhookService;

/**
 * Class BraintreeService
 * @package App\Service
 */
class BraintreeService
{
    /**
     * @var PaymentService
     */
    protected PaymentService $paymentService;

    /**
     * @var CustomerService
     */
    protected CustomerService $customerService;

    /**
     * @var WebhookService
     */
    protected WebhookService $webhookService;

    /**
     * @var ForwardApiService
     */
    protected ForwardApiService $forwardApiService;

    /**
     * PaypalService constructor.
     * @param PaymentService $paymentService
     * @param CustomerService $customerService
     * @param WebhookService $webhookService
     * @param ForwardApiService $forwardApiService
     */
    public function __construct(
        PaymentService $paymentService,
        CustomerService $customerService,
        WebhookService $webhookService,
        ForwardApiService $forwardApiService
    ) {
        $this->paymentService = $paymentService;
        $this->customerService = $customerService;
        $this->webhookService = $webhookService;
        $this->forwardApiService = $forwardApiService;
    }

    /**
     * @return PaymentService
     */
    public function getPaymentService(): PaymentService
    {
        return $this->paymentService;
    }

    /**
     * @return CustomerService
     */
    public function getCustomerService(): CustomerService
    {
        return $this->customerService;
    }

    /**
     * @return WebhookService
     */
    public function getWebhookService(): WebhookService
    {
        return $this->webhookService;
    }

    /**
     * @return ForwardApiService
     */
    public function getForwardApiService(): ForwardApiService
    {
        return $this->forwardApiService;
    }
}
