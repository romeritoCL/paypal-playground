<?php

namespace App\Controller\Paypal;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VaultController
 * @package App\Controller\Paypal
 *
 * @Route("/paypal/vault", name="paypal-vault-")
 */
class VaultController extends AbstractController
{
    /**
     * @Route("/payment", name="payment", methods={"GET"}, defaults={"action" = "payment"})
     *
     * @param string $action
     *
     * @return Response
     */
    public function vaultPayment(string $action)
    {
        $dataClientToken = $this->paypalService->getVaultService()->getDataClientToken();
        return $this->render('paypal/vault/'. $action .'.html.twig', [
            'dataClientToken' => $dataClientToken,
        ]);
    }
}
