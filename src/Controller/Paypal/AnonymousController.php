<?php

namespace App\Controller\Paypal;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class AnonymousController
 * @package App\Controller\Paypal
 *
 * @Route("/paypal/anonymous", name="paypal-anonymous-")
 */
class AnonymousController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     *
     * @return RedirectResponse|Response
     */
    public function index()
    {
        if ($this->sessionService->isActive()) {
            return $this->redirectToRoute('paypal-authenticated-index');
        }
        $request = Request::createFromGlobals();
        $authToken = $request->get('code');
        if ($authToken) {
            return $this->render('paypal/anonymous/auth-token.html.twig', [
                'auth_token' => $authToken,
            ]);
        }
        return $this->render('paypal/anonymous/index.html.twig', [
            'PAYPAL_SDK_CLIENT_ID' =>
                $this->sessionService->session->get('PAYPAL_SDK_CLIENT_ID') ??
                $this->getParameter('PAYPAL_SDK_CLIENT_ID'),
        ]);
    }

    /**
     * @Route("/auth-token", name="auth-token", methods={"POST"})
     * @return RedirectResponse|Response
     */
    public function anonymousAuthToken()
    {
        if ($this->sessionService->isActive()) {
            return $this->redirectToRoute('paypal-authenticated-index');
        }
        $request = Request::createFromGlobals();
        $authToken = $request->request->get('auth_token', null);
        if ($authToken) {
            $openIdTokeninfo = $this->paypalService->getIdentityService()->getAccessTokenFromAuthToken($authToken);
            if ($openIdTokeninfo) {
                return $this->render('paypal/anonymous/access-token.html.twig', [
                    'auth_token' => $authToken,
                    'access_token' => $openIdTokeninfo->getAccessToken(),
                    'refresh_token' => $openIdTokeninfo->getRefreshToken(),
                    'accessTokenObject' => $openIdTokeninfo
                ]);
            }
        }
        return $this->redirectToRoute('paypal-anonymous-index');
    }

    /**
     * @Route("/user-info", name="user-info", methods={"POST"})
     * @return RedirectResponse|Response
     */
    public function anonymousUserInfo()
    {
        if ($this->sessionService->isActive()) {
            return $this->redirectToRoute('paypal-authenticated-index');
        }
        $request = Request::createFromGlobals();
        $refreshToken = $request->request->get('refresh_token', null);
        if ($refreshToken) {
            $userInfo = $this->paypalService->getIdentityService()->getUserInfoFromRefreshToken($refreshToken);
            if ($userInfo) {
                $this->sessionService->login($userInfo, $refreshToken);
                return $this->render('paypal/anonymous/user-info.html.twig', [
                    'refresh_token' => $refreshToken,
                    'name' => $userInfo->getName(),
                    'userInfo' => $userInfo
                ]);
            }
        }
        return $this->redirectToRoute('paypal-anonymous-index');
    }
}
