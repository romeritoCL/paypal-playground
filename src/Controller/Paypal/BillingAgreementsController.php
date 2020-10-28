<?php

namespace App\Controller\Paypal;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BillingAgreementsController
 * @package App\Controller\Paypal
 * @Route("/paypal/billing-agreements", name="paypal-billing-agreements-")
 */
class BillingAgreementsController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @return Response
     */
    public function index()
    {
        return $this->render('paypal/billingAgreements/base.html.twig');
    }

    /**
     * @Route("/token", name="token-create", methods={"POST"})
     * @return JsonResponse
     */
    public function tokenCreate()
    {
        $request = Request::createFromGlobals();
        $requestBody = $request->getContent();
        $response = $this->paypalService->getBillingAgreementService()->createBillingAgreementToken($requestBody);
        return new JsonResponse(
            $response['result']
        );
    }

    /**
     * @Route("/", name="create", methods={"POST"})
     * @return JsonResponse
     */
    public function billingAgreementCreate()
    {
        $request = Request::createFromGlobals();
        $requestBody = $request->getContent();
        $response = $this->paypalService->getBillingAgreementService()->createBillingAgreement($requestBody);
        return new JsonResponse(
            $response['result']
        );
    }

    /**
     * @Route("/{billingAgreementId}", name="delete", methods={"DELETE"})
     * @param string $billingAgreementId
     * @return JsonResponse
     */
    public function billingAgreementDelete(string $billingAgreementId)
    {
        $response = $this->paypalService->getBillingAgreementService()->deleteBillingAgreement($billingAgreementId);
        return new JsonResponse(
            $response['result']
        );
    }

    /**
     * @Route("/reference-trasaction", name="reference-transaction", methods={"POST"})
     * @return JsonResponse
     */
    public function referenceTransaction()
    {
        $request = Request::createFromGlobals();
        $requestBody = $request->getContent();
        $response = $this->paypalService->getBillingAgreementService()->createReferenceTransaction($requestBody);
        return new JsonResponse(
            $response['result']
        );
    }
}
