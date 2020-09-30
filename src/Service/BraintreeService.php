<?php

namespace App\Service;

use App\Service\Braintree\CustomerService;
use App\Service\Braintree\PaymentService;

/**
 * Class BraintreeService
 * @package App\Service
 */
class BraintreeService
{
    /**
     * @var PaymentService
     */
    protected $paymentService;

    /**
     * @var CustomerService
     */
    protected $customerService;

    /**
     * PaypalService constructor.
     * @param PaymentService $paymentService
     * @param CustomerService $customerService
     */
    public function __construct(
        PaymentService $paymentService,
        CustomerService $customerService
    ) {
        $this->paymentService = $paymentService;
        $this->customerService = $customerService;
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
}
