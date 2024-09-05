<?php
/**
 * User: demius
 * Date: 21.01.2024
 * Time: 22:45
 */

namespace App\EventSubscriber;

use App\Entity\User;
use App\Model\Enum\Security\UserRolesEnum;

trait CurrentUserTrait
{
    protected function isServiceUser(): bool
    {
        return !$this->security->getUser() instanceof User ||
            $this->security->isGranted(UserRolesEnum::ROLE_ROOT);
    }
}