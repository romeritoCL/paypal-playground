<?php

namespace App\Controller\Hyperwallet;

use Hyperwallet\Exception\HyperwalletApiException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PaymentsController
 * @package App\Controller\Hyperwallet
 *
 * @Route("/hyperwallet/payments", name="hyperwallet-payments-")
 */
class PaymentsController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('hyperwallet/payments/index.html.twig');
    }

    /**
     * @Route("/create", name="create-get", methods={"GET"})
     *
     * @return Response
     */
    public function createPaymentGet(): Response
    {
        return $this->render('hyperwallet/payments/create.html.twig');
    }

    /**
     * @Route("/create", name="create-post", methods={"POST"})
     *
     * @return Response
     */
    public function createPaymentPost(): Response
    {
        $request = Request::createFromGlobals();
        $paymentDetails = $request->request->all();
        $payment = $this->hyperwalletService->getPaymentservice()->create($paymentDetails);
        return $this->render('default/dump.html.twig', [
            'raw_result' => false,
            'result' => $payment,
        ]);
    }

    /**
     * @Route("/search", name="search", methods={"GET"})
     *
     * @return Response
     * @throws HyperwalletApiException
     */
    public function searchPayment(): Response
    {
        $payments = $this->hyperwalletService->getPaymentService()->list();
        return $this->render('hyperwallet/payments/search.html.twig', [
            'payments' => $payments,
        ]);
    }

    /**
     * @Route("/{paymentToken}", name="get", methods={"GET"})
     *
     * @param string $paymentToken
     *
     * @return Response
     */
    public function getPayment(string $paymentToken): Response
    {
        $payment = $this->hyperwalletService->getPaymentService()->get($paymentToken);
        return $this->render('default/dump.html.twig', [
            'raw_result' => false,
            'result' => $payment,
        ]);
    }
}
