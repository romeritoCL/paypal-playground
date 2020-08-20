<?php

namespace App\Controller;

use App\Service\PaypalService;
use App\Service\SessionService;
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
     * @Route("/", name="index")
     *
     * @return RedirectResponse|Response
     */
    public function index()
    {
        if ($this->sessionService->isActive()) {
            return $this->redirectToRoute('myAccount');
        }
        $request = Request::createFromGlobals();
        $code = $request->get('code', null);
        if ($code) {
            return $this->render('default/login.html.twig', [
                'auth_code' => $code,
            ]);
        }
        return $this->render('default/index.html.twig', [
            'pp_client_id' => $this->sessionService->session->get('client-id') ?? $this->getParameter('client_id'),
        ]);
    }

    /**
     * @Route("/redeem-auth-code", name="redeemAuthCode")
     * @return RedirectResponse|Response
     */
    public function redeemAuthCode()
    {
        if ($this->sessionService->isActive()) {
            return $this->redirectToRoute('myAccount');
        }
        $request = Request::createFromGlobals();
        $code = $request->request->get('auth_code', null);
        if ($code) {
            $openIdTokeninfo = $this->paypalService->getAccessCodeFromAuthCode($code);
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
     * @return RedirectResponse|Response
     */
    public function getUserInfo()
    {
        if ($this->sessionService->isActive()) {
            return $this->redirectToRoute('myAccount');
        }
        $request = Request::createFromGlobals();
        $refreshToken = $request->request->get('refresh_token', null);
        if ($refreshToken) {
            $userInfo = $this->paypalService->getUserInfoFromRefreshToken($refreshToken);
            if ($userInfo) {
                $this->sessionService->login($userInfo, $refreshToken);
                return $this->render('default/user-info.html.twig', [
                    'refresh_token' => $refreshToken,
                    'name' => $userInfo->getName(),
                    'userInfo' => $userInfo
                ]);
            }
        }
        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/my-account", name="myAccount")
     *
     * @return Response | RedirectResponse
     */
    public function myAccount()
    {
        if (!$this->sessionService->isActive()) {
            return $this->redirectToRoute('index');
        }
        $refreshToken = $this->sessionService->getRefreshToken();
        $myTransactions = $this->paypalService->getUserTransactionsFromRefreshToken($refreshToken);
        if ($refreshToken) {
            $userInfo = $this->paypalService->getUserInfoFromRefreshToken($refreshToken);
            if ($userInfo) {
                return $this->render('default/my-account.html.twig', [
                    'name' => $userInfo->getName(),
                    'email' => $userInfo->getEmail(),
                    'userInfo' => $userInfo,
                    'transactions' => $myTransactions,
                ]);
            }
        }
        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        $this->sessionService->session->clear();
        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/credentials-update", name="credentials-update")
     *
     * @return Response | RedirectResponse
     */
    public function credentialUpdate()
    {
        $request = Request::createFromGlobals();
        $clientId = $request->request->get('client_id', null);
        $clientSecret = $request->request->get('client_secret', null);
        if ($clientId && $clientSecret) {
            $this->sessionService->updateCredentials($clientId, $clientSecret);
        }
        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/make-payments", name="make-payments")
     *
     * @return Response | RedirectResponse
     */
    public function makePayments()
    {
        if (!$this->sessionService->isActive()) {
            return $this->redirectToRoute('index');
        }
        return $this->render('default/make-payments.html.twig', [
            'pp_client_id' =>
                $this->sessionService->session->get('client-id') ?? $this->getParameter('client_id'),
        ]);
    }

    /**
     * @Route("/receive-payments", name="receive-payments")
     *
     * @return Response | RedirectResponse
     */
    public function receivePayments()
    {
        if (!$this->sessionService->isActive()) {
            return $this->redirectToRoute('index');
        }
        return $this->render('default/receive-payments.html.twig', []);
    }

    /**
     * @Route("/receive-payments-result", name="receive-payments-result")
     *
     * @return Response | RedirectResponse
     */
    public function receivePaymentsResult()
    {
        if (!$this->sessionService->isActive()) {
            return $this->redirectToRoute('index');
        }
        //TODO
        //$payout = $this->paypalService->createPayout();
        return $this->render('default/result.html.twig', [
            'result' => [],
        ]);
    }
}
