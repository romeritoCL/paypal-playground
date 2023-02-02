<?php

namespace App\Controller\Braintree;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Exception;

/**
 * Class ForwardApiController
 *
 * @package App\Controller\Braintree
 *
 * @Route("/braintree/forward-api", name="braintree-forward-api-")
 */
class ForwardApiController extends AbstractController
{
    /**
     * @Route("/direct-tokenization", name="direct-tokenization", methods={"POST"})
     *
     * @return Response
     */
    public function index(): Response
    {
        $request = Request::createFromGlobals();
        $paymentNonce = $request->request->get('payment_nonce');
        $deviceData = $request->request->get('device_data');

        $forwardApiResponse = $this->braintreeService->getForwardApiService()->directTokenization(
            $paymentNonce,
            $deviceData
        );

        return $this->render('default/dump.html.twig', [
            'result' => $forwardApiResponse,
            'raw_result' => false,
        ]);
    }
}
