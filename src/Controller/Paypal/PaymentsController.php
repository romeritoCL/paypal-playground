<?php

namespace App\Controller\Paypal;

use PayPal\Api\Invoice;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class PaymentsController
 * @package App\Controller\Paypal
 *
 * @Route("/paypal/payments", name="paypal-payments-")
 */
class PaymentsController extends AbstractController
{
    /**
     * @Route("/pay-ins", name="pay-ins", methods={"GET"})
     *
     * @return Response | RedirectResponse
     */
    public function payIns()
    {
        return $this->render('paypal/payments/pay-ins.html.twig');
    }

    /**
     * @Route("/payments/{paymentId}/capture", name="payments-capture", methods={"POST"})
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
     * @Route("/payouts", name="payouts", methods={"GET"})
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
     * @Route("/payouts", name="payouts-create", methods={"POST"})
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
     * @Route("/payouts/{statusId}", name="payouts-refresh", methods={"GET"})
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
     * @Route("/invoices", name="invoices", methods={"GET"})
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
     * @Route("/invoices", name="invoices-create", methods={"POST"})
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
     * @Route("/invoices/{invoiceId}", name="invoices-refresh", methods={"GET"})
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
     * @Route("/subscriptions", name="subscriptions", methods={"GET"})
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
