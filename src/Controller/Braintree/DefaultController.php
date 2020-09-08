<?php

namespace App\Controller\Braintree;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        return $this->render('braintree/payments/payments.html.twig');
    }
}
