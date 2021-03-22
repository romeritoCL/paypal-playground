<?php

namespace App\Service\Hyperwallet;

use Exception;
use Hyperwallet\Exception\HyperwalletApiException;
use Hyperwallet\Model\User;
use Hyperwallet\Response\ListResponse;
use Hyperwallet\Model\UserStatusTransition;

/**
 * Class UserService
 * @package App\Service\Hyperwallet
 */
class UserService extends AbstractHyperwalletService
{
    /**
     * @return ListResponse
     *
     * @throws HyperwalletApiException
     */
    public function list(): ListResponse
    {
        return $this->client->listUsers([
            'status' => ['ACTIVATED', 'PRE_ACTIVATED'],
        ]);
    }

    /**
     * @param array $userParameters
     * @return User | Exception
     */
    public function create(array $userParameters)
    {
        try {
            $user = new User($userParameters);
            return $this->client->createUser($user);
        } catch (Exception $exception) {
            return $exception;
        }
    }

    /**
     * @param string $userToken
     * @return Exception|User
     */
    public function get(string $userToken)
    {
        try {
            return $this->client->getUser($userToken);
        } catch (Exception $exception) {
            return $exception;
        }
    }
}
