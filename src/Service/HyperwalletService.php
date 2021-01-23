<?php

namespace App\Service;

use App\Service\Hyperwallet\UserService;

/**
 * Class HyperwalletService
 * @package App\Service
 */
class HyperwalletService
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * HyperWalletService constructor.
     * @param UserService $userService
     */
    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
    }

    /**
     * @return UserService
     */
    public function getUserService(): UserService
    {
        return $this->userService;
    }
}
