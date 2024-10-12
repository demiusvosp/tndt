<?php
/**
 * User: demius
 * Date: 06.05.2023
 * Time: 19:24
 */
declare(strict_types=1);

namespace App\Security\Voter;

use App\Model\Enum\Security\UserPermissionsEnum;
use App\Model\Enum\Security\UserRolesEnum;
use App\Security\Hierarchy\HierarchyHelper;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;


/**
 * Избиратель проверяющий доступ согласно глобальным ролям
 */
class GlobalRolesVoter implements VoterInterface, LoggerAwareInterface
{
    private HierarchyHelper $hierarchyHelper;
    private LoggerInterface $securityLogger;

    public function __construct(HierarchyHelper $hierarchyHelper) {
        $this->hierarchyHelper = $hierarchyHelper;
    }

    public function setLogger(LoggerInterface $securityLogger): void
    {
        $this->securityLogger = $securityLogger;
    }

    public function vote(TokenInterface $token, $subject, array $attributes): int
    {
        foreach ($token->getRoleNames() as $roleName) {
            if (UserRolesEnum::ROLE_ROOT === $roleName) {
                return VoterInterface::ACCESS_GRANTED;
            }
            if (UserRolesEnum::isProjectRole($roleName) || UserPermissionsEnum::isProjectRole($roleName)) {
                // project role, - skip
                // $this->securityLogger->debug('{role} is not global role - skip', ['role' => $roleName]);
                continue;
            }
            if (!UserRolesEnum::isValid($roleName) && !UserPermissionsEnum::isValid($roleName)) {
                // not valid role
                $this->securityLogger->warning(
                    '{role} is invalid role - skip',
                    ['role' => $roleName, 'user' => $token->getUserIdentifier()]
                );
                continue;
            }

            foreach ($attributes as $attribute) {
                if ($this->hierarchyHelper->has($attribute, $roleName)) {
                    $this->securityLogger->debug(
                        'Global {role} by granted {attribute} - grant to {user}',
                        ['attribute' => $attribute, 'role' => $roleName, 'user' => $token->getUserIdentifier()]
                    );
                    return VoterInterface::ACCESS_GRANTED;
                }
            }
        }
        return VoterInterface::ACCESS_ABSTAIN;
    }
}