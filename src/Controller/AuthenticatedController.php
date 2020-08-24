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
 * Class AuthenticatedController
 * @package App\Controller
 */
class AuthenticatedController extends AbstractController
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
     * @Route("/logged-in/account", name="account")
     *
     * @return Response | RedirectResponse
     */
    public function account()
    {
        if (!$this->sessionService->isActive()) {
            return $this->redirectToRoute('index');
        }
        $refreshToken = $this->sessionService->getRefreshToken();
        $myTransactions = $this->paypalService->getUserTransactionsFromRefreshToken($refreshToken);
        if ($refreshToken) {
            $userInfo = $this->paypalService->getUserInfoFromRefreshToken($refreshToken);
            if ($userInfo) {
                return $this->render('authenticated/account.html.twig', [
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
     * @Route("/logged-in/payments", name="payments")
     *
     * @return Response | RedirectResponse
     */
    public function payments()
    {
        if (!$this->sessionService->isActive()) {
            return $this->redirectToRoute('index');
        }
        return $this->render('authenticated/payments.html.twig', [
            'pp_client_id' =>
                $this->sessionService->session->get('client-id') ?? $this->getParameter('client_id'),
        ]);
    }

    /**
     * @Route("/logged-in/payments/capture/{paymentId}", name="payments-capture")
     * @param string $paymentId
     * @return Response | RedirectResponse
     */
    public function paymentsCapture($paymentId)
    {
        if (!$this->sessionService->isActive()) {
            return $this->redirectToRoute('index');
        }
        $capture = $this->paypalService->capturePayment($paymentId);
        return $this->render('authenticated/result.html.twig', [
            'result' => $capture,
            'result_id' => 'payment-id'
        ]);
    }

    /**
     * @Route("/logged-in/payouts", name="payouts")
     *
     * @return Response | RedirectResponse
     */
    public function payouts()
    {
        if (!$this->sessionService->isActive()) {
            return $this->redirectToRoute('index');
        }
        return $this->render('authenticated/payouts.html.twig');
    }

    /**
     * @Route("/logged-in/payouts/create", name="payouts-create")
     *
     * @return Response | RedirectResponse
     */
    public function payoutsCreate()
    {
        if (!$this->sessionService->isActive()) {
            return $this->redirectToRoute('index');
        }
        $request = Request::createFromGlobals();
        $subject = $request->request->get('subject', null);
        $note = $request->request->get('note', null);
        $email = $request->request->get('email', null);
        $itemId = $request->request->get('item-id', null);
        $currency = $request->request->get('currency', null);
        $amount = $request->request->get('amount', null);
        $payout = $this->paypalService->createPayout($subject, $note, $email, $itemId, $amount, $currency);
        return $this->render('authenticated/result.html.twig', [
            'result' => $payout,
            'result_id' => $payout->getBatchHeader()->getPayoutBatchId()
        ]);
    }

    /**
     * @Route("/logged-in/payouts/{statusId}", name="payouts-refresh")
     * @param string $statusId
     * @return Response | RedirectResponse
     */
    public function payoutsRefresh($statusId)
    {
        if (!$this->sessionService->isActive()) {
            return $this->redirectToRoute('index');
        }
        $payout = $this->paypalService->getPayout($statusId);
        return $this->render('authenticated/result.html.twig', [
            'result' => $payout,
            'result_id' => $payout->getBatchHeader()->getPayoutBatchId()
        ]);
    }
}
