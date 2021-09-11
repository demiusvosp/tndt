<?php
/**
 * User: demius
 * Date: 10.09.2021
 * Time: 0:32
 */
declare(strict_types=1);

namespace App\Security\Voter;

use App\Security\Hierarchy\HierarchyHelper;
use App\Security\UserPermissionsEnum;
use App\Security\UserRolesEnum;
use App\Service\ProjectManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Избиратель для текущего проекта. Работает только для запросов имеющих currentProject (т.е. тех, в которые был
 * передан проект, например страниц внутри проекта)
 */
class CurrentProjectVoter implements VoterInterface
{
    private HierarchyHelper $hierarchyHelper;
    private ProjectManager $projectManager;

    public function __construct(
        HierarchyHelper $hierarchyHelper,
        ProjectManager $projectManager
    ) {
        $this->hierarchyHelper = $hierarchyHelper;
        $this->projectManager = $projectManager;
    }

    public function vote(TokenInterface $token, $subject, array $attributes): int
    {
        if(!$this->projectManager->isProjectContext()) {
            // способны обработать только web-страницы с переданным project
            return VoterInterface::ACCESS_ABSTAIN;
        }

        foreach ($token->getRoleNames() as $fullRoleName) {
            if (UserRolesEnum::isValid($fullRoleName)) {
                // это глобальная роль, пусть с ней RoleVoter разбирается
                continue;
            }
            [$role, $project] = UserRolesEnum::explodeSyntheticRole($fullRoleName);
            if (empty($role) || empty($project)) {
                // это не роль в проекте
                continue;
            }
            if ($project !== $this->projectManager->getProject()->getSuffix()) {
                // роль не этого проекта
                continue;
            }
            if (!UserRolesEnum::isValid($role)) {
                // неизвестная роль
                throw new \DomainException('Unknown '.$role.' role');
            }

            foreach ($attributes as $attribute) {
                if ($attribute === UserRolesEnum::ROLE_ROOT) {
                    // действия требующие root пусть RootVoter разбирает
                    return VoterInterface::ACCESS_ABSTAIN;
                }
                if(UserPermissionsEnum::isValid($attribute)) {

                    if($this->hierarchyHelper->has($attribute, $role)) {
                        return VoterInterface::ACCESS_GRANTED;
                    }
                }
            }
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}