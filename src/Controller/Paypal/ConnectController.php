<?php

namespace App\Controller\Paypal;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Exception;

/**
 * Class ConnectController
 * @package App\Controller\Paypal
 *
 * @Route("/paypal/connect", name="paypal-connect-")
 */
class ConnectController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     *
     * @return Response
     */
    public function index()
    {
        if ($this->paypalService->getSessionService()->isActive()) {
            return $this->render('paypal/connect/authenticated.html.twig');
        }

        return $this->render('paypal/connect/authenticate.html.twig');
    }

    /**
     * @Route("/auth-token", name="auth-token", methods={"POST"})
     *
     * @return Response
     */
    public function authToken()
    {
        $request = Request::createFromGlobals();
        $authToken = $request->request->get('auth_token');
        if ($authToken) {
            $openIdTokeninfo = $this->paypalService->getIdentityService()->getAccessTokenFromAuthToken($authToken);
            if ($openIdTokeninfo) {
                return $this->render('default/dump-input-id.html.twig', [
                    'raw_result' => false,
                    'result' => $openIdTokeninfo,
                    'result_id' => $openIdTokeninfo->getRefreshToken()
                ]);
            }
        }
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     * @return RedirectResponse|Response
     */
    public function login()
    {
        $request = Request::createFromGlobals();
        $refreshToken = $request->request->get('refresh_token', null);
        if ($refreshToken) {
            $userInfo = $this->paypalService->getIdentityService()->getUserInfoFromRefreshToken($refreshToken);
            if ($userInfo) {
                $this->paypalService->getSessionService()->login($userInfo, $refreshToken);
                return $this->render('default/dump-input-id.html.twig', [
                    'raw_result' => false,
                    'result' => $userInfo,
                    'result_id' => $userInfo->getEmail()
                ]);
            }
        }
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     *
     * @return RedirectResponse
     */
    public function logout()
    {
        $this->paypalService->getSessionService()->logout();
        return $this->redirectToRoute('paypal-connect-index');
    }

    /**
     * @Route("/transactions", name="transactions", methods={"GET"})
     *
     * @return Response | RedirectResponse
     */
    public function myAccount()
    {
        $refreshToken = $this->paypalService->getSessionService()->getRefreshToken();
        $myTransactions = $this->paypalService
            ->getReportingService()
            ->getUserTransactionsFromRefreshToken($refreshToken);
        if ($myTransactions) {
            return $this->render('default/dump.html.twig', [
                'raw_result' => false,
                'result' => $myTransactions,
            ]);
        }
    }
}
