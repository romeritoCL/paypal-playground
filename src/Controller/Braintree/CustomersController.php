<?php

namespace App\Controller\Braintree;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Exception;

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
        $customer instanceof Exception ? $customerId = null : $customerId = $customer->customer->id;
        return $this->render('default/dump-input-id.html.twig', [
            'raw_result' => false,
            'result' => $customer,
            'result_id' => $customerId,
        ]);
    }

    /**
     * @Route("/{customerId}/token", name="get-cutomer-token", methods={"GET"})
     *
     * @param string $customerId
     * @return Response
     */
    public function getCustomerToken(string $customerId)
    {
        $clientToken = $this->braintreeService->getPaymentService()->getClientToken($customerId);

        return new Response($clientToken);
    }
}
