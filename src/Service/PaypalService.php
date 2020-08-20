<?php

namespace App\Service;

use PayPal\Api\Currency;
use PayPal\Api\OpenIdTokeninfo;
use PayPal\Api\OpenIdUserinfo;
use PayPal\Api\Payout;
use PayPal\Api\PayoutBatch;
use PayPal\Api\PayoutItem;
use PayPal\Api\PayoutSenderBatchHeader;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Psr\Log\LoggerInterface;
use Exception;

/**
 * Class PaypalService
 * @package App\Service
 */
class PaypalService
{
    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var ApiContext
     */
    private $apiContext;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PaypalService constructor.
     * @param string $clientId
     * @param string $clientSecret
     * @param LoggerInterface $logger
     * @param SessionService $sessionService
     */
    public function __construct(
        string $clientId,
        string $clientSecret,
        LoggerInterface $logger,
        SessionService $sessionService
    ) {
        $sesionClientId = $sessionService->session->get('client-id');
        $sesionClientSecret = $sessionService->session->get('client-secret');
        $this->clientId = $sesionClientId ?? $clientId;
        $this->clientSecret = $sesionClientSecret ?? $clientSecret;
        $this->logger = $logger;
        $apiContext = new ApiContext(new OAuthTokenCredential($this->clientId, $this->clientSecret));
        $apiContext->setConfig(['mode' => 'sandbox']);
        $this->apiContext = $apiContext;
    }

    /**
     * @param $authCode
     * @return OpenIdTokeninfo|null
     */
    public function getAccessCodeFromAuthCode($authCode) : ?OpenIdTokeninfo
    {
        try {
            $accessToken = OpenIdTokeninfo::createFromAuthorizationCode(
                ['code' => $authCode],
                $this->clientId,
                $this->clientSecret,
                $this->apiContext
            );
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::getAccessCodeFromAuthCode = ' . $e->getMessage());
            return null;
        }
        return $accessToken;
    }

    /**
     * @param string $refreshToken
     * @return OpenIdTokeninfo|null
     */
    public function refreshToken(string $refreshToken) : ?OpenIdTokeninfo
    {
        try {
            $tokenInfo = new OpenIdTokeninfo();
            $tokenInfo = $tokenInfo->createFromRefreshToken(['refresh_token' => $refreshToken], $this->apiContext);
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::refreshToken = ' . $e->getMessage());
            return null;
        }
        return $tokenInfo;
    }

    /**
     * @param OpenIdTokeninfo $tokenInfo
     * @return OpenIdUserinfo|null
     */
    public function getUserInfo(OpenIdTokeninfo $tokenInfo) : ?OpenIdUserinfo
    {
        try {
            $params = ['access_token' => $tokenInfo->getAccessToken()];
            $userInfo = OpenIdUserinfo::getUserinfo($params, $this->apiContext);
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::getUserInfo = ' . $e->getMessage());
            return null;
        }
        return $userInfo;
    }

    /**
     * @param string $refreshToken
     * @return OpenIdUserinfo|null
     */
    public function getUserInfoFromRefreshToken(string $refreshToken) : ?OpenIdUserinfo
    {
        try {
            $tokenInfo = $this->refreshToken($refreshToken);
            $userInfo = $this->getUserInfo($tokenInfo);
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::getUserInfoFromRefreshToken = ' . $e->getMessage());
            return null;
        }
        return $userInfo;
    }

    /**
     * @param string $refreshToken
     * @return bool|string|null
     */
    public function getUserTransactionsFromRefreshToken(string $refreshToken)
    {
        try {
            $tokenInfo = $this->refreshToken($refreshToken);
            $ch = curl_init();
            curl_setopt(
                $ch,
                CURLOPT_URL,
                "https://api.sandbox.paypal.com/v1/reporting/transactions?start_date=" .
                date('Y-m-d', strtotime('15 days ago')) . 'T00:00:00-0700' .
                "&end_date=" . date('Y-m-d', strtotime('now')) . 'T00:00:00-0700'
            );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $tokenInfo->getAccessToken(),
            ]);
            $userTransactions = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::getUserTransactionsFromRefreshToken = ' . $e->getMessage());
            return null;
        }
        return json_decode($userTransactions);
    }

    /**
     * @param string $subject
     * @param string $note
     * @param string $receiverEmail
     * @param string $itemId
     * @param float $amount
     * @param string $currency
     * @return PayoutBatch|null
     */
    public function createPayout(
        string $subject,
        string $note,
        string $receiverEmail,
        string $itemId,
        float $amount,
        string $currency
    ): ?PayoutBatch {
        $payouts = new Payout();
        $senderBatchHeader = new PayoutSenderBatchHeader();
        $senderBatchHeader
            ->setSenderBatchId(uniqid())
            ->setEmailSubject($subject);
        $senderItem = new PayoutItem();
        $senderItem->setRecipientType('Email')
            ->setNote($note)
            ->setReceiver($receiverEmail)
            ->setSenderItemId($itemId)
            ->setAmount(new Currency(json_encode((object)[
                'value' => $amount,
                'currency' => $currency,
            ])));

        $payouts->setSenderBatchHeader($senderBatchHeader)
            ->addItem($senderItem);

        try {
            $payouts = $payouts->createSynchronous($this->apiContext);
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::createPayout = ' . $e->getMessage());
            return null;
        }

        return $payouts;
    }
}
