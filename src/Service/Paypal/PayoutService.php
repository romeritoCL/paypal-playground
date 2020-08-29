<?php

namespace App\Service\Paypal;

use Exception;
use PayPal\Api\Currency;
use PayPal\Api\Payout;
use PayPal\Api\PayoutBatch;
use PayPal\Api\PayoutItem;
use PayPal\Api\PayoutSenderBatchHeader;

/**
 * Class PayoutsService
 * @package App\Service\Paypal
 */
class PayoutService extends AbstractPaypalService
{
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
        $payout = new Payout();
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

        $payout->setSenderBatchHeader($senderBatchHeader)
            ->addItem($senderItem);

        try {
            $payouts = $payout->create(null, $this->apiContext);
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::createPayout = ' . $e->getMessage());
            return null;
        }

        return $payouts;
    }

    /**
     * @param string $payoutId
     * @return PayoutBatch|null
     */
    public function getPayout(string $payoutId): ?PayoutBatch
    {
        $payout = new Payout();
        try {
            $payout = $payout->get($payoutId, $this->apiContext);
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::getPayout = ' . $e->getMessage());
            return null;
        }

        return $payout;
    }
}
