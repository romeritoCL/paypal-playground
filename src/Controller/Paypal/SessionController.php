<?php

namespace App\Controller\Paypal;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SessionController
 * @package App\Controller\Paypal
 *
 * @Route("/paypal/session", name="paypal-session-")
 */
class SessionController extends AbstractController
{
    /**
     * @Route("/sandbox-credentials", name="sandbox-credentials-create", methods={"POST"})
     *
     * @return RedirectResponse
     */
    public function sandboxCredentialsCreate()
    {
        $request = Request::createFromGlobals();
        $clientId = $request->request->get('client_id', null);
        $clientSecret = $request->request->get('client_secret', null);
        $extra = $request->request->get('extra', null);
        if (($clientId && $clientSecret) || $extra) {
            $this->paypalService->getSessionService()->updateCredentials($clientId, $clientSecret, $extra);
        }
        return $this->redirectToRoute('paypal-index');
    }

    /**
     * @Route("/sandbox-credentials/delete", name="sandbox-credentials-delete", methods={"GET"})
     *
     * @return RedirectResponse
     */
    public function sandboxCredentialsDelete()
    {
        $this->paypalService->getSessionService()->removeCredentials();

        return $this->redirectToRoute('paypal-index');
    }
}
