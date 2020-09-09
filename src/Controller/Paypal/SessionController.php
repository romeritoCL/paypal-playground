<?php

namespace App\Controller\Paypal;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class SessionController
 * @package App\Controller\Paypal
 *
 * @Route("/paypal/session", name="paypal-session-")
 */
class SessionController extends AbstractController
{
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
