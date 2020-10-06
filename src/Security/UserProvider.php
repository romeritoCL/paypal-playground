<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider
 * @package App\Security
 */
class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    /**
     * @param string $username
     *
     * @return User|UserInterface
     */
    public function loadUserByUsername(string $username)
    {
        $user = new User();
        $user->setEmail($username);

        return $user;
    }

    /**
     * Refreshes the user after being reloaded from the session.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        if (empty($user->getUsername())) {
            throw new UnsupportedUserException('Unable to find user\'s Username');
        }

        return $user;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass(string $class)
    {
        return User::class === $class;
    }

    /**
     * @param UserInterface $user
     * @param string $newEncodedPassword
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        //Not needed since no password is present;
    }
}
