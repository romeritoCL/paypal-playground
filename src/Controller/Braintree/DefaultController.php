<?php

namespace App\Controller\Braintree;

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
     * @Route("/", name="index", methods={"GET"})
     *
     * @return Response
     */
    public function index()
    {
        return $this->render('braintree/index.html.twig');
    }
}
