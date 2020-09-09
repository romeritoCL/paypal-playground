<?php

namespace App\Controller\Braintree;

use App\Service\BraintreeService;
use Braintree\Exception\NotFound;
use Braintree\Transaction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package App\Controller\Paypal
 *
 * @Route("/braintree", name="braintree-")
 */
class DefaultController extends AbstractController
{
    /**
     * @var BraintreeService
     */
    protected $braintreeService;

    /**
     * DefaultController constructor.
     * @param BraintreeService $braintreeService
     */
    public function __construct(BraintreeService $braintreeService)
    {
        $this->braintreeService = $braintreeService;
    }

    /**
     * @Route("/", name="index")
     *
     * @return Response
     */
    public function index()
    {
        return $this->render('braintree/index.html.twig');
    }

    /**
     * @Route("/payments", name="payments")
     *
     * @return Response
     */
    public function payments()
    {
        $clientToken = $this->braintreeService->getPaymentService()->getClientToken();
        return $this->render('braintree/payments/payments.html.twig', [
            'clientToken' => $clientToken,
        ]);
    }

    /**
     * @Route("/payments/payload", name="payments-payload")
     *
     * @return Response
     */
    public function paymentsPayload()
    {
        $request = Request::createFromGlobals();
        $payload = $request->request->all();
        return $this->render('default/dump.html.twig', [
            'result' => (object) $payload,
            'raw_result' => false,
        ]);
    }

    /**
     * @Route("/payments/sale", name="payments-sale")
     *
     * @return Response
     */
    public function paymentsSale()
    {
        $request = Request::createFromGlobals();
        $paymentNonce = $request->request->get('payment_nonce');
        $amount = $request->request->get('amount');
        $deviceData = $request->request->get('device_data');
        $sale = $this->braintreeService->getPaymentService()->createSale($amount, $paymentNonce, $deviceData);
        /** @var Transaction $transaction */
        $transaction = $sale->transaction;
        return $this->render('default/dump-input-id.html.twig', [
            'result' => $sale,
            'raw_result' => false,
            'result_id' => $transaction->id
        ]);
    }

    /**
     * @Route("/payments/capture", name="payments-capture")
     *
     * @return Response
     */
    public function paymentsCapture()
    {
        $request = Request::createFromGlobals();
        $transactionId = $request->request->get('transaction_id');
        $amount = $request->request->get('amount');
        $capture = $this->braintreeService->getPaymentService()->captureSale($transactionId, $amount);
        return $this->render('default/dump.html.twig', [
            'result' => (object) $capture,
            'raw_result' => false,
        ]);
    }

    /**
     * @Route("/payments/{transactionId}", name="payments-get-transaction")
     *
     * @param string $transactionId
     * @return Response
     * @throws NotFound
     */
    public function getTransaction(string $transactionId)
    {
        $transaction = $this->braintreeService->getPaymentService()->getTransaction($transactionId);
        return $this->render('default/dump.html.twig', [
            'result' => (object) $transaction,
            'raw_result' => false,
        ]);
    }
}
