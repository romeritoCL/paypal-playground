<?php

namespace App\Controller\Paypal;

use App\Service\PaypalService;
use App\Service\SessionService;
use PayPal\Api\Invoice;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class AuthenticatedController
 * @package App\Controller\Paypal
 *
 * @Route("/paypal")
 */
class AuthenticatedController extends AbstractController
{
    /**
     * @var SessionService
     */
    protected $sessionService;

    /**
     * @var PaypalService
     */
    protected $paypalService;

    /**
     * DefaultController constructor.
     * @param SessionService $sessionService
     * @param PaypalService $paypalService
     */
    public function __construct(SessionService $sessionService, PaypalService $paypalService)
    {
        $this->sessionService = $sessionService;
        $this->paypalService = $paypalService;
    }

    /**
     * @Route("/logged-in/account", name="account")
     *
     * @return Response | RedirectResponse
     */
    public function account()
    {
        if (!$this->sessionService->isActive()) {
            return $this->redirectToRoute('paypal-index');
        }
        $refreshToken = $this->sessionService->getRefreshToken();
        $myTransactions = $this->paypalService
            ->getReportingService()
            ->getUserTransactionsFromRefreshToken($refreshToken);
        if ($refreshToken !== null) {
            $userInfo = $this->paypalService
                ->getIdentityService()
                ->getUserInfoFromRefreshToken($refreshToken);
            if ($userInfo) {
                return $this->render('paypal/authenticated/account.html.twig', [
                    'name' => $userInfo->getName(),
                    'email' => $userInfo->getEmail(),
                    'userInfo' => $userInfo,
                    'transactions' => $myTransactions,
                ]);
            }
        }
        return $this->redirectToRoute('paypal-index');
    }

    /**
     * @Route("/logged-in/payments", name="payments")
     *
     * @return Response | RedirectResponse
     */
    public function payments()
    {
        if (!$this->sessionService->isActive()) {
            return $this->redirectToRoute('paypal-index');
        }
        return $this->render('paypal/authenticated/payments.html.twig', [
            'PAYPAL_SDK_CLIENT_ID' =>
                $this->sessionService->session->get('PAYPAL_SDK_CLIENT_ID') ??
                $this->getParameter('PAYPAL_SDK_CLIENT_ID'),
        ]);
    }

    /**
     * @Route("/logged-in/payments/capture/{paymentId}", name="payments-capture")
     * @param string $paymentId
     * @return Response | RedirectResponse
     */
    public function paymentsCapture(string $paymentId)
    {
        if (!$this->sessionService->isActive()) {
            return $this->redirectToRoute('paypal-index');
        }
        $capture = $this->paypalService->getPaymentService()->capturePayment($paymentId);
        return $this->render('paypal/authenticated/result.html.twig', [
            'raw_result' => false,
            'result' => $capture,
            'result_id' => 'payment-id'
        ]);
    }

    /**
     * @Route("/logged-in/payouts", name="payouts")
     *
     * @return Response | RedirectResponse
     */
    public function payouts()
    {
        if (!$this->sessionService->isActive()) {
            return $this->redirectToRoute('paypal-index');
        }
        return $this->render('paypal/authenticated/payouts.html.twig');
    }

    /**
     * @Route("/logged-in/payouts/create", name="payouts-create")
     *
     * @return Response | RedirectResponse
     */
    public function payoutsCreate()
    {
        if (!$this->sessionService->isActive()) {
            return $this->redirectToRoute('paypal-index');
        }
        $request = Request::createFromGlobals();
        $subject = $request->request->get('subject', null);
        $note = $request->request->get('note', null);
        $email = $request->request->get('email', null);
        $itemId = $request->request->get('item-id', null);
        $currency = $request->request->get('currency', null);
        $amount = $request->request->get('amount', null);
        $payout = $this->paypalService
            ->getPayoutService()
            ->createPayout($subject, $note, $email, $itemId, $amount, $currency);
        return $this->render('paypal/authenticated/result.html.twig', [
            'raw_result' => false,
            'result' => $payout,
            'result_id' => $payout->getBatchHeader()->getPayoutBatchId()
        ]);
    }

    /**
     * @Route("/logged-in/payouts/{statusId}", name="payouts-refresh")
     * @param string $statusId
     * @return Response | RedirectResponse
     */
    public function payoutsRefresh(string $statusId)
    {
        if (!$this->sessionService->isActive()) {
            return $this->redirectToRoute('paypal-index');
        }
        $payout = $this->paypalService->getPayoutService()->getPayout($statusId);
        return $this->render('paypal/authenticated/result.html.twig', [
            'raw_result' => false,
            'result' => $payout,
            'result_id' => $payout->getBatchHeader()->getPayoutBatchId()
        ]);
    }

    /**
     * @Route("/logged-in/invoices", name="invoices")
     *
     * @return Response | RedirectResponse
     */
    public function invoices()
    {
        if (!$this->sessionService->isActive()) {
            return $this->redirectToRoute('paypal-index');
        }
        return $this->render('paypal/authenticated/invoices.html.twig');
    }

    /**
     * @Route("/logged-in/invoices/create", name="invoices-create")
     *
     * @return Response | RedirectResponse
     */
    public function invoicesCreate()
    {
        if (!$this->sessionService->isActive()) {
            return $this->redirectToRoute('paypal-index');
        }
        $request = Request::createFromGlobals();
        $inputForm = $request->request->all();
        $invoice = $this->paypalService->getInvoiceService()
            ->createInvoice($inputForm);
        if ($invoice instanceof Invoice) {
            $this->paypalService->getInvoiceService()->sendInvoice($invoice);
            $invoiceQR = $this->paypalService->getInvoiceService()
                ->getInvoiceQRHTML($invoice);
        }
        if (isset($invoiceQR)) {
            return $this->render('paypal/authenticated/result.html.twig', [
                'raw_result' => true,
                'result' => $invoiceQR,
                'result_id' => $invoice->getId()
            ]);
        }
        return new JsonResponse('Error creating the Invoice, please check the logs');
    }

    /**
     * @Route("/logged-in/invoices/{invoiceId}", name="invoices-refresh")
     * @param string $invoiceId
     * @return Response | RedirectResponse
     */
    public function invoicesRefresh(string $invoiceId)
    {
        if (!$this->sessionService->isActive()) {
            return $this->redirectToRoute('paypal-index');
        }
        $invoice = $this->paypalService->getInvoiceService()->getInvoice($invoiceId);
        return $this->render('paypal/authenticated/result.html.twig', [
            'raw_result' => false,
            'result' => $invoice,
            'result_id' => $invoice->getId()
        ]);
    }

    /**
     * @Route("/logged-in/subscriptions", name="subscriptions")
     *
     * @return Response | RedirectResponse
     */
    public function subscriptions()
    {
        if (!$this->sessionService->isActive()) {
            return $this->redirectToRoute('paypal-index');
        }
        return $this->render('paypal/authenticated/subscriptions.html.twig');
    }
}
