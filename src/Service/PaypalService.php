<?php

namespace App\Service;

use App\Service\Paypal\BillingAgreementService;
use App\Service\Paypal\IdentityService;
use App\Service\Paypal\InvoiceService;
use App\Service\Paypal\PaymentService;
use App\Service\Paypal\PayoutService;
use App\Service\Paypal\ReportingService;
use App\Service\Paypal\SessionService;
use App\Service\Paypal\VaultService;

/**
 * Class PaypalService
 * @package App\Service
 */
class PaypalService
{
    /**
     * @var IdentityService
     */
    protected $identityService;

    /**
     * @var PaymentService
     */
    protected $paymentService;

    /**
     * @var PayoutService
     */
    protected $payoutService;

    /**
     * @var ReportingService
     */
    protected $reportingService;

    /**
     * @var InvoiceService
     */
    protected $invoiceService;

    /**
     * @var SessionService
     */
    protected $sessionService;

    /**
     * @var BillingAgreementService
     */
    protected $billingAgreementService;

    /**
     * @var VaultService
     */
    protected $vaultService;

    /**
     * PaypalService constructor.
     * @param IdentityService $identityService
     * @param PaymentService $paymentService
     * @param PayoutService $payoutService
     * @param ReportingService $reportingService
     * @param InvoiceService $invoiceService
     * @param SessionService $sessionService
     * @param BillingAgreementService $billingAgreementService
     */
    public function __construct(
        IdentityService $identityService,
        PaymentService $paymentService,
        PayoutService $payoutService,
        ReportingService $reportingService,
        InvoiceService $invoiceService,
        SessionService $sessionService,
        BillingAgreementService $billingAgreementService,
        VaultService $vaultService
    ) {
        $this->identityService = $identityService;
        $this->paymentService = $paymentService;
        $this->payoutService = $payoutService;
        $this->reportingService = $reportingService;
        $this->invoiceService = $invoiceService;
        $this->sessionService = $sessionService;
        $this->billingAgreementService = $billingAgreementService;
        $this->vaultService = $vaultService;
    }

    /**
     * @return IdentityService
     */
    public function getIdentityService(): IdentityService
    {
        return $this->identityService;
    }

    /**
     * @return PaymentService
     */
    public function getPaymentService(): PaymentService
    {
        return $this->paymentService;
    }

    /**
     * @return PayoutService
     */
    public function getPayoutService(): PayoutService
    {
        return $this->payoutService;
    }

    /**
     * @return ReportingService
     */
    public function getReportingService(): ReportingService
    {
        return $this->reportingService;
    }

    /**
     * @return InvoiceService
     */
    public function getInvoiceService(): InvoiceService
    {
        return $this->invoiceService;
    }

    /**
     * @return SessionService
     */
    public function getSessionService(): SessionService
    {
        return $this->sessionService;
    }

    /**
     * @return BillingAgreementService
     */
    public function getBillingAgreementService(): BillingAgreementService
    {
        return $this->billingAgreementService;
    }

    /**
     * @return VaultService
     */
    public function getVaultService(): VaultService
    {
        return $this->vaultService;
    }
}
