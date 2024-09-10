<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AppSecurityUserChecker implements UserCheckerInterface
{

    /**
     * @param UserInterface $user
     * @return void
     */
    final public function checkPreAuth(UserInterface $user): void
    {
        if (!$user->getIsActive()) {
            throw new CustomUserMessageAuthenticationException("Ваш пользователь заблокирован!");
        }
    }

    /**
     * @param UserInterface $user
     * @return void
     */
    final public function checkPostAuth(UserInterface $user): void
    {
    }
}