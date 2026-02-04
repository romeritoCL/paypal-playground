<?php

namespace App\Controller\Paypal;

use Exception;
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
     * @Route("/ppcp", name="ppcp", methods={"GET"}, defaults={"action" = "ppcp"})
     * @Route("/pay-ins", name="pay-ins", methods={"GET"}, defaults={"action" = "pay-ins"})
     * @Route("/app-switch", name="app-switch", methods={"GET"}, defaults={"action" = "app-switch"})
     * @Route("/pay-outs", name="pay-outs", methods={"GET"}, defaults={"action" = "pay-outs"})
     * @Route("/bopis", name="bopis", methods={"GET"}, defaults={"action" = "bopis"})
     * @Route("/subscriptions", name="subscriptions", methods={"GET"}, defaults={"action" = "subscriptions"})
     * @Route("/invoices", name="invoices", methods={"GET"}, defaults={"action" = "invoices"})
     * @Route("/ecs", name="ecs", methods={"GET"}, defaults={"action" = "ecs"})
     * @Route("/paylater", name="paylater", methods={"GET"}, defaults={"action" = "paylater"})
     * @Route("/msp", name="msp", methods={"GET"}, defaults={"action" = "msp"})
     *
     * @param string $action
     *
     * @return Response | RedirectResponse
     */
    public function payments(string $action)
    {
        return $this->render('paypal/payments/'. $action .'.html.twig');
    }

    /**
     * @Route("/{paymentId}/capture", name="capture", methods={"POST"})
     * @param string $paymentId
     * @return Response | RedirectResponse
     */
    public function capture(string $paymentId)
    {
        $capture = $this->paypalService->getPaymentService()->capturePayment($paymentId);
        return $this->render('default/dump-input-id.html.twig', [
            'raw_result' => false,
            'result' => $capture,
            'result_id' => 'payment-id'
        ]);
    }

    /**
     * @Route("/pay-outs", name="pay-outs-create", methods={"POST"})
     *
     * @return Response | RedirectResponse
     */
    public function payoutsCreate()
    {
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
        return $this->render('default/dump-input-id.html.twig', [
            'raw_result' => false,
            'result' => $payout,
            'result_id' => $payout->getBatchHeader()->getPayoutBatchId()
        ]);
    }

    /**
     * @Route("/pay-outs/{statusId}", name="pay-outs-refresh", methods={"GET"})
     *
     * @param string $statusId
     * @return Response | RedirectResponse
     */
    public function payoutsRefresh(string $statusId)
    {
        $payout = $this->paypalService->getPayoutService()->getPayout($statusId);
        return $this->render('default/dump-input-id.html.twig', [
            'raw_result' => false,
            'result' => $payout,
            'result_id' => $payout->getBatchHeader()->getPayoutBatchId()
        ]);
    }

    /**
     * @Route("/invoices", name="invoices-create", methods={"POST"})
     *
     * @return Response | RedirectResponse
     */
    public function invoicesCreate()
    {
        $request = Request::createFromGlobals();
        $inputForm = $request->request->all();
        $invoice = $this->paypalService->getInvoiceService()->createInvoice($inputForm);
        if ($invoice instanceof Invoice) {
            $this->paypalService->getInvoiceService()->sendInvoice($invoice);
            $invoiceQR = $this->paypalService->getInvoiceService()->getInvoiceQRHTML($invoice);
        }
        if (isset($invoiceQR)) {
            return $this->render('default/dump-input-id.html.twig', [
                'raw_result' => true,
                'result' => $invoiceQR,
                'result_id' => $invoice->getId()
            ]);
        }
        return new JsonResponse('Error creating the Invoice, please check the logs');
    }

    /**
     * @Route("/ppcp", name="ppcp", methods={"GET"}, defaults={"action" = "ppcp"})
     *
     * @param string $action
     * @return Response
     * @throws Exception
     */
    public function ppcp(string $action): Response
    {
        $clientToken = $this->paypalService->getIdentityService()->getClientToken();
        return $this->render(
            'paypal/payments/'. $action .'.html.twig',
            [
                'clientToken' => $clientToken,
            ]
        );
    }

    /**
     * @Route("/invoices/{invoiceId}", name="invoices-refresh", methods={"GET"})
     * @param string $invoiceId
     * @return Response | RedirectResponse
     */
    public function invoicesRefresh(string $invoiceId)
    {
        $invoice = $this->paypalService->getInvoiceService()->getInvoice($invoiceId);
        return $this->render('default/dump-input-id.html.twig', [
            'raw_result' => false,
            'result' => $invoice,
            'result_id' => $invoice->getId()
        ]);
    }

    /**
     * @Route("/{paymentId}/ecs-result", name="ecs-result", methods={"POST"})
     * @param string $paymentId
     * @return Response | RedirectResponse
     */
    public function ecsResult(string $paymentId)
    {
        $capture = $this->paypalService->getPaymentService()->capturePayment($paymentId);
        return $this->render('paypal/payments/ecs-results.twig', [
            'capture' => [
                'payer' => $capture->payer,
                'shipping' => $capture->purchase_units[0]->shipping,
            ],
        ]);
    }

    /**
     * @Route("/create", name="create", methods={"POST"})
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        $request = Request::createFromGlobals();
        $requestBody = $request->getContent();
        $headers = ['PayPal-Request-Id' => time()];
        $order = $this->paypalService->getPaymentService()->createOrder($requestBody, $headers);
        return new JsonResponse($order);
    }
}
