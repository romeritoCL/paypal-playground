<?php

namespace App\Controller\Paypal;

use App\Service\SessionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class SessionController
 * @package App\Controller\Paypal
 *
 * @Route("/paypal")
 */
class SessionController extends AbstractController
{
    /**
     * @var SessionService
     */
    protected $sessionService;

    /**
     * DefaultController constructor.
     * @param SessionService $sessionService
     */
    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        $this->sessionService->session->clear();
        return $this->redirectToRoute('paypal-index');
    }

    /**
     * @Route("/sandbox-credentials", name="sandbox-credentials")
     *
     * @return Response | RedirectResponse
     */
    public function sandboxCredentials()
    {
        $request = Request::createFromGlobals();
        $clientId = $request->request->get('client_id', null);
        $clientSecret = $request->request->get('client_secret', null);
        if ($clientId && $clientSecret) {
            $this->sessionService->updateCredentials($clientId, $clientSecret);
        }
        return $this->redirectToRoute('paypal-index');
    }
}
