<?php

namespace App\Service\Paypal;

use Exception;

/**
 * Class ReportingService
 * @package App\Service\Paypal
 */
class ReportingService extends IdentityService
{
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
                date('Y-m-d', strtotime('5 days ago')) . 'T00:00:00-0700' .
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
}
