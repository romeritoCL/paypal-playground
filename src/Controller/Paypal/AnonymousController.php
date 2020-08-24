<?php

namespace App\Controller\Paypal;

use App\Service\PaypalService;
use App\Service\SessionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class AnonymousController
 * @package App\Controller\Paypal
 *
 * @Route("/paypal")
 */
class AnonymousController extends AbstractController
{
    /**
     * @var SessionService
     */
    protected $sessionService;

    /**
     * @var PaypalService
     */
    protected $paypalService;

    /**
     * DefaultController constructor.
     * @param SessionService $sessionService
     * @param PaypalService $paypalService
     */
    public function __construct(SessionService $sessionService, PaypalService $paypalService)
    {
        $this->sessionService = $sessionService;
        $this->paypalService = $paypalService;
    }

    /**
     * @Route("/", name="paypal-index")
     *
     * @return RedirectResponse|Response
     */
    public function index()
    {
        $request = Request::createFromGlobals();
        return $this->redirectToRoute('anonymous-home', $request->query->all());
    }

    /**
     * @Route("/anonymous/home", name="anonymous-home")
     *
     * @return RedirectResponse|Response
     */
    public function anonymousHome()
    {
        if ($this->sessionService->isActive()) {
            return $this->redirectToRoute('account');
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
     * @Route("/anonymous/auth-token", name="anonymous-auth-token")
     * @return RedirectResponse|Response
     */
    public function anonymousAuthToken()
    {
        if ($this->sessionService->isActive()) {
            return $this->redirectToRoute('account');
        }
        $request = Request::createFromGlobals();
        $authToken = $request->request->get('auth_token', null);
        if ($authToken) {
            $openIdTokeninfo = $this->paypalService->getAccessTokenFromAuthToken($authToken);
            if ($openIdTokeninfo) {
                return $this->render('paypal/anonymous/access-token.html.twig', [
                    'auth_token' => $authToken,
                    'access_token' => $openIdTokeninfo->getAccessToken(),
                    'refresh_token' => $openIdTokeninfo->getRefreshToken(),
                    'accessTokenObject' => $openIdTokeninfo
                ]);
            }
        }
        return $this->redirectToRoute('anonymous-home');
    }

    /**
     * @Route("/anonymous/user-info", name="anonymous-user-info")
     * @return RedirectResponse|Response
     */
    public function anonymousUserInfo()
    {
        if ($this->sessionService->isActive()) {
            return $this->redirectToRoute('account');
        }
        $request = Request::createFromGlobals();
        $refreshToken = $request->request->get('refresh_token', null);
        if ($refreshToken) {
            $userInfo = $this->paypalService->getUserInfoFromRefreshToken($refreshToken);
            if ($userInfo) {
                $this->sessionService->login($userInfo, $refreshToken);
                return $this->render('paypal/anonymous/user-info.html.twig', [
                    'refresh_token' => $refreshToken,
                    'name' => $userInfo->getName(),
                    'userInfo' => $userInfo
                ]);
            }
        }
        return $this->redirectToRoute('anonymous-home');
    }
}
