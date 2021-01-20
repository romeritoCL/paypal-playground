<?php

namespace App\Controller\Hyperwallet;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UsersController
 * @package App\Controller\Paypal
 *
 * @Route("/hyperwallet/users", name="hyperwallet-users-")
 */
class UsersController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     *
     * @return Response
     */
    public function index()
    {
        return $this->render('hyperwallet/users/index.html.twig');
    }

    /**
     * @Route("/create", name="create", methods={"GET"})
     *
     * @return Response
     */
    public function create()
    {
        return $this->render('hyperwallet/users/create.html.twig');
    }
}
