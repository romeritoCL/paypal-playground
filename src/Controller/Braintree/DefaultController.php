<?php

namespace App\Controller\Braintree;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package App\Controller\Paypal
 *
 * @Route("/braintree")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="braintree-index")
     *
     * @return Response
     */
    public function index()
    {
        return $this->render('braintree/index.html.twig');
    }
}
