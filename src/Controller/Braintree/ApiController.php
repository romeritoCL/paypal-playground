<?php

namespace App\Controller\Braintree;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiController
 *
 * @package App\Controller\Braintree
 *
 * @Route("/braintree/api", name="braintree-api-")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('braintree/api/base.html.twig');
    }
}
