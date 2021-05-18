<?php

namespace App\Service;

use App\Service\Hyperwallet\PaymentService;
use App\Service\Hyperwallet\TransferService;
use App\Service\Hyperwallet\UserService;

/**
 * Class HyperwalletService
 * @package App\Service
 */
class HyperwalletService
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var PaymentService
     */
    protected $paymentService;

    /**
     * @var TransferService
     */
    protected $transferService;

    /**
     * HyperWalletService constructor.
     * @param UserService $userService
     * @param PaymentService $paymentService
     * @param TransferService $transferService
     */
    public function __construct(
        UserService $userService,
        PaymentService $paymentService,
        TransferService $transferService
    ) {
        $this->userService = $userService;
        $this->paymentService = $paymentService;
        $this->transferService = $transferService;
    }

    /**
     * @return UserService
     */
    public function getUserService(): UserService
    {
        return $this->userService;
    }

    /**
     * @return PaymentService
     */
    public function getPaymentService(): PaymentService
    {
        return $this->paymentService;
    }

    /**
     * @return TransferService
     */
    public function getTransferService(): TransferService
    {
        return $this->transferService;
    }
}
