<?php

namespace App\Controller\Paypal;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class DefaultController
 * @package App\Controller\Paypal
 *
 * @Route("/paypal", name="paypal-")
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
        return $this->render('paypal/index.html.twig');
    }
}
