<?php

namespace App\Controller\Braintree\Vault;

use App\Controller\Braintree\AbstractController;
use Braintree\Exception\NotFound;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VaultController
 *
 * @package App\Controller\Braintree\Vault
 *
 * @Route("/braintree/vault", name="braintree-vault-")
 */
class VaultController extends AbstractController
{
    /**
     * @Route("/", name="customer-list", methods={"GET"})
     *
     * @return Response
     */
    public function listCustomer(): Response
    {
        $customers = $this->braintreeService->getCustomerService()->listCustomers();
        return $this->render('braintree/api/vault/list.html.twig', [
            'customers' => $customers
        ]);
    }

    /**
     * @Route("/vault/{customerId}", name="customer-get", methods={"GET"})
     *
     * @param $customerId
     * @return Response
     * @throws NotFound
     */
    public function getCustomer($customerId): Response
    {
        $customer = $this->braintreeService->getCustomerService()->getCustomer($customerId);
        return $this->render('default/dump.html.twig', [
            'raw_result' => false,
            'result' => $customer
        ]);
    }
}
