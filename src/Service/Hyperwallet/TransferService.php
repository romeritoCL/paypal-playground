<?php

namespace App\Service\Hyperwallet;

use Exception;
use Hyperwallet\Exception\HyperwalletApiException;
use Hyperwallet\Model\Transfer;
use Hyperwallet\Model\TransferStatusTransition;
use Hyperwallet\Response\ListResponse;

/**
 * Class TransferService
 * @package App\Service\Hyperwallet
 */
class TransferService extends AbstractHyperwalletService
{
    /**
     * @return ListResponse
     *
     * @throws HyperwalletApiException
     */
    public function list(): ListResponse
    {
        return $this->client->listTransfers();
    }

    /**
     * @param array $transferDetails
     * @return Transfer | Exception
     */
    public function create(array $transferDetails)
    {
        try {
            $transfer = new Transfer($transferDetails);
            return $this->client->createTransfer($transfer);
        } catch (Exception $exception) {
            return $exception;
        }
    }

    /**
     * @param string $transferToken
     * @return Exception | Transfer
     */
    public function get(string $transferToken)
    {
        try {
            return $this->client->getTransfer($transferToken);
        } catch (Exception $exception) {
            return $exception;
        }
    }

    /**
     * @param string $transferToken
     * @return Exception | TransferStatusTransition
     */
    public function commit(string $transferToken)
    {
        try {
            return $this->client->createTransferStatusTransition(
                $transferToken,
                (new TransferStatusTransition())
                    ->setTransition("SCHEDULED")
            );
        } catch (Exception $exception) {
            return $exception;
        }
    }
}
