<?php

namespace App\Controller;

use App\Service\PaypalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $request = Request::createFromGlobals();
        $code = $request->get('code', null);
        if ($code) {
            return $this->render('default/login.html.twig', [
                'auth_code' => $code,
            ]);
        }
        return $this->render('default/index.html.twig', [
            'pp_client_id' => $this->getParameter('client_id'),
        ]);
    }

    /**
     * @Route("/redeem-auth-code", name="redeemAuthCode")
     * @param PaypalService $paypalService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function redeemAuthCode(PaypalService $paypalService)
    {
        $request = Request::createFromGlobals();
        $code = $request->request->get('auth_code', null);
        if ($code) {
            $openIdTokeninfo = $paypalService->getAccessCodeFromAuthCode($code);
            if ($openIdTokeninfo) {
                return $this->render('default/access.html.twig', [
                    'auth_code' => $code,
                    'access_code' => $openIdTokeninfo->getAccessToken(),
                    'refresh_token' => $openIdTokeninfo->getRefreshToken(),
                    'accessTokenObject' => $openIdTokeninfo
                ]);
            }
        }
        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/get-user-info", name="getUserInfo")
     * @param PaypalService $paypalService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function getUserInfo(PaypalService $paypalService)
    {
        $request = Request::createFromGlobals();
        $refreshToken = $request->request->get('refresh_token', null);
        if ($refreshToken) {
            $userInfo = $paypalService->getUserInfoFromRefreshToken($refreshToken);
            if ($userInfo) {
                return $this->render('default/user-info.html.twig', [
                    'refresh_token' => $refreshToken,
                    'name' => $userInfo->getName(),
                    'userInfo' => $userInfo
                ]);
            }
        }
        return $this->redirectToRoute('index');
    }
}
