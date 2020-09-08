<?php

namespace App\Service;

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
     * PaypalService constructor.
     * @param PaymentService $paymentService
     */
    public function __construct(
        PaymentService $paymentService
    ) {
        $this->paymentService = $paymentService;
    }

    /**
     * @return PaymentService
     */
    public function getPaymentService(): PaymentService
    {
        return $this->paymentService;
    }
}
