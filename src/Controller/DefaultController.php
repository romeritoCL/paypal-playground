<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends AbstractController
{
    public const PAYPAL_FAVICON_URL = 'https://www.paypalobjects.com/webstatic/icon/favicon.ico';
    /**
     * @Route("/favicon.ico", name="favicon")
     *
     * @return RedirectResponse
     */
    public function favicon()
    {
        return $this->redirect(self::PAYPAL_FAVICON_URL);
    }

    /**
     * @Route("/", name="index")
     *
     * @return RedirectResponse|Response
     */
    public function index()
    {
        $request = Request::createFromGlobals();
        return $this->redirectToRoute('anonymous-home', $request->query->all());
    }
}
