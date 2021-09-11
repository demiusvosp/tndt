<?php
/**
 * User: demius
 * Date: 10.09.2021
 * Time: 0:39
 */
declare(strict_types=1);

namespace App\Security\Voter;

use App\Security\UserRolesEnum;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;


/**
 * Избиратель root, дает ему любое существующее в системе право
 */
class RootVoter implements VoterInterface
{
    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        foreach ($token->getRoleNames() as $roleName) {
            if (UserRolesEnum::ROLE_ROOT === $roleName) {
                return VoterInterface::ACCESS_GRANTED;
            }
        }
        return VoterInterface::ACCESS_ABSTAIN;
    }
}