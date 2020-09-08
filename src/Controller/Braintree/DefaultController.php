<?php

namespace App\Controller\Braintree;

use App\Service\BraintreeService;
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
}
