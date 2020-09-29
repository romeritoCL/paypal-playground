<?php

namespace App\Controller\Braintree;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CustomersController
 *
 * @package App\Controller\Braintree
 *
 * @Route("/braintree/customers", name="braintree-customers-")
 */
class CustomersController extends AbstractController
{
    /**
     * @Route("/", name="list", methods={"GET"})
     *
     * @return Response
     */
    public function listCustomers()
    {
        $customers = $this->braintreeService->getCustomerService()->listCustomers();
        return $this->render('braintree/payments/vault/customer-group-item.html.twig', [
            'customers' => $customers
        ]);
    }

    /**
     * @Route("/", name="create", methods={"POST"})
     *
     * @return Response
     */
    public function createCustomer()
    {
        $request = Request::createFromGlobals();
        $customerData = $request->request->all();
        $customer = $this->braintreeService->getCustomerService()->createCustomer($customerData);
        return $this->render('default/dump.html.twig', [
            'raw_result' => false,
            'result' => $customer
        ]);
    }
}
