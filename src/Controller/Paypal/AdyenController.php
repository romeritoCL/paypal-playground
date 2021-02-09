<?php

namespace App\Controller\Paypal;

use Adyen\AdyenException;
use App\Service\AdyenService;
use App\Service\PaypalService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class AdyenController
 * @package App\Controller\Paypal
 *
 * @Route("/paypal/adyen", name="paypal-adyen-")
 */
class AdyenController extends AbstractController
{
    /**
     * @var AdyenService
     */
    protected $adyenService;

    /**
     * AdyenController constructor.
     * @param PaypalService $paypalService
     * @param AdyenService $adyenService
     */
    public function __construct(PaypalService $paypalService, AdyenService $adyenService)
    {
        $this->adyenService = $adyenService;
        parent::__construct($paypalService);
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     * @return Response | RedirectResponse
     * @throws AdyenException
     */
    public function index()
    {
        $paymentMethods = $this->adyenService->getPaymentMethods();
        $clientKey = $this->adyenService->getClientKey();
        $paypalId = $this->adyenService->getPayPalId();
        return $this->render('paypal/adyen/index.html.twig', [
            'paymentMethods' => $paymentMethods,
            'clientKey' => $clientKey,
            'paypalId' => $paypalId,
        ]);
    }

    /**
     * @Route("/make-payment", name="make-payment", methods={"POST"})
     * @return JsonResponse
     * @throws AdyenException
     */
    public function paymentMake()
    {
        $request = Request::createFromGlobals();
        $paymentData = $request->request->all();
        $action = $this->adyenService->makePayment($paymentData);

        return new JsonResponse($action);
    }

    /**
     * @Route("/payment-details", name="payment-details", methods={"POST"})
     * @return Response
     * @throws AdyenException
     */
    public function paymentDetails()
    {
        $request = Request::createFromGlobals();
        $paymentData = $request->request->all();
        $paymentDetails = $this->adyenService->paymentDetails($paymentData);

        return $this->render('default/dump-input-id.html.twig', [
            'result' => $paymentDetails,
            'raw_result' => false,
            'result_id' => $paymentDetails['pspReference'],
        ]);
    }

    /**
     * @Route("/payment-capture", name="payment-capture", methods={"POST"})
     * @return Response
     * @throws AdyenException
     */
    public function paymentCapture()
    {
        $request = Request::createFromGlobals();
        $paymentData = $request->request->all();
        $captureDetails = $this->adyenService->paymentCapture($paymentData);

        return $this->render('default/dump.html.twig', [
            'result' => $captureDetails,
            'raw_result' => false,
        ]);
    }
}
