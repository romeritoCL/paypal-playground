<?php

namespace App\Controller\Braintree;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Exception;

/**
 * Class ApiDumpController
 *
 * @package App\Controller\Braintree
 *
 * @Route("/braintree/api-dump", name="braintree-api-dump-")
 */
class ApiDumpController extends AbstractController
{
    /**
     * @Route("/vault", name="customers-list", methods={"GET"})
     *
     * @return Response
     */
    public function dumpVault()
    {
        $customers = $this->braintreeService->getCustomerService()->listCustomers();
        return $this->render('default/dump.html.twig', [
            'raw_result' => false,
            'result' => $customers
        ]);
    }

    /**
     * @Route("/vault/{customerId}", name="payment-methods-list", methods={"GET"})
     *
     * @return Response
     * @throws \Braintree\Exception\NotFound
     */
    public function dumpCustomerVault($customerId): Response
    {
        $customer = $this->braintreeService->getCustomerService()->getCustomer($customerId);
        return $this->render('default/dump.html.twig', [
            'raw_result' => false,
            'result' => $customer
        ]);
    }
}
