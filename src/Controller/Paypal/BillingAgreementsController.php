<?php

namespace App\Controller\Paypal;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BillingAgreementsController
 * @package App\Controller\Paypal
 *
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
}
