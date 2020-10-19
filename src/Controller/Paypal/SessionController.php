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
        $this->paypalService->getSessionService()->logout();
        return $this->redirectToRoute('paypal-connect-index');
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
            $this->paypalService->getSessionService()->updateCredentials($clientId, $clientSecret);
        }
        return $this->redirectToRoute('paypal-index');
    }
}
